<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LostItemController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('index');

// Test route to check database and users
Route::get('/test-db', function() {
    try {
        $userCount = \App\Models\User::count();
        $users = \App\Models\User::all();
        return response()->json([
            'database' => 'connected',
            'user_count' => $userCount,
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->userID,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->user_Role
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::post('/log-out', [AuthController::class, 'logout'])->name('log-out');

// Contact page route
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/index', [ContactController::class, 'home'])->name('index');

//Route::get('/add-item', [ItemController::class, 'create'])->name('add-item-form');
Route::delete('/item/{id}/delete', [ItemController::class, 'destroy'])->name('destroy');
Route::post  ('/storeniga', [LostItemController::class, 'storeLostItem'])->name('storeLostItem');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/item/{item}/edit', [ItemController::class, 'edit'])->name('editItem');
Route::get('/pcparts', [DashboardController::class, 'parts'])->name('pcparts');
Route::get('/item', [ItemController::class, 'index'])->name('item');
Route::post('/store', [ItemController::class, 'store'])->name('store');
Route::put('/item/{item}', [ItemController::class, 'update'])->name('updateItem');

Route::get('/lostitem', [LostItemController::class, 'index'])->name('lostitem');
Route::get('/lostitem/{lostitem}/edit', [LostItemController::class, 'edit'])->name('editLostItem');
Route::put('/lostitem/{lostitem}', [LostItemController::class, 'updateLostItem'])->name('updateLostItem');
Route::delete('/lostitem/{id}/delete', [LostItemController::class, 'destroylostItem'])->name('destroylostItem');

Route::get('/user', [UserController::class, 'index'])->name('user');
Route::put('/update-user', [UserController::class, 'updateUser'])->name('updateUser');
Route::post('/users/add', [UserController::class, 'store'])->name('userstore');
Route::delete('/user/{id}/user', [UserController::class, 'destroy'])->name('destroyuser');


Route::get('/viewdetails', [DashboardController::class, 'details'])->name('details');
Route::get('/laboneF', [DashboardController::class, 'laboneF'])->name('laboneF');
Route::get('/laboneNF', [DashboardController::class, 'laboneNF'])->name('laboneNF');

Route::get('/labtwoF', [DashboardController::class, 'labtwoF'])->name('labtwoF');
Route::get('/labtwoNF', [DashboardController::class, 'labtwoNF'])->name('labtwoNF');

Route::get('/getComlab1Quantity', [LostItemController::class, 'getComlab1Quantity']);

