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

    /**
     * Devuelve información de productos incluyendo stock disponible
     */
    public function productsInfo(Request $request)
    {
        $ids = $request->input('ids', []);

        $products = Product::with(['variants:id,product_id,stock,atributo_tipo,nombre_color,nombre_tono'])
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'slug', 'price', 'image', 'stock']);

        // Transformar la respuesta para que Alpine la entienda mejor
        $transformed = $products->map(function ($product) {
            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'image' => $product->image,
                'stock' => $product->stock,
            ];

            // Solo incluir variantes si existen
            if ($product->variants && $product->variants->isNotEmpty()) {
                $data['variants'] = $product->variants->mapWithKeys(function ($variant) {
                    // Para mostrar en la interfaz, podemos usar el nombre del color/tono como nombre de la variante
                    $variantName = '';
                    if ($variant->atributo_tipo === 'color' && !empty($variant->nombre_color)) {
                        $variantName = $variant->nombre_color;
                    } elseif ($variant->atributo_tipo === 'tono' && !empty($variant->nombre_tono)) {
                        $variantName = $variant->nombre_tono;
                    } elseif ($variant->atributo_tipo === 'ninguno') {
                        $variantName = 'Sin atributo';
                    }

                    return [$variant->id => [
                        'stock' => $variant->stock,
                        'atributo_tipo' => $variant->atributo_tipo,
                        'nombre' => $variantName
                    ]];
                });
            }

            return $data;
        });

        return response()->json($transformed);
    }

    /**
     * Valida el stock de los productos en el carrito
     * Mejorado para proveer información más detallada
     */
    public function validateStock(Request $request)
    {
        try {
            $items = $request->input('items', []);
            $errors = [];

            foreach ($items as $item) {
                $product = Product::with(['variants' => function ($query) {
                    $query->select('id', 'product_id', 'stock', 'atributo_tipo');
                }])->find($item['id']);

                if (!$product) {
                    $errors[] = [
                        'id' => $item['id'],
                        'error' => 'Producto no encontrado',
                        'available_stock' => 0,
                        'requested_quantity' => $item['quantity']
                    ];
                    continue;
                }

                // Verificar si el item tiene variante especificada
                if (!empty($item['variant_id'])) {
                    // Producto CON variante específica
                    $variant = $product->variants->where('id', $item['variant_id'])->first();

                    if (!$variant) {
                        $errors[] = [
                            'id' => $item['id'],
                            'variant_id' => $item['variant_id'],
                            'error' => 'Variante no encontrada',
                            'available_stock' => 0,
                            'requested_quantity' => $item['quantity']
                        ];
                    } else if ($variant->stock < $item['quantity']) {
                        $errors[] = [
                            'id' => $item['id'],
                            'variant_id' => $item['variant_id'],
                            'error' => 'Stock insuficiente en variante',
                            'available_stock' => $variant->stock,
                            'requested_quantity' => $item['quantity']
                        ];
                    }
                } else {
                    // Producto SIN variante específica

                    // Primero verificamos si existe una variante "sin atributo"
                    $noAttributeVariant = $product->variants->where('atributo_tipo', 'ninguno')->first();

                    if ($noAttributeVariant) {
                        // Verificar stock de la variante "sin atributo"
                        if ($noAttributeVariant->stock < $item['quantity']) {
                            $errors[] = [
                                'id' => $item['id'],
                                'variant_id' => $noAttributeVariant->id,
                                'error' => 'Stock insuficiente en variante sin atributo',
                                'available_stock' => $noAttributeVariant->stock,
                                'requested_quantity' => $item['quantity']
                            ];
                        }
                    } else {
                        // No hay variante "sin atributo", verificar stock general del producto
                        $stock = $product->stock;

                        if ($stock < $item['quantity']) {
                            $errors[] = [
                                'id' => $item['id'],
                                'error' => 'Stock insuficiente',
                                'available_stock' => $stock,
                                'requested_quantity' => $item['quantity']
                            ];
                        }
                    }
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'valid' => false,
                    'errors' => $errors
                ], 422);
            }

            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            \Log::error('Error en validación de stock: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
