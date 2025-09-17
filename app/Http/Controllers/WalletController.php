<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wallets = $request->user()->wallets()->get();

        return response()->json($wallets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'wallet_name' => ['required','string','max:255'],
                'balance' => ['required','numeric','min:0'],
                'skin_index' => ['nullable','integer','min:1','max:12']
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $wallet = $request->user()->wallets()->create($request->only('wallet_name', 'balance', 'skin_index'));

        return response()->json($wallet, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet, Request $request)
    {
        if ($wallet->user_id != $request->user()->id) {
            return response()->json(['message'=>"Unauthorized"],403);
        }

        return response()->json($wallet);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Wallet $wallet, Request $request)
    {
        if ($wallet->user_id != $request->user()->id) {
            return response()->json(['message'=>"Unauthorized"],403);
        }

        try {
           $request->validate([
                'wallet_name' => ['required','string','max:255'],
                'skin_index' => ['nullable','integer','min:1','max:12'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $wallet->update($request->only('wallet_name', 'skin_index'));

        return response()->json($wallet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet, Request $request)
    {
        if ($wallet->user_id != $request->user()->id) {
            return response()->json(['message'=>"Unauthorized"],403);
        }

        $wallet->delete();

        return response()->json(["message"=> "Deleted successfully"],200);
    }
}
