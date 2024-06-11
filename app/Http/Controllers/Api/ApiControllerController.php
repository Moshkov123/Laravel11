<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Description;
use App\Models\Feature;
use App\Models\Product;
use App\Models\Product_Categorie;
use App\Models\Product_Feature;
use Illuminate\Http\Request;

class ApiControllerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $products = Product::with(['photo', 'productCategory', 'productFeature'])->get()->toArray();

        foreach ($products as &$product) {
            foreach ($product['product_category'] as &$category) {
                $category['categorie'] = Category::find($category['categorie_id'])->categorie;
            }

            foreach ($product['product_feature'] as &$feature) {

                $feature['parameter'] = Feature::find($feature['feature_id'])->parameter;

                $description = Description::where('feature_id', $feature['feature_id'])->first();
                $feature['description'] = $description ? $description->description : null;
            }
        }

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['photo', 'productCategory', 'productFeature'])->findOrFail($id);
    
        foreach ($product->productCategory as &$category) {
            $category->categorie = Category::find($category->categorie_id)->categorie;
            $product->makeHidden(['created_at', 'updated_at']);
        }
    
        foreach ($product->productFeature as &$feature) {
            $feature->parameter = Feature::find($feature->feature_id)->parameter;
    
            $description = Description::where('feature_id', $feature->feature_id)->first();
            $feature->description = $description ? $description->description : null;
            $feature->makeHidden(['created_at', 'updated_at']);
        }
        $product->makeHidden(['created_at', 'updated_at']);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
