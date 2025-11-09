<?php

use Illuminate\Support\Facades\Route;
use App\UI\API\Controllers\RegisterApiController;

Route::post('/register', RegisterApiController::class);
