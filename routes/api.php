<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {

    Route::prefix('members')->group(function () {

        Route::prefix('events')->group(function () {

            Route::get('/calendar', [\App\Http\Controllers\API\EventsController::class, 'calendar'])->name('events.calendar')->withoutMiddleware('auth:api');

            Route::get('/', [\App\Http\Controllers\API\EventsController::class, 'index'])->name('events.index')->withoutMiddleware('auth:api');

        });

        Route::prefix('statistics')->group(function () {

            Route::get('/age', [\App\Http\Controllers\API\Statistics\MemberAgeController::class, 'age'])->name('members.statistics.age')->withoutMiddleware('auth:api');

            Route::get('/children', [\App\Http\Controllers\API\Statistics\MemberAgeController::class, 'children'])->name('members.statistics.children')->withoutMiddleware('auth:api');

            Route::get('/youths', [\App\Http\Controllers\API\Statistics\MemberAgeController::class, 'youths'])->name('members.statistics.youths')->withoutMiddleware('auth:api');

            Route::get('/adults', [\App\Http\Controllers\API\Statistics\MemberAgeController::class, 'adults'])->name('members.statistics.adults')->withoutMiddleware('auth:api');

        });

        Route::prefix('total')->group(function () {

            Route::get('/alive', [\App\Http\Controllers\API\MemberController::class, 'total_alive'])->name('members.total.alive')->withoutMiddleware('auth:api');

            Route::get('/registered', [\App\Http\Controllers\API\MemberController::class, 'total_registered'])->name('members.total.registered')->withoutMiddleware('auth:api');

        });

        Route::get('/filter', [App\Http\Controllers\API\MemberController::class, 'filter'])->name('members.filter')->withoutMiddleware('auth:api');

        Route::prefix('{member}')->group(function () {

            Route::match(['put', 'patch'], '/parents', [\App\Http\Controllers\API\MemberController::class, 'change_parents'])->name('members.parents.update')->middleware('permission:change.clan.member.parents');

            Route::prefix('/spouses')->group(function () {

                Route::delete('/{spouse}', [\App\Http\Controllers\API\MemberController::class, 'remove_spouse'])->name('members.spouses.remove')->middleware('permission:remove.clan.member.spouse');

                Route::post('/', [\App\Http\Controllers\API\MemberController::class, 'add_spouse'])->name('members.spouses.add')->middleware('permission:add.clan.member.spouse');

            });

            Route::delete('/', [\App\Http\Controllers\API\MemberController::class, 'destroy'])->name('members.destroy')->middleware('permission:delete.clan.members');

            Route::match(['put', 'patch'], '/', [\App\Http\Controllers\API\MemberController::class, 'update'])->name('members.update')->middleware('permission:edit.clan.members');

            Route::get('/', [\App\Http\Controllers\API\MemberController::class, 'show'])->name('members.show')->withoutMiddleware('auth:api');

        });

        Route::post('/', [\App\Http\Controllers\API\MemberController::class, 'store'])->name('members.store')->middleware('permission:create.clan.members');

        Route::get('/', [\App\Http\Controllers\API\MemberController::class, 'index'])->name('members.index')->withoutMiddleware('auth:api');

    });

    Route::prefix('users')->group(function () {

        Route::delete('/cleanup', [\App\Http\Controllers\API\UserController::class, 'clean_up'])->name('users.cleanup')->middleware('permission:clean.up.user.records');

        Route::prefix('total')->group(function () {

            Route::get('/valid', [\App\Http\Controllers\API\UserController::class, 'total_with_details'])->name('users.total.valid')->middleware('permission:view.total.users.with.details');

            Route::get('/', [\App\Http\Controllers\API\UserController::class, 'total'])->name('users.total')->middleware('permission:view.total.users');

        });

        Route::prefix('{user}')->group(function () {

            Route::prefix('roles')->group(function () {

                Route::match(['put', 'patch'], '/remove', [\App\Http\Controllers\API\UserController::class, 'remove_roles'])->name('users.roles.remove')->middleware('permission:remove.roles.from.user');

                Route::match(['put', 'patch'], '/assign', [\App\Http\Controllers\API\UserController::class, 'assign_roles'])->name('users.roles.assign')->middleware('permission:assign.roles.to.user');

            });

            Route::match(['put', 'patch'], '/password', [\App\Http\Controllers\API\UserController::class, 'change_password'])->name('users.change.password')->middleware('permission:change.password');

            Route::delete('/', [\App\Http\Controllers\API\UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete.users');

            Route::get('/', [\App\Http\Controllers\API\UserController::class, 'show'])->name('users.show')->middleware('permission:view.users');

        });

        Route::post('/', [\App\Http\Controllers\API\UserController::class, 'store'])->name('users.store')->middleware('permission:create.users');

        Route::get('/', [\App\Http\Controllers\API\UserController::class, 'index'])->name('users.index')->middleware('permission:view.users');

    });

    Route::prefix('roles')->group(function () {

        Route::prefix('{roles}')->group(function () {

            Route::prefix('permissions')->group(function () {

                Route::prefix('remove')->group(function () {

                    Route::match(['put', 'patch'], '/all', [\App\Http\Controllers\API\RolesController::class, 'remove_all_permissions'])->name('roles.permissions.remove.all')->middleware('permission:remove.permissions.from.roles');

                    Route::match(['put', 'patch'], '/', [\App\Http\Controllers\API\RolesController::class, 'remove_permissions'])->name('roles.permissions.remove')->middleware('permission:remove.permissions.from.roles');

                });

                Route::match(['put', 'patch'], '/add', [\App\Http\Controllers\API\RolesController::class, 'add_permissions'])->name('roles.update')->middleware('permission:add.permissions.to.roles');

            });

            Route::match(['put', 'patch'], '/', [\App\Http\Controllers\API\RolesController::class, 'update'])->name('roles.update')->middleware('permission:edit.roles');

            Route::delete('/', [\App\Http\Controllers\API\RolesController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete.roles');

            Route::get('/', [\App\Http\Controllers\API\RolesController::class, 'show'])->name('roles.show')->middleware('permission:view.roles');

        });

        Route::post('/', [\App\Http\Controllers\API\RolesController::class, 'store'])->name('roles.store')->middleware('permission:create.roles');

        Route::get('/', [\App\Http\Controllers\API\RolesController::class, 'index'])->name('roles.index')->middleware('permission:view.roles');

    });

    Route::prefix('permissions')->group(function () {

        Route::get('/{permission}', [\App\Http\Controllers\API\PermissionsController::class, 'show'])->name('permissions.show')->middleware('permission:view.permissions');

        Route::get('/', [\App\Http\Controllers\API\PermissionsController::class, 'index'])->name('permissions.index')->middleware('permission:view.permissions');

    });

});

Route::group(['middleware' => 'api'],function ($router) {

    Route::post('login', [\App\Http\Controllers\API\AuthController::class,'login']);

    Route::post('logout', [\App\Http\Controllers\API\AuthController::class,'logout']);

    Route::post('refresh', [\App\Http\Controllers\API\AuthController::class,'refresh']);

    Route::post('me', [\App\Http\Controllers\API\AuthController::class,'me']);

    Route::post('sendPasswordResetCode', [\App\Http\Controllers\API\ResetPasswordController::class, 'sendEmail']);

    Route::post('verifyPasswordResetCode', [\App\Http\Controllers\API\ResetPasswordController::class, 'verifyToken']);

    Route::post('resetPassword', [\App\Http\Controllers\API\ResetPasswordController::class, 'changePassword']);

});
