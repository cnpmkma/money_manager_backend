<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $wallet_ids = $request->user()->wallets()->pluck('id');

    // Nếu có wallet_id query param, chỉ lấy transactions của ví đó
    if ($request->has('wallet_id')) {
        $walletId = $request->query('wallet_id');
        // kiểm tra xem wallet đó có thuộc user không
        if (!$wallet_ids->contains($walletId)) {
            return response()->json(['error' => 'Wallet không hợp lệ'], 403);
        }
        $transactions = Transaction::with('category')
            ->where('wallet_id', $walletId)
            ->orderBy('created_at', 'desc')
            ->get();
    } else {
        $transactions = Transaction::with('category')
            ->whereIn('wallet_id', $wallet_ids)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    return response()->json($transactions);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => ['required', 'numeric'],
                'note' => ['nullable', 'string'],
                'wallet_id' => ['required', 'exists:wallets,id'],
                'category_id' => ['required', 'exists:categories,id'],
                'transaction_date' => ['required', 'date']
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // check quyền sở hữu ví
        if (! $request->user()->wallets()->where('id', $validated['wallet_id'])->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaction = Transaction::create($validated);

        return response()->json($transaction->load('category'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction, Request $request)
    {
        if ($transaction->wallet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($transaction->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->wallet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'amount' => ['required', 'numeric'],
                'note' => ['nullable', 'string'],
                'wallet_id' => ['required', 'exists:wallets,id'],
                'category_id' => ['required', 'exists:categories,id'],
                'transaction_date' => ['required', 'date']
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // check ví có thuộc user không
        if (! $request->user()->wallets()->where('id', $validated['wallet_id'])->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaction->update($validated);

        return response()->json($transaction->load('category'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction, Request $request)
    {
        if ($transaction->wallet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaction->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
