<?php

use App\Http\Controllers\{ Question};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user', fn (Request $request) => $request->user());

Route::get('my-questions/{status}', Question\MineController::class)->name('my-questions');
Route::get('questions', Question\IndexController::class)->name('questions.index');
Route::post('questions', Question\StoreController::class)->name('questions.store');
Route::get('questions/download', Question\DownloadController::class)->name('questions.download');
Route::put('questions/{question}', Question\UpdateController::class)->name('questions.update');
Route::delete('questions/{question}', Question\DeleteController::class)->name('questions.delete');
Route::delete('questions/{question}/archive', Question\ArchiveController::class)->name('questions.archive');
Route::put('questions/{question}/restore', Question\RestoreController::class)->name('questions.restore');
Route::put('questions/{question}/publish', Question\PublishController::class)->name('questions.publish');
Route::post('questions/{question}/vote/{vote}', Question\VoteController::class)->name('questions.vote');
