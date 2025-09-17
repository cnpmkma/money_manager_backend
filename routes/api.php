<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/register", [AuthController::class,"register"]);
Route::post("/login", [AuthController::class,"login"]);
Route::middleware('auth:sanctum')->group(function () {
    Route::post("/logout", [AuthController::class,"logout"]);
});
    
Route::middleware("auth:sanctum")->group(function () {
    Route::get("/wallets", [WalletController::class,"index"]);
    Route::post("/wallets", [WalletController::class,"store"]);
    Route::get("/wallets/{wallet}", [WalletController::class,"show"]);
    Route::patch("/wallets/{wallet}", [WalletController::class,"update"]);
    Route::delete("/wallets/{wallet}", [WalletController::class,"destroy"]);

    Route::get("/transactions", [TransactionController::class,"index"]);
    Route::post("/transactions", [TransactionController::class,"store"]);
    Route::get("/transactions/{transaction}", [TransactionController::class,"show"]);
    Route::patch("/transactions/{transaction}", [TransactionController::class,"update"]);
    Route::delete("/transactions/{transaction}", [TransactionController::class,"destroy"]);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/profile', [UserController::class, 'profile']);
    Route::patch('/profile', [UserController::class, 'updateProfile']);

    Route::get('/budgets', [BudgetController::class, 'index']);
    Route::get('/budgets/{budget}', [BudgetController::class, 'show']);
    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::put('/budgets/{budget}', [BudgetController::class, 'update']);
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy']);
});
