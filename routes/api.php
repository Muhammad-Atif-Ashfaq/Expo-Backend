<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AdminController,
    ExpoController,
    ContestController,
    ContestStartController,
    JudgeController,
    FormFieldController,
    ParticipientController,
    ScoreController,
    ScoreCardController,
    FileUploadController,
    ContestResultController,
};



Route::post('/admin/register',   [AdminController::class, 'register'])->name('admin.register');
Route::post('/admin/login',      [AdminController::class, 'login'])->name('admin.login');


Route::get('all/events',[ExpoController::class, 'allEvents']);
Route::get('all/contests/{id}',[ContestController::class, 'allExpoContests']);

Route::get('/behind-screen-results/{id}',   [ContestStartController::class, 'behindScreenResult']);

Route::post('/approved-behind-screen-results',   [ContestStartController::class, 'behindScreenResultAfterApprove']);

Route::get('/behind-screen-contestinfo/{id}',[ContestController::class, 'userScreenContestInfo']);

Route::get('/public-contest-result/{contest_id}',      [ContestResultController::class, 'getPublicContestResult']);
/* admin routes */
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/update_profile',   [AdminController::class, 'update_profile'])->name('admin.update_profile');


    Route::post('logout',      [AdminController::class, 'logout']);
    /*expo, contest, judges, form_fields, participients routes*/
    Route::resource('expo',          ExpoController::class);
    Route::resource('contests',      ContestController::class);
    Route::resource('form_fields',   FormFieldController::class);
    Route::resource('judges',        JudgeController::class);

    Route::get('/judge/{id}',                       [JudgeController::class, 'index']);
    Route::post('/upload-file',                     [FileUploadController::class, 'upload'])->name('upload.file');
    Route::get('/contest-start/{id}',               [ContestStartController::class, 'index']);
    Route::post('/judge-participant/{contest_id}',  [ContestStartController::class, 'judgeParticipant']);

    Route::post('/upload-file',                     [FileUploadController::class, 'upload'])->name('upload.file');
    Route::get('/contest-start/{id}',               [ContestStartController::class, 'index']);
    Route::post('/judge-participant/{contest_id}',  [ContestStartController::class, 'judgeParticipant']);
    Route::post('/publish-record/{contest_id}',     [ContestStartController::class, 'publishRecord']);
    Route::get('/contest-result/{contest_id}',      [ContestStartController::class, 'getContestRecordsWithPositions']);
    Route::post('/approve-judge-score', [ContestStartController::class, 'approveJudgeScores']);
    Route::post('/manual-rematch-contest', [ContestResultController::class, 'manuallyRematch']);
    Route::post('/rematch/{contest_id}',            [ContestStartController::class, 'initiateRematch']);
    Route::get('/generateIframeLink/{contest_id}', [ContestStartController::class, 'generateIframeLink']);
    Route::post('send-pdf',            [ContestResultController::class, 'sendPDF']);
});
    /*participients routes*/
    Route::resource('participients',                ParticipientController::class);
    /* get form fields */
    Route::get('/all_formFields',                   [FormFieldController::class, 'index']);

/* judges routes */
Route::middleware(['auth:sanctum', 'role:judge'])->prefix('judge')->group(function () {

    Route::post('/save_score',          [ScoreController::class, 'save_score'])->name('judge.save_score');
    Route::get('/check_score',          [ScoreController::class, 'check_score'])->name('judge.check_score');
    // Route::apiResource('score-cards',    ScoreCardController::class);
    Route::get('score-cards/{id}',         [ScoreCardController::class, 'index']);

    Route::get('judge-score-card/{id}',         [ScoreCardController::class, 'judgeScoreCard']);

    Route::post('submit-score',         [ContestStartController::class, 'submitScore'])->middleware('sequential_scoring');
});

