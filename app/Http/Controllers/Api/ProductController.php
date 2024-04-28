<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\updateProductRequest;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function getOne(string $id) {
        return Product::with('inventories', 'files')->findOrFail($id);
    }

    public function getList() {
        return Product::with('inventories', 'files')->get();
    }

    public function create(CreateProductRequest $request)
    {
        $payload = $request->validated();

        $product = DB::transaction(function () use ($payload) {
            $product = Product::create($payload);

            foreach ($payload['files'] as $uploadFile) {
                $image = $uploadFile['image'];
                $fileName = $image->getClientOriginalName();

                Storage::put($fileName, $image);

                $product->files()->create([
                    'filename' => $fileName,
                    'thumbnail' => $uploadFile['thumbnail']
                ]);
            }

            foreach ($payload['inventories'] as $inventory) {
                $product->inventories()->create($inventory);
            }

            return $product;
        });

        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function update(updateProductRequest $request, string $id)
    {
        $payload = $request->validated();

        $product = DB::transaction(function () use ($payload, $id) {
            $product = Product::findOrFail($id);

            foreach ($payload['files'] as $uploadFile) {
                $image = $uploadFile['image'];
                $fileName = $image->getClientOriginalName();
                $existFile = file_exists(storage_path('app/'.$fileName));

                if (!$existFile) {
                    Storage::put($fileName, $image);

                    $product->files()->create([
                        'filename' => $fileName,
                        'thumbnail' => $uploadFile['thumbnail']
                    ]);
                }
            }

            foreach ($payload['inventories'] as $inventory) {
                if ($product->inventories()->find($inventory['id'])) {
                    $product->inventories()->update($inventory);
                } else{
                    $product->inventories()->create($inventory);
                }
            }

            return $product;
        });



        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function delete(Request $request, string $id) {
        $product = Product::with('inventories', 'files')->findOrFail($id);

        DB::transaction(function () use ($product) {
            $product->inventories()->delete();
            $product->files()->delete();
            $product->delete();
        });

        return response()->json([], 200);
    }
}
