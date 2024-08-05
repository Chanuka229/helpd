<?php

use App\Http\Controllers\Api\Account\AccountController as AccountAccountController;
use App\Http\Controllers\Api\Auth\AuthController as AuthAuthController;
use App\Http\Controllers\Api\Dashboard\Admin\CompaniesController as DashboardAdminCompaniesController;
use App\Http\Controllers\Api\Dashboard\Admin\LabelController as DashboardAdminLabelController;
use App\Http\Controllers\Api\Dashboard\Admin\LanguageController as DashboardAdminLanguageController;
use App\Http\Controllers\Api\Dashboard\Admin\PriorityController as DashboardAdminPriorityController;
use App\Http\Controllers\Api\Dashboard\Admin\SettingController as DashboardAdminSettingController;
use App\Http\Controllers\Api\Dashboard\Admin\StatusController as DashboardAdminStatusController;
use App\Http\Controllers\Api\Dashboard\Admin\UserController as DashboardAdminUserController;
use App\Http\Controllers\Api\Dashboard\Admin\UserRoleController as DashboardAdminUserRoleController;
use App\Http\Controllers\Api\Dashboard\CannedReplyController as DashboardCannedReplyController;
use App\Http\Controllers\Api\Dashboard\StatsController as DashboardStatsController;
use App\Http\Controllers\Api\Dashboard\TicketsController as DashboardTicketsController;
use App\Http\Controllers\Api\File\FileController as FileFileController;
use App\Http\Controllers\Api\Language\LanguageController as LanguageLanguageController;
use App\Http\Controllers\Api\Tickets\TicketsController as UserTicketsController;

Route::group(['prefix' => 'lang'], static function () {
    Route::get('/', [LanguageLanguageController::class, 'list'])->name('language.list');
    Route::get('/{lang}', [LanguageLanguageController::class, 'get'])->name('language.get');
});

Route::group(['prefix' => 'auth'], static function () {
    Route::post('login', [AuthAuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [AuthAuthController::class, 'logout'])->name('auth.logout');
    Route::post('register', [AuthAuthController::class, 'register'])->name('auth.register');
    Route::post('recover', [AuthAuthController::class, 'recover'])->name('auth.recover');
    Route::post('reset', [AuthAuthController::class, 'reset'])->name('auth.reset');
    Route::get('user', [AuthAuthController::class, 'user'])->name('auth.user');
    Route::post('check', [AuthAuthController::class, 'check'])->name('auth.check');
});

Route::group(['prefix' => 'account'], static function () {
    Route::post('update', [AccountAccountController::class, 'update'])->name('account.update');
    Route::post('password', [AccountAccountController::class, 'password'])->name('account.password');
});

Route::get('files/download/{uuid}', [FileFileController::class, 'download'])->name('files.download');
Route::apiResource('files', FileFileController::class)->only(['store', 'show']);

Route::get('Tickets/statuses', [UserTicketsController::class, 'statuses'])->name('Tickets.statuses');
Route::get('Tickets/Companies', [UserTicketsController::class, 'Companies'])->name('Tickets.Companies');
Route::post('Tickets/attachments', [FileFileController::class, 'uploadAttachment'])->name('Tickets.upload-attachment');
Route::post('Tickets/{Tickets}/reply', [UserTicketsController::class, 'reply'])->name('Tickets.reply');
Route::apiResource('Tickets', UserTicketsController::class)->except(['update', 'destroy']);

Route::group(['prefix' => 'dashboard'], static function () {

    Route::group(['prefix' => 'stats'], static function () {
        Route::get('count', [DashboardStatsController::class, 'count'])->name('dashboard.stats-count');
        Route::get('registered-users', [DashboardStatsController::class, 'registeredUsers'])->name('dashboard.stats.registered-users');
        Route::get('opened-Tickets', [DashboardStatsController::class, 'openedTickets'])->name('dashboard.stats.opened-Tickets');
    });

    Route::get('Tickets/filters', [DashboardTicketsController::class, 'filters'])->name('dashboard.Tickets.filters');
    Route::get('Tickets/canned-replies', [DashboardTicketsController::class, 'cannedReplies'])->name('dashboard.Tickets.canned-replies');
    Route::post('Tickets/quick-actions', [DashboardTicketsController::class, 'quickActions'])->name('dashboard.Tickets.quick-actions');
    Route::post('Tickets/attachments', [DashboardTicketsController::class, 'uploadAttachment'])->name('dashboard.Tickets.upload-attachment');
    Route::post('Tickets/{Tickets}/remove-label', [DashboardTicketsController::class, 'removeLabel'])->name('dashboard.Tickets.remove-label');
    Route::post('Tickets/{Tickets}/quick-actions', [DashboardTicketsController::class, 'TicketsQuickActions'])->name('dashboard.Tickets.Tickets-quick-actions');
    Route::post('Tickets/{Tickets}/reply', [DashboardTicketsController::class, 'reply'])->name('dashboard.Tickets.reply');
    Route::apiResource('Tickets', DashboardTicketsController::class)->except(['update']);

    Route::apiResource('canned-replies', DashboardCannedReplyController::class);

    Route::group(['prefix' => 'admin'], static function () {

        Route::get('Companies/users', [DashboardAdminCompaniesController::class, 'users'])->name('dashboard.Companies.users');
        Route::apiResource('Companies', DashboardAdminCompaniesController::class);

        Route::apiResource('labels', DashboardAdminLabelController::class);

        Route::apiResource('statuses', DashboardAdminStatusController::class)->except(['store', 'delete']);

        Route::apiResource('priorities', DashboardAdminPriorityController::class)->except(['store', 'delete']);

        Route::get('users/user-roles', [DashboardAdminUserController::class, 'userRoles'])->name('users.user-roles');
        Route::apiResource('users', DashboardAdminUserController::class);

        Route::get('user-roles/permissions', [DashboardAdminUserRoleController::class, 'permissions'])->name('user-roles.permissions');
        Route::apiResource('user-roles', DashboardAdminUserRoleController::class);

        Route::get('settings/user-roles', [DashboardAdminSettingController::class, 'userRoles'])->name('settings.user-roles');
        Route::get('settings/languages', [DashboardAdminSettingController::class, 'languages'])->name('settings.languages');
        Route::get('settings/general', [DashboardAdminSettingController::class, 'getGeneral'])->name('settings.get.general');
        Route::post('settings/general', [DashboardAdminSettingController::class, 'setGeneral'])->name('settings.set.general');
        Route::get('settings/seo', [DashboardAdminSettingController::class, 'getSeo'])->name('settings.get.seo');
        Route::post('settings/seo', [DashboardAdminSettingController::class, 'setSeo'])->name('settings.set.seo');
        Route::get('settings/appearance', [DashboardAdminSettingController::class, 'getAppearance'])->name('settings.get.appearance');
        Route::post('settings/appearance', [DashboardAdminSettingController::class, 'setAppearance'])->name('settings.set.appearance');
        Route::get('settings/localization', [DashboardAdminSettingController::class, 'getLocalization'])->name('settings.get.localization');
        Route::post('settings/localization', [DashboardAdminSettingController::class, 'setLocalization'])->name('settings.set.localization');
        Route::get('settings/authentication', [DashboardAdminSettingController::class, 'getAuthentication'])->name('settings.get.authentication');
        Route::post('settings/authentication', [DashboardAdminSettingController::class, 'setAuthentication'])->name('settings.set.authentication');
        Route::get('settings/outgoing-mail', [DashboardAdminSettingController::class, 'getOutgoingMail'])->name('settings.get.outgoingMail');
        Route::post('settings/outgoing-mail', [DashboardAdminSettingController::class, 'setOutgoingMail'])->name('settings.set.outgoingMail');
        Route::get('settings/logging', [DashboardAdminSettingController::class, 'getLogging'])->name('settings.get.logging');
        Route::post('settings/logging', [DashboardAdminSettingController::class, 'setLogging'])->name('settings.set.logging');
        Route::get('settings/captcha', [DashboardAdminSettingController::class, 'getCaptcha'])->name('settings.get.captcha');
        Route::post('settings/captcha', [DashboardAdminSettingController::class, 'setCaptcha'])->name('settings.set.captcha');

        Route::post('languages/sync', [DashboardAdminLanguageController::class, 'sync'])->name('language.sync');
        Route::apiResource('languages', DashboardAdminLanguageController::class);
    });
});