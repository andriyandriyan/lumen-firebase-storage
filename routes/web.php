<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Upload gambar ke Firebase Storage
$router->post('/images', function (Request $request) use ($router) {
    $this->validate($request, [
        'image' => 'required|image',
    ]);
    $file = $request->file('image');
    $name = time() . '-' . $file->getClientOriginalName();
    $filePath = 'images/' . $name;
    Storage::disk('gcs')->put($filePath, file_get_contents($file));
    return response()->json([
        'message' => 'Gambar berhasil diupload'
    ]);
});

// Menampilkan semua gambar
$router->get('/images', function () use ($router) {
    $images = [];
    $files = Storage::disk('gcs')->files('images');
    foreach ($files as $file) {
        $images[] = [
            'name' => str_replace('images/', '', $file),
            'src'  => Storage::disk('gcs')->url($file),
        ];
    }
    return response()->json($images);
});

// Menghapus gambar
$router->post('/delete-images', function (Request $request) use ($router) {
    $this->validate($request, [
        'image' => 'required',
    ]);
    $image = $request->input('image');
    Storage::disk('gcs')->delete('images/' . $image);
    return response()->json([
        'message' => 'Gambar berhasil dihapus '
    ]);
});