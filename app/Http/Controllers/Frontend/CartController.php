<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function productsInfo(Request $request)
    {
        $ids = $request->input('ids', []);
        $products = Product::with('variants')
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'slug', 'price', 'image', 'stock']);

        return response()->json($products);
    }

    public function validateStock(Request $request)
    {
        $items = $request->input('items', []);
        $errors = [];

        foreach ($items as $item) {
            $product = Product::with('variants')->find($item['id']);

            if (!$product) {
                $errors[] = ['id' => $item['id'], 'error' => 'Producto no encontrado'];
                continue;
            }

            if (isset($item['variant_id'])) {
                $variant = $product->variants()->find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    $errors[] = ['id' => $item['id'], 'variant_id' => $item['variant_id'], 'error' => 'Stock insuficiente en variante'];
                }
            } else {
                $stock = $product->variants->sum('stock') ?: $product->stock;
                if ($stock < $item['quantity']) {
                    $errors[] = ['id' => $item['id'], 'error' => 'Stock insuficiente'];
                }
            }
        }

        if (!empty($errors)) {
            return response()->json(['valid' => false, 'errors' => $errors], 422);
        }

        return response()->json(['valid' => true]);
    }
}
