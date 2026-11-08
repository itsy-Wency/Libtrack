<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Accept status updates from external borrowing integrations.
Route::post('/webhooks/borrowing-status', [WebhookController::class, 'receive']);
