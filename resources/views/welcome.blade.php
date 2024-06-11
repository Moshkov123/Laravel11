<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
</head>
<body>
    <h1>All Products</h1>
    <ul>
        @foreach($products as $product)
            <li>
                <h2>{{ $product->art_number}}</h2>
                <div>
                    @foreach($product->Photo as $photo)
                        <img src="{{ $photo->photo }}" alt="{{ $product->art_number }}" width="100">
                    @endforeach
                </div>
                <h3>Categories</h3>
                <ul>
                    @foreach($product->Product_Categorie as $productCategory)
                        <li>{{ $productCategory->categorie_id }}</li>
                    @endforeach
                </ul>
                <h3>Features</h3>
                <ul>
                    @foreach($product->Product_Feature as $productFeature)
                        <li>{{ $productFeature->feature_id }}</li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</body>
</html>