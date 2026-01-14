<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coffee;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $this->getActiveCart($request->user());
        $cart->load('items.coffee');

        return response()->json($this->cartResponse($cart));
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'coffee_id' => ['required', 'exists:coffees,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $coffee = Coffee::where('id', $data['coffee_id'])
            ->where('is_active', true)
            ->first();

        if (! $coffee) {
            return response()->json(['message' => 'Recurso no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $cart = $this->getActiveCart($request->user());
        $cart->load('items');

        $existingItem = $cart->items->firstWhere('coffee_id', $coffee->id);
        $newQuantity = $existingItem ? $existingItem->quantity + $data['quantity'] : $data['quantity'];

        if ($newQuantity > $coffee->stock) {
            return response()->json([
                'message' => 'Stock insuficiente',
                'errors' => [
                    'quantity' => ["Solo hay {$coffee->stock} unidades disponibles."],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $unitPrice = $coffee->price;
        $totalPrice = $this->calculateSubtotal($unitPrice, $newQuantity);

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);
        } else {
            $cart->items()->create([
                'coffee_id' => $coffee->id,
                'quantity' => $newQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);
        }

        $cart->load('items.coffee');

        return response()->json($this->cartResponse($cart), Response::HTTP_CREATED);
    }

    public function updateItem(Request $request, CartItem $item): JsonResponse
    {
        if ($response = $this->validateCartItemOwnership($item, $request)) {
            return $response;
        }

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $coffee = Coffee::where('id', $item->coffee_id)
            ->where('is_active', true)
            ->first();

        if (! $coffee) {
            return response()->json(['message' => 'Recurso no encontrado'], Response::HTTP_NOT_FOUND);
        }

        if ($data['quantity'] > $coffee->stock) {
            return response()->json([
                'message' => 'Stock insuficiente',
                'errors' => [
                    'quantity' => ["Solo hay {$coffee->stock} unidades disponibles."],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $item->update([
            'quantity' => $data['quantity'],
            'unit_price' => $coffee->price,
            'total_price' => $this->calculateSubtotal($coffee->price, $data['quantity']),
        ]);

        $cart = $item->cart->fresh('items.coffee');

        return response()->json($this->cartResponse($cart));
    }

    public function removeItem(Request $request, CartItem $item): JsonResponse
    {
        if ($response = $this->validateCartItemOwnership($item, $request)) {
            return $response;
        }

        $cart = $item->cart;
        $item->delete();
        $cart->refresh()->load('items.coffee');

        return response()->json($this->cartResponse($cart));
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getActiveCart($request->user());
        $cart->items()->delete();
        $cart->load('items.coffee');

        return response()->json($this->cartResponse($cart));
    }

    private function getActiveCart(User $user): ShoppingCart
    {
        return ShoppingCart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['status' => 'active']
        );
    }

    private function validateCartItemOwnership(CartItem $item, Request $request): ?JsonResponse
    {
        $cart = $item->cart()->with('user')->first();

        if (! $cart || $cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], Response::HTTP_FORBIDDEN);
        }

        if ($cart->status !== 'active') {
            return response()->json(['message' => 'El carrito no está activo'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return null;
    }

    private function calculateSubtotal($unitPrice, int $quantity): string
    {
        return number_format((float) $unitPrice * $quantity, 2, '.', '');
    }

    private function cartResponse(ShoppingCart $cart): array
    {
        $items = $cart->items ?? collect();
        $items->load('coffee');
        $total = $items->sum(fn ($item) => (float) $item->total_price);

        return [
            'cart' => [
                'id' => $cart->id,
                'status' => $cart->status,
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'coffee' => $item->coffee,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ];
                })->values(),
                'total_cart' => number_format($total, 2, '.', ''),
            ],
        ];
    }
}
