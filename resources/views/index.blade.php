<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
</head>
<body>
    <h1>All Products</h1>
    @foreach ($products as $product)
        <div class="product">
            <h2>{{ $product['mark'] }}</h2>
            <p>{{ $product['description'] }}</p>
            <p>Price: {{ $product['price'] }}</p>
            <p>Quantity: {{ $product['quantity'] }}</p>
            <h2 class="text-xl font-bold mb-2">Photos</h2>

        @foreach($product['photo'] as $photo)
            <img src="{{ $photo['photo'] }}" alt="Product Photo" class="mb-2">
        @endforeach

            <h3>Categories</h3>
            @foreach ($product['product_category'] as $category)
                <p>{{ $category['categorie'] }}</p>
            @endforeach

            <h3>Features</h3>
            @foreach ($product['product_feature'] as $feature)
                <p>{{ $feature['parameter'] }}</p>
                <p>{{ $feature['description'] }}</p>
            @endforeach
        </div>
    @endforeach
</body>
</html>