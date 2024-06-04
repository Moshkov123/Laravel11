<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload XML File</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-8 rounded-md shadow-md">
        <form action="/upload" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="xmlFile" class="block text-gray-700 font-bold mb-2">Select XML File:</label>
                @csrf
                <input type="file" id="xmlFile" name="xmlFile" class="w-full border rounded-md py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500">
            </div>
            <div class="text-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Upload</button>
            </div>
        </form>
    </div>
</body>
</html>
