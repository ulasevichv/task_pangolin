<?php

use App\Http\Controllers\API\BestSellersController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => API_VERSION_PREFIX . '/' . 'best-sellers'], function () {

    Route::post('search', [BestSellersController::class, 'search'])->name(BestSellersController::ROUTE_SEARCH);

});