<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeSources\Http\Controllers\KnowledgeSourceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('knowledgesources', KnowledgeSourceController::class)->names('knowledgesources');
});
