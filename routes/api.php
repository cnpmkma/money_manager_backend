<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
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
});

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/transactions", [TransactionController::class,"index"]);
});
