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
use App\JobProcessor;
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

Route::get('task', function () {
    $jobProcessor = new JobProcessor();
    $jobID = $jobProcessor->getJobToProcess();

    return ['jobID' => $jobID];
});

Route::get('task/{jobID}', function($jobID) {
    $jobProcessor = new JobProcessor();
    $status = $jobProcessor->getStatus($jobID);

    return ['status' => $status];
});

Route::post('process/{jobID?}', function ($jobID = null) {
    $jobProcessor = new JobProcessor();

    if ($jobID === null) {
        $jobID = $jobProcessor->getJobToProcess();
    }

    $output = $jobProcessor->process($jobID);

    return ['output' => $output];
});

Route::get('getAverageProcessingTime', function () {
    $jobProcessor = new JobProcessor();
    return $jobProcessor->getAverageProcessingTime();
});

Route::get('phpinfo', function () {
    phpinfo();
});

