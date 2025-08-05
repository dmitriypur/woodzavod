<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', [HomeController::class, 'index'])->name('home');

// Маршруты для домов
Route::get('/catalog', [HouseController::class, 'index'])->name('catalog');
Route::get('/catalog/{slug}', [HouseController::class, 'show'])->name('house.show');

// Маршрут для обработки заявок
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

// Маршрут для AJAX отправки форм
Route::post('/submit-form', [LeadController::class, 'submitForm'])->name('submit.form');

// Карты сайта
Route::middleware('cache.sitemap')->group(function () {
    Route::get('/sitemap.html', [SitemapController::class, 'html'])->name('sitemap.html');
    Route::get('/sitemap.xml', [SitemapController::class, 'xml'])->name('sitemap.xml');
    Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots.txt');
});

// Маршрут для тестирования Telegram (только для разработки)
//Route::get('/test-telegram', function() {
//    $controller = new LeadController();
//    $result = $controller->testTelegramConnection();
//    return response()->json($result);
//})->name('test.telegram');
//
//Route::get('/telegram/get-id', function() {
//    $updates = file_get_contents('https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/getUpdates');
//    return response()->json(json_decode($updates, true));
//});

// Маршрут для статических страниц (должен быть последним)
Route::get('/{slug}', [PageController::class, 'page'])->name('page.show');
