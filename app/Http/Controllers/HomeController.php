<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Получаем популярные проекты (первые 6)
        $houses = House::with('media')
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Получаем отзывы для главной страницы
        $reviews = Review::where('is_published', true)
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('welcome', compact('houses', 'reviews'));
    }
}
