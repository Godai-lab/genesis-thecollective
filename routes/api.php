<?php

use App\Livewire\ChatGodai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\LeadRequestController;
use App\Http\Controllers\API\VehicleModelController;
use App\Http\Controllers\API\PerplexityController;

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

// Route::prefix('v1')->group(function () {
//     Route::get('/cities/{account_id}/account', [CityController::class, 'indexV1']);
    
//     Route::get('/models/{account_id}/account', [VehicleModelController::class, 'indexV1']);
// });

// Route::prefix('v1')->group(function () {
//     Route::post('/leads_request/{account_id}/account', [LeadRequestController::class, 'storeV1']);
// });
// Route::post('/assistant/search-perplexity', [ChatGodai::class, 'searchPerplexityFromAssistant']);
Route::post('/search-perplexity', [PerplexityController::class, 'search']);