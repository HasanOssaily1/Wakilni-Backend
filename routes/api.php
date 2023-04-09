<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\ProductsTypesController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resources([
    'items' => ItemsController::class,
    'products' => ProductsTypesController::class,
]);


 Route::post('/auth/login', [AuthController::class, 'login'])->withoutMiddleware(['jwt']);
 Route::post('/auth/register', [AuthController::class, 'register'])->withoutMiddleware(['jwt']);


