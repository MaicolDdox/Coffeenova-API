<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coffee;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function checkout(Request $request): JsonResponse
    {
        $user = $request->user();
        $cart = $user->shoppingCarts()->where('status', 'active')->first()
            ?? $user->shoppingCarts()->create(['status' => 'active']);

        $cartItems = $cart->items()->with('coffee')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $order = DB::transaction(function () use ($user, $cart) {
            $items = CartItem::where('shopping_cart_id', $cart->id)->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['El carrito está vacío'],
                ]);
            }

            $orderItemsPayload = [];
            $total = 0;

            foreach ($items as $item) {
                $coffee = Coffee::where('id', $item->coffee_id)->lockForUpdate()->first();

                if (! $coffee || ! $coffee->is_active) {
                    throw ValidationException::withMessages([
                        'coffee' => ['Producto no disponible.'],
                    ]);
                }

                if ($item->quantity > $coffee->stock) {
                    throw ValidationException::withMessages([
                        'stock' => ["Stock insuficiente para {$coffee->name}. Disponible: {$coffee->stock}."],
                    ]);
                }

                $unitPrice = $item->unit_price ?? $coffee->price;
                $subtotal = (float) $this->calculateSubtotal($unitPrice, $item->quantity);

                $orderItemsPayload[] = [
                    'coffee' => $coffee,
                    'item' => $item,
                    'unit_price' => $unitPrice,
                    'subtotal' => $this->calculateSubtotal($unitPrice, $item->quantity),
                ];

                $total += $subtotal;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total' => number_format($total, 2, '.', ''),
                'status' => 'paid',
                'payment_method' => 'simulated',
                'paid_at' => now(),
            ]);

            foreach ($orderItemsPayload as $payload) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'coffee_id' => $payload['coffee']->id,
                    'quantity' => $payload['item']->quantity,
                    'unit_price' => $payload['unit_price'],
                    'subtotal' => $payload['subtotal'],
                ]);

                $payload['coffee']->decrement('stock', $payload['item']->quantity);
            }

            $cart->update(['status' => 'completed']);

            return $order;
        });

        $order->load(['items.coffee', 'user']);

        return response()->json([
            'message' => 'Compra simulada exitosa',
            'data' => [
                'order' => $order,
                'items' => $order->items,
                'user' => $order->user,
            ],
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with('items.coffee')
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], Response::HTTP_FORBIDDEN);
        }

        $order->load('items.coffee');

        return response()->json(['data' => $order]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $query = Order::query()
            ->with(['items.coffee', 'user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function adminShow(Order $order): JsonResponse
    {
        $order->load(['items.coffee', 'user']);

        return response()->json(['data' => $order]);
    }

    private function calculateSubtotal($unitPrice, int $quantity): string
    {
        return number_format((float) $unitPrice * $quantity, 2, '.', '');
    }
}
