<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Category;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    /**
     * Отображение списка домов
     */
    public function index(Request $request)
    {
        $query = House::query()->where('is_published', true);

        // Фильтрация по категории
        if ($request->has('category')) {
            $categorySlug = $request->input('category');
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                $query->whereHas('categories', function($q) use ($category) {
                    $q->where('categories.id', $category->id);
                });
            }
        }

        // Сортировка
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'area_asc':
                $query->orderBy('area_total', 'asc');
                break;
            case 'area_desc':
                $query->orderBy('area_total', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $houses = $query->paginate(12);
        $categories = Category::all();

        return view('houses.index', compact('houses', 'categories'));
    }

    /**
     * Отображение детальной информации о доме
     */
    public function show($slug)
    {
        $house = House::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Получаем отзывы для дома
        $reviews = $house->reviews()->where('is_published', true)->get();

        // Получаем похожие дома из тех же категорий
        $similarHouses = House::where('id', '!=', $house->id)
            ->where('is_published', true)
            ->whereHas('categories', function($query) use ($house) {
                $query->whereIn('categories.id', $house->categories->pluck('id'));
            })
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('houses.show', compact('house', 'reviews', 'similarHouses'));
    }
}
