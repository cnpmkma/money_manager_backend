<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Budget::with('category')
                    ->where('user_id', $user->id)
                    ->get();
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'max_amount' => 'required|numeric|min:0',
        ]);

        $budget = Budget::create($data);
        return response()->json($budget, 201);
    }

    public function show(Budget $budget)
    {
        return $budget->load(['category', 'user']);
    }

    public function update(Request $request, Budget $budget)
    {
        $data = $request->validate([
            'max_amount' => 'sometimes|numeric|min:0',
        ]);

        $budget->update($data);
        return response()->json($budget);
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return response()->noContent();
    }
}
