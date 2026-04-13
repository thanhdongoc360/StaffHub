<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Role;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-role', function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'employee']);
});

Route::get('/map-role', function () {
    $users = User::all();

    foreach ($users as $user) {
        $role = Role::where('name', $user->role)->first();
        if ($role) {
            $user->role_id = $role->id;
            $user->save();
        }
    }

    return "Done";
});