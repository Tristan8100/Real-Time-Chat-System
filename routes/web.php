<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Pest\Plugins\Profile;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::post('/send-message', [MessageController::class, 'send']); //

    Route::post('/conversation/{id}', [MessageController::class, 'initiateConversation']); //

    Route::get('/conversation/{id}', [MessageController::class, 'fetchConversation']);

    Route::get('/dashboard', [MessageController::class, 'AllUsers'])->name('dashboard'); //

    Route::get('/fetch-previous', [MessageController::class, 'fetchAllConversations']);

    //api
    Route::get('/dashboard/api', [MessageController::class, 'AllUsersAPI']);

    Route::post('/search', [MessageController::class, 'search']);
});

Route::get('/try', function(){
    $users = \App\Models\User::all();
    return view('try.try1', compact('users'));
});



Route::put('/add-photo', [ProfileController::class, 'addPhoto'])->name('add.photo');



require __DIR__.'/auth.php';
