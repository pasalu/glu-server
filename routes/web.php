<?php

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

use App\JobSubmitter;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('task', function (Request $request) {
    $command = $request->input('command');
    $priority = $request->input('priority');
    
    $jobsSubmitter = new JobSubmitter();
    $jobID = $jobsSubmitter->submit($command, $priority);

    return ['jobID' => $jobID];
});

Route::get('phpinfo', function () {
    phpinfo();
});

Route::post('second', function () {
    return response()->json([
        'first' => 'Peter',
        'last' => 'Salu'
    ]);
});
