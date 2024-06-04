<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Product;
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
        // Find the product by its ID
        $product = Product::findOrFail($id);
    
        // Retrieve all photos associated with the product
        $photos = $product->photo;
    
        // Pass the product and its photos to the view
        return view('show', compact('product', 'photos'));
    }
}
