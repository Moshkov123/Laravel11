<?php

namespace TCG\Voyager\Http\Controllers;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Database\DatabaseUpdater;
use TCG\Voyager\Database\Schema\Column;
use TCG\Voyager\Database\Schema\Identifier;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Events\TableAdded;
use TCG\Voyager\Events\TableDeleted;
use TCG\Voyager\Events\TableUpdated;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Schema;
use stdClass;


class VoyagerDatabaseController extends Controller
{
    public function index()
    {
        // Извлекает все типы данных из модели типов данных в Voyager
        $dataTypes = Voyager::model('DataType')->select('id', 'name', 'slug')->get()->keyBy('name')->toArray();
    
        // Получить все таблицы из подключения к базе данных с помощью конструктора запросов
        $tables = DB::table('information_schema.tables')
            ->where('table_schema', '=', DB::connection()->getDatabaseName())
            ->pluck('table_name')
            ->toArray();

        // Обработка каждой таблицы и добавьте префикс, slug и идентификатор типа данных, если это возможно
        $processedTables = array_map(function ($table) use ($dataTypes) {
            
            $tableName = Str::replaceFirst(DB::getTablePrefix(), '', $table);
    
            return (object) [
                'prefix'     => DB::getTablePrefix(),
                'name'       => $tableName,
                'slug'       => $dataTypes[$tableName]['slug'] ?? null,
                'dataTypeId' => $dataTypes[$tableName]['id'] ?? null,
            ];
        }, $tables);
       
    
        return Voyager::view('voyager::tools.database.index')->with(compact('processedTables'));
    }

    /**
     * Create database table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $db = $this->prepareDbManager('create');
    
        return Voyager::view('voyager::tools.database.edit-add', compact('db'));
    }
    

    /**
     * Store new database table.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $conn = 'database.connections.'.config('database.default');
            Type::registerCustomPlatformTypes();

            $table = $request->table;
            if (!is_array($request->table)) {
                $table = json_decode($request->table, true);
            }
            $table['options']['collate'] = config($conn.'.collation', 'utf8mb4_unicode_ci');
            $table['options']['charset'] = config($conn.'.charset', 'utf8mb4');
            $table = Table::make($table);
            SchemaManager::createTable($table);

            if (isset($request->create_model) && $request->create_model == 'on') {
                $modelNamespace = config('voyager.models.namespace', app()->getNamespace());
                $params = [
                    'name' => $modelNamespace.Str::studly(Str::singular($table->name)),
                ];

                // if (in_array('deleted_at', $request->input('field.*'))) {
                //     $params['--softdelete'] = true;
                // }

                if (isset($request->create_migration) && $request->create_migration == 'on') {
                    $params['--migration'] = true;
                }

                Artisan::call('voyager:make:model', $params);
            } elseif (isset($request->create_migration) && $request->create_migration == 'on') {
                Artisan::call('make:migration', [
                    'name'    => 'create_'.$table->name.'_table',
                    '--table' => $table->name,
                ]);
            }

            event(new TableAdded($table));

            return redirect()
               ->route('voyager.database.index')
               ->with($this->alertSuccess(__('voyager::database.success_create_table', ['table' => $table->name])));
        } catch (Exception $e) {
            return back()->with($this->alertException($e))->withInput();
        }
    }

    /**
     * Edit database table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($table)
    {
        if (!Schema::hasTable($table)) {
            return redirect()
                ->route('voyager.database.index')
                ->with($this->alertError(__('voyager::database.edit_table_not_exist')));
        }
    
        $db = $this->prepareDbManager('update', $table);
        
        return Voyager::view('voyager::tools.database.edit-add', compact('db'));
    }
    /**
     * Update database table.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
       

        $table = json_decode($request->table, true);

        try {
            DatabaseUpdater::update($table);
            // TODO: synch BREAD with Table
            // $this->cleanOldAndCreateNew($request->original_name, $request->name);
            event(new TableUpdated($table));
        } catch (Exception $e) {
            return back()->with($this->alertException($e))->withInput();
        }

        return redirect()
               ->route('voyager.database.index')
               ->with($this->alertSuccess(__('voyager::database.success_create_table', ['table' => $table['name']])));
    }
    public function prepareDbManager($action, $tableName = '')
    {
        $db = new stdClass();
    
        // Get the platform types
        $db->platformTypes = Type::getPlatformTypes();
    
        if ($action === 'update') {
            if (!Schema::hasTable($tableName)) {
                return $db;
            }
    
            // Get the columns
            $db->columns = Schema::getColumnListing($tableName);
    
            // Set the form action for updating
            $db->formAction = route('voyager.database.update', $tableName);
    
            // Set the table name
            $db->table = $tableName;
        } else {
            // Create a new table schema using Blueprint
            $tableSchema = function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            };
    
            // Set the form action for creating a new table
            $db->formAction = route('voyager.database.store');
        }
    
        $oldTable = old('table');
        $db->oldTable = $oldTable;
        $db->action = $action;
        $db->identifierRegex = Identifier::REGEX;
        $db->platform = Schema::getConnection()->getDatabaseName();
        $db->tableJson = json_encode($oldTable);
    
        return $db;
    }
    
    public function cleanOldAndCreateNew($originalName, $tableName)
    {
        if (!empty($originalName) && $originalName != $tableName) {
            $dt = DB::table('data_types')->where('name', $originalName);
            if ($dt->get()) {
                $dt->delete();
            }

            $perm = DB::table('permissions')->where('table_name', $originalName);
            if ($perm->get()) {
                $perm->delete();
            }

            $params = ['name' => Str::studly(Str::singular($tableName))];
            Artisan::call('voyager:make:model', $params);
        }
    }

    public function reorder_column(Request $request)
    {
       

        if ($request->ajax()) {
            $table = $request->table;
            $column = $request->column;
            $after = $request->after;
            if ($after == null) {
                // SET COLUMN TO THE TOP
                DB::query("ALTER $table MyTable CHANGE COLUMN $column FIRST");
            }

            return 1;
        }

        return 0;
    }

    /**
     * Show table.
     *
     * @param string $table
     *
     * @return JSON
     */
    public function show($table)
{
    $additional_attributes = [];
    $model_name = Voyager::model('DataType')->where('name', $table)->pluck('model_name')->first();
    if (isset($model_name)) {
        $model = app($model_name);
        if (isset($model->additional_attributes)) {
            foreach ($model->additional_attributes as $attribute) {
                $additional_attributes[$attribute] = [];
            }
        }
    }

    // Use Laravel's Schema facade to get the column information
    $columns = Schema::getColumnListing($table);
    $tableSchema = [];

    foreach ($columns as $column) {
        $tableSchema[$column] = [
            'type' => Schema::getColumnType($table, $column),
            'nullable' => Schema::hasColumn($table, $column . ' NULL'),
            // Add more attributes as needed
        ];
    }

    return response()->json(collect($tableSchema)->merge($additional_attributes));
}

    /**
     * Destroy table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($table)
    {
        try {
            // Use Laravel's Schema facade to drop the table
            Schema::dropIfExists($table);
    
            event(new TableDeleted($table));
    
            return redirect()
                ->route('voyager.database.index')
                ->with($this->alertSuccess(__('voyager::database.success_delete_table', ['table' => $table])));
        } catch (Exception $e) {
            return back()->with($this->alertException($e));
        }
    }
}