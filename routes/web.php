<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('contacts.create');
});


Route::middleware('auth')->group(function(){
    Route::get('/admin',fn()=>'問い合わせ一覧(準備中)')->name('admin.index');
    Route::resource('admin/tags',TagController::class)->except(['create','show']);
});

Route::resource('contacts',ContactController::class)->only(['create','store']);
Route::post('/contacts/confirm',[ContactController::class,'confirm']);
Route::get('/contacts/thanks',[ContactController::class,'thanks'])->name('contact.thanks');
