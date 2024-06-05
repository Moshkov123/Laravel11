<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Description;
use App\Models\Feature;
use App\Models\Photo;
use App\Models\Product;
use App\Models\Product_Categorie;
use App\Models\Product_Feature;
use App\Models\Xml;
use Illuminate\Http\Request;
use SimpleXMLElement;

class XmlController extends Controller
{
    public function  index(){
        return view("forma");
    }

    
    public function upload(Request $request)
    {
        if ($request->hasFile('xmlFile')) {
            $file = $request->file('xmlFile');
            $xmlContent = $file->get();
    
            $xml = new SimpleXMLElement($xmlContent);
            $offer = $xml->offers->offer;
            
        
            // Extract data
            $artNumber = (string) $offer->art_number;
            $markId = (string) $offer->mark_id;
            $description = (string) $offer->com_description;
            $price = (string) $offer->price;
            $price = str_replace(' ', '', $price); // Remove the space from the price
            $quantity = (int) $offer->quantity;
            $complectation = (string) $offer->complectation;
            $options = (string) $offer->options;
            $properties = $offer->properties->item;

            
        // Сохранение данных о свойствах продукта
        

            $images = [];
            foreach ($offer->images->image as $image) {
                $images[] = (string) $image;
            }
    
            // Save data to database
            $product = new Product([
                'art_number' => $artNumber,
                'mark' => $markId,
                'description' => $description,
                'price' => $price,
                'quantity' => $quantity,
                'complectation' => $complectation,
                'options' => $options,
            ]);
            $product->save();

            $categories = [];
        foreach ($offer->children() as $element) {
            if (strpos($element->getName(), 'folder_') === 0) {
                $categoryName = (string) $element;
                if (!empty($categoryName)) {
                    $category = Category::firstOrCreate(['categorie' => $categoryName]);
                    $categories[] = $category->id;
                }
            }
        }


        
        foreach ($properties as $property) {
            $name = (string) $property->name;
            $value = (string) $property->value;

            // Создание или получение существующего свойства
            $feature = Feature::firstOrCreate(['parameter' => $name]);

            $productFeature = new Product_Feature([
                'product_id' => $product->id,
                'feature_id' => $feature->id,
            ]);
            $productFeature->save();

            // Сохранение описания свойства
            $description = new Description([
                'description' => $value,
                'feature_id' => $feature->id
            ]);
            $description->save();
        }
        

        // Save product-category relationships
        foreach ($categories as $categoryId) {
            $productCategory = new Product_Categorie([
                'product_id' => $product->id,
                'categorie_id' => $categoryId,
            ]);
            $productCategory->save();
        }

            foreach ($images as $imageUrl) {
            $photo = new Photo(['photo' => $imageUrl,'product_id'=>$product->id ]);
            $photo->save();
        }

            // Retrieve and return JSON data
            $data = Product::where('art_number', $artNumber)->first();
            return response()->json($data);
        }
       

    
        return back()->with('error', 'Failed to upload file');
    }

    public function show($id)
    {
    $product = Product::findOrFail($id);

    $photos = $product->photo;

    $productCategories = Product_Categorie::where('product_id', $product->id)->get();

    $categories = [];
    foreach ($productCategories as $productCategory) {
        $category = Category::find($productCategory->categorie_id);
        if ($category) {
            $categories[] = $category;
        }
    }
    
    return view('show', compact('product', 'photos', 'categories'));
    }
}
