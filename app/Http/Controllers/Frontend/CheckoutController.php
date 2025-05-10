<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Muestra el formulario de checkout
     */
    public function index()
    {
        return view('checkout.index');
    }

    /**
     * Almacena una nueva orden y redirecciona a WhatsApp
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'document' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'cart_items' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Decodificar los items del carrito
        $cartItems = json_decode($request->cart_items, true);

        // Verificar que haya items en el carrito
        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío'
            ], 422);
        }

        // Verificar stock antes de crear la orden
        foreach ($cartItems as $item) {
            $productId = $item['id'];
            $variantId = $item['variant_id'] ?? null;
            $quantity = $item['quantity'];

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if (!$variant || $variant->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "No hay suficiente stock para {$item['name']}"
                    ], 422);
                }
            } else {
                $product = Product::find($productId);
                if (!$product || $product->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "No hay suficiente stock para {$item['name']}"
                    ], 422);
                }
            }
        }

        // Iniciar transacción para asegurar que todo se guarde correctamente
        DB::beginTransaction();

        try {
            // Crear la orden
            $order = Order::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'document' => $request->document,
                'address' => $request->address,
                'status' => 'pendiente',
                'total' => 0, // Se actualizará después de agregar los items
            ]);

            $total = 0;
            $itemsDetail = [];

            // Crear los items de la orden y actualizar stock
            foreach ($cartItems as $item) {
                $productId = $item['id'];
                $variantId = $item['variant_id'] ?? null;
                $quantity = $item['quantity'];
                $price = $item['price'];
                $itemTotal = $price * $quantity;
                $total += $itemTotal;

                // Guardar detalles para el mensaje de WhatsApp
                $variantText = '';
                if ($variantId && isset($item['variant_name'])) {
                    $variantText = " - " . $item['variant_name'];
                }

                $itemsDetail[] = "{$quantity}x {$item['name']}{$variantText}: $" . number_format($itemTotal, 0, ',', '.');

                // Crear el item de orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'name' => $item['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $itemTotal,
                ]);

                // Actualizar el stock
                if ($variantId) {
                    $variant = ProductVariant::find($variantId);
                    $variant->stock -= $quantity;
                    $variant->save();
                } else {
                    $product = Product::find($productId);
                    $product->stock -= $quantity;
                    $product->save();
                }
            }

            // Actualizar el total de la orden
            $order->update(['total' => $total]);

            // Confirmar transacción
            DB::commit();

            // Construir mensaje para WhatsApp
            $message = "🛍️ *NUEVA ORDEN #{$order->id}* 🛍️\n\n";
            $message .= "*Cliente:* {$request->name}\n";
            $message .= "*Teléfono:* {$request->phone}\n";
            $message .= "*Documento:* {$request->document}\n";
            $message .= "*Dirección:* {$request->address}\n\n";

            $message .= "*PRODUCTOS:*\n";
            $message .= implode("\n", $itemsDetail);
            $message .= "\n\n*TOTAL: $" . number_format($total, 0, ',', '.') . "*";

            // Número de WhatsApp al que se enviará el mensaje (incluir código de país)
            $whatsappNumber = "573011434336"; // Reemplaza con tu número real

            // Crear URL de WhatsApp con el mensaje
            $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

            // Retornar respuesta exitosa con URL de WhatsApp
            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'order_id' => $order->id,
                'whatsapp_url' => $whatsappUrl
            ]);
        } catch (\Exception $e) {
            // Si algo falla, revertir transacción
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la página de confirmación de la orden
     */
    public function confirmation($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        return view('checkout.confirmation', compact('order'));
    }
}
