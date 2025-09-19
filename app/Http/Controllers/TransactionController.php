<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // Lấy danh sách ID của các ví thuộc người dùng
    $wallet_ids = $request->user()->wallets()->pluck('id');

    if ($request->has('wallet_id')) {
        $walletId = $request->query('wallet_id');

        // Kiểm tra ví có hợp lệ không
        if (!$wallet_ids->contains($walletId)) {
            return response()->json(['error' => 'Wallet không hợp lệ'], 403);
        }

        // Lấy transactions cho ví cụ thể
        $transactions = Transaction::with('category')
            ->where('wallet_id', $walletId)
            ->orderBy('created_at', 'desc')
            ->get();
    } else {
        // Lấy tất cả transactions của người dùng
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

        $wallet = $request->user()->wallets()->find($validated['wallet_id']);
        if (!$wallet) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::transaction(function() use ($validated, $wallet, &$transaction) {
            // Tạo transaction
            $transaction = Transaction::create($validated);

            // Cập nhật số dư ví
            $category = $transaction->category; // category có trường 'type' = 'thu' | 'chi'
            if ($category->type === 'chi') {
                $wallet->balance -= $transaction->amount;
            } else {
                $wallet->balance += $transaction->amount;
            }
            $wallet->save();
        });

        return response()->json(Transaction::with('category')->find($transaction->id), 201);
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
        // Kiểm tra quyền
        if ($transaction->wallet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate request
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

        $newWallet = $request->user()->wallets()->find($validated['wallet_id']);
        if (!$newWallet) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::transaction(function() use ($transaction, $validated, $newWallet) {
            $oldWallet = $transaction->wallet;
            $oldAmount = $transaction->amount;
            $oldType = $transaction->category->type;

            // Update transaction
            $transaction->update($validated);

            $newAmount = $transaction->amount;
            $newType = $transaction->category->type;

            // --- Nếu cùng ví ---
            if ($oldWallet->id === $newWallet->id) {
                $delta = 0;

                // Hoàn tác giao dịch cũ
                $delta += ($oldType === 'chi') ? $oldAmount : -$oldAmount;

                // Áp dụng giao dịch mới
                $delta += ($newType === 'chi') ? -$newAmount : $newAmount;

                $oldWallet->balance += $delta;
                $oldWallet->save();
            } else {
                // --- Nếu khác ví ---
                // Ví cũ: hoàn tác giao dịch cũ
                if ($oldType === 'chi') $oldWallet->balance += $oldAmount;
                else $oldWallet->balance -= $oldAmount;
                $oldWallet->save();

                // Ví mới: áp dụng giao dịch mới
                if ($newType === 'chi') $newWallet->balance -= $newAmount;
                else $newWallet->balance += $newAmount;
                $newWallet->save();
            }
        });

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

        DB::transaction(function() use ($transaction) {
            $wallet = $transaction->wallet;
            $amount = $transaction->amount;
            $type = $transaction->category->type;

            // Điều chỉnh số dư ví
            if ($type === 'chi') $wallet->balance += $amount;
            else $wallet->balance -= $amount;
            $wallet->save();

            $transaction->delete();
        });

        return response()->json(['message' => 'Deleted successfully']);
    }
}
