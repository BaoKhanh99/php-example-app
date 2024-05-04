<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService
{
  public function getList()
  {
    return Product::with('inventories', 'files')->get();
  }

  public function getOne(string $id)
  {
    return Product::with('inventories', 'files')->findOrFail($id);
  }

  public function store($payload)
  {
    $product = DB::transaction(function () use ($payload) {
      $product = Product::create($payload);

      foreach ($payload['files'] as $uploadFile) {
        $image = $uploadFile['image'];

        if ($image instanceof UploadedFile) {
          $fileName = $image->getClientOriginalName();

          Storage::put($fileName, $image);

          $product->files()->create([
            'filename' => $fileName,
            'thumbnail' => $uploadFile['thumbnail']
          ]);
        }
      }

      foreach ($payload['inventories'] as $inventory) {
        $product->inventories()->create($inventory);
      }

      return $product;
    });

    return $product;
  }

  public function update(int $id, $payload)
  {
    $product = DB::transaction(function () use ($payload, $id) {
      $product = Product::findOrFail($id);

      foreach ($payload['files'] as $uploadFile) {
        $image = $uploadFile['image'];

        if ($image instanceof UploadedFile) {
          $fileName = $image->getClientOriginalName();
          $existFile = Storage::exists($fileName);

          if (!$existFile) {
            Storage::put($fileName, $image);

            $product->files()->create([
              'filename' => $fileName,
              'thumbnail' => $uploadFile['thumbnail']
            ]);
          }
        }
      }

      foreach ($payload['inventories'] as $inventory) {
        $product->inventories()->updateOrCreate($inventory);
      }

      return $product;
    });

    return $product;
  }

  public function delete(int $id)
  {
    $product = Product::with('inventories', 'files')->findOrFail($id);

    DB::transaction(function () use ($product) {
      $product->inventories()->delete();
      $product->files()->delete();
      $product->delete();
    });
  }
}
