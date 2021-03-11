<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Sendportal\Base\Facades\Sendportal;
use App\Http\Middleware\RequireWorkspace;

Route::middleware([
    config('sendportal-host.throttle_middleware'),
    RequireWorkspace::class
])->group(function() {

    // Auth'd API routes (workspace-level auth!).
    Sendportal::apiRoutes();

    Route::get(
        'v1/all-tags', 
        'Api\TagController@index'
    )->name('api.all-tags');

});

// Non-auth'd API routes.
Sendportal::publicApiRoutes();