<?php


use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/variant', function () {
    $size = request('size');
    $pot = request('pot');
    $productId = request('product_id'); // lấy thêm product_id từ query

    $variant = ProductVariant::where('size', $size)
        ->where('pot', $pot)
        ->where('product_id', $productId) // lọc đúng sản phẩm
        ->first();

    return response()->json($variant);
})->name('getVariant');