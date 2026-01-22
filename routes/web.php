<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

Route::get('/', function () {
    return redirect()->route('articles.index');
});

Route::resource('articles', ArticleController::class);

// AI Routes
Route::post('articles/{article}/ai-summarize', [ArticleController::class, 'aiSummarize'])
    ->name('articles.ai.summarize');
Route::post('articles/{article}/ai-tags', [ArticleController::class, 'aiTags'])
    ->name('articles.ai.tags');
Route::post('articles/{article}/ai-seo', [ArticleController::class, 'aiSeo'])
    ->name('articles.ai.seo');


 Route::post('articles/ai-generate-content', [ArticleController::class, 'generateContent'])
    ->name('articles.ai.generate');
Route::post('articles/ai-quick-generate', [ArticleController::class, 'quickGenerate'])
    ->name('articles.ai.quick');
Route::post('articles/{article}/ai-expand', [ArticleController::class, 'expandContent'])
    ->name('articles.ai.expand');   
Route::post('articles/{article}/ai-rewrite-tone', [ArticleController::class, 'rewriteTone'])
    ->name('articles.ai.rewrite-tone');