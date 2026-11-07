<?php

use Botble\Base\Facades\BaseHelper;
use ArchiElite\Career\Models\Career;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'ArchiElite\Career\Http\Controllers', 'middleware' => ['web', 'core']], function (): void {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function (): void {
        Route::group(['prefix' => 'careers', 'as' => 'careers.'], function (): void {
            Route::get('applications', [
                'as' => 'applications.index',
                'uses' => 'CareerApplicationController@index',
                'permission' => 'careers.applications.index',
            ]);

            Route::get('applications/{application}/download', [
                'as' => 'applications.download',
                'uses' => 'CareerApplicationController@download',
                'permission' => 'careers.applications.index',
            ])->wherePrimaryKey();

            Route::delete('applications', [
                'as' => 'applications.bulk-destroy',
                'uses' => 'CareerApplicationController@bulkDestroy',
                'permission' => 'careers.destroy',
            ]);

            Route::delete('applications/{application}', [
                'as' => 'applications.destroy',
                'uses' => 'CareerApplicationController@destroy',
                'permission' => 'careers.destroy',
            ])->wherePrimaryKey();

            Route::resource('', 'CareerController')->parameters(['' => 'career']);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Theme::registerRoutes(function (): void {
            Route::get(SlugHelper::getPrefix(Career::class, 'careers') ?: 'careers', 'PublicController@careers')
                ->name('public.careers');
            Route::post((SlugHelper::getPrefix(Career::class, 'careers') ?: 'careers') . '/{career}/apply', 'PublicController@postApply')
                ->name('public.careers.apply');
        });
    }
});
