<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Sendportal\Base\Facades\Sendportal;
use App\Http\Middleware\RequireWorkspace;

Route::middleware([
    config('sendportal-host.throttle_middleware'),
    RequireWorkspace::class
])->group(function () {

    // Auth'd API routes (workspace-level auth!).
    Sendportal::apiRoutes();

    Route::get('v1/all-tags', 'Api\TagController@index')->name('api.all-tags');
    Route::get('v1/tags/{tag}', 'Api\TagController@show')->name('api.tags.show');
    Route::get('v1/tags/{tag}/subscribers', 'Api\TagController@subscribers')->name('api.tags.subscribers');

    Route::get('v1/campaigns/{id}/report', 'Api\CampaignReportsController@index')
        ->name('api.compaigns.reports.index');
    Route::get('v1/campaigns/{id}/report/recipients', 'Api\CampaignReportsController@recipients')
        ->name('api.compaigns.reports.recipients');
    Route::get('v1/campaigns/{id}/report/opens', 'Api\CampaignReportsController@opens')
        ->name('api.compaigns.reports.opens');
    Route::get('v1/campaigns/{id}/report/clicks', 'Api\CampaignReportsController@clicks')
        ->name('api.compaigns.reports.clicks');
    Route::get('v1/campaigns/{id}/report/bounces', 'Api\CampaignReportsController@bounces')
        ->name('api.compaigns.reports.bounces');
    Route::get('v1/campaigns/{id}/report/unsubscribes', 'Api\CampaignReportsController@unsubscribes')
        ->name('api.compaigns.reports.unsubscribes');
});

// Non-auth'd API routes.
Sendportal::publicApiRoutes();
