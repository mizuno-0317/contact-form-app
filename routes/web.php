<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
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

// Route::get('/', function () {
//     return redirect()->route('contacts.create');
// });

Route::get('/',[ContactController::class,'create'])->name('contact.create');



Route::middleware('auth')->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');
    Route::get('/admin/contacts/{contact}',[AdminController::class,'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}',[AdminController::class,'destroy'])->name('admin.destroy');
    Route::resource('admin/tags',TagController::class)->except(['create','show']);
});

Route::resource('contacts',ContactController::class)->only(['create','store']);
Route::post('/contacts/confirm',[ContactController::class,'confirm']);
Route::get('/thanks',[ContactController::class,'thanks'])->name('contact.thanks');

