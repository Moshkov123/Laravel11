<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
</head>
<body>
    
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">{{ $product->mark }}</h1>

        <!-- Display product details -->
        <p><strong>Art Number:</strong> {{ $product->art_number }}</p>
        <p><strong>Description:</strong> {{ $product->description }}</p>
        <p><strong>Price:</strong> {{ $product->price }}</p>
        <p><strong>Quantity:</strong> {{ $product->quantity }}</p>

        <!-- Display categories -->
        <h2 class="text-xl font-bold mb-2">Categories</h2>
        @foreach($categories as $category)
            <p>{{ $category->categorie }}</p>
        @endforeach

        <!-- Display photos -->
        <h2 class="text-xl font-bold mb-2">Photos</h2>
        @foreach($photos as $photo)
            <img src="{{ $photo->photo }}" alt="Product Photo" class="mb-2">
        @endforeach

        <!-- Display features -->
        <h2 class="text-xl font-bold mb-2">Features</h2>
        @foreach($features as $feature)
            <p><strong>{{ $feature['name'] }}:</strong> {{ $feature['value'] }}</p>
        @endforeach
    </div>
</body>
</html>