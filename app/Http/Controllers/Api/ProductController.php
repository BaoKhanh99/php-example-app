<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\updateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}


    public function getOne(string $id) {
        return $this->productService->getOne($id);
    }

    public function getList() {
        return $this->productService->getList();
    }

    public function create(CreateProductRequest $request)
    {
        $payload = $request->validated();

        $product = $this->productService->store($payload);

        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function update(updateProductRequest $request, int $id)
    {
        $payload = $request->validated();

        $product = $this->productService->update($id, $payload);

        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function delete(Request $request, string $id) {
        $this->productService->delete($id);

        return response()->json([], 200);
    }
}
