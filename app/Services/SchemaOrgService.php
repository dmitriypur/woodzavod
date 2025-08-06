<?php

namespace App\Services;

use App\Models\House;
use App\Models\Review;
use App\Models\Category;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Graph;
use Illuminate\Support\Facades\URL;

class SchemaOrgService
{
    /**
     * Генерирует микроразметку для организации
     */
    public function generateOrganization(): string
    {
        $organization = Schema::organization()
            ->name('Деревянное домостроение')
            ->url(URL::to('/'))
            ->logo(asset('images/logo.png'))
            ->description('Строительство деревянных домов из бруса в Московской области')
            ->address(Schema::postalAddress()
                ->addressCountry('RU')
                ->addressRegion('Московская область')
            )
            ->contactPoint(Schema::contactPoint()
                ->contactType('customer service')
                ->areaServed('RU')
                ->availableLanguage('ru')
            )
            ->sameAs([
                // Добавить ссылки на соцсети если есть
            ]);

        return $organization->toScript();
    }

    /**
     * Генерирует микроразметку для веб-сайта
     */
    public function generateWebSite(): string
    {
        $website = Schema::webSite()
            ->name('Деревянное домостроение')
            ->url(URL::to('/'))
            ->description('Строительство деревянных домов из бруса в Московской области')
            ->inLanguage('ru')
            ->potentialAction(Schema::searchAction()
                ->target(URL::to('/catalog?search={search_term_string}'))
                ->query('required name=search_term_string')
            );

        return $website->toScript();
    }

    /**
     * Генерирует микроразметку для дома (Product)
     */
    public function generateHouseProduct(House $house): string
    {
        $product = Schema::product()
            ->name($house->title)
            ->description($house->description)
            ->url(route('house.show', $house->slug))
            ->sku($house->id)
            ->category('Деревянные дома')
            ->brand(Schema::brand()->name('Деревянное домостроение'));

        if ($house->hasMedia('main')) {
            $product->image($house->getFirstMediaUrl('main'));
        }

        // Всегда добавляем offer для валидности Schema.org Product
        $offer = Schema::offer()
            ->priceCurrency('RUB')
            ->availability('https://schema.org/InStock')
            ->seller(Schema::organization()->name('Деревянное домостроение'));
            
        if ($house->price) {
            $offer->price($house->price);
        } else {
            // Если цена не указана, добавляем информацию о том, что цена по запросу
            $offer->priceSpecification(
                Schema::priceSpecification()
                    ->priceCurrency('RUB')
                    ->valueAddedTaxIncluded(true)
            );
        }
        
        $product->offers($offer);

        // Добавляем характеристики дома
        $additionalProperties = [];
        
        if ($house->area_total) {
            $additionalProperties[] = Schema::propertyValue()
                ->name('Общая площадь')
                ->value($house->area_total . ' м²');
        }

        if ($house->floor_count) {
            $additionalProperties[] = Schema::propertyValue()
                ->name('Количество этажей')
                ->value($house->floor_count);
        }

        if ($house->bedroom_count) {
            $additionalProperties[] = Schema::propertyValue()
                ->name('Количество спален')
                ->value($house->bedroom_count);
        }

        if ($house->bathroom_count) {
            $additionalProperties[] = Schema::propertyValue()
                ->name('Количество санузлов')
                ->value($house->bathroom_count);
        }

        if ($house->brus_volume) {
            $additionalProperties[] = Schema::propertyValue()
                ->name('Объем бруса')
                ->value($house->brus_volume . ' м³');
        }

        if (!empty($additionalProperties)) {
            $product->additionalProperty($additionalProperties);
        }

        // Добавляем отзывы если есть
        $reviews = $house->reviews()->where('is_published', true)->get();
        if ($reviews->count() > 0) {
            $reviewSchemas = [];
            $totalRating = 0;
            
            foreach ($reviews as $review) {
                $reviewSchema = Schema::review()
                    ->author(Schema::person()->name($review->author))
                    ->reviewBody($review->text)
                    ->datePublished($review->created_at->toISOString());
                
                $reviewSchemas[] = $reviewSchema;
            }
            
            $product->review($reviewSchemas);
        }

        return $product->toScript();
    }

    /**
     * Генерирует микроразметку для отзыва
     */
    public function generateReview(Review $review): string
    {
        $reviewSchema = Schema::review()
            ->author(Schema::person()->name($review->author))
            ->reviewBody($review->text)
            ->datePublished($review->created_at->toISOString())
            ->reviewRating(Schema::rating()
                ->ratingValue($review->rating)
                ->bestRating(5)
                ->worstRating(1)
            )
            ->itemReviewed(Schema::service()
                ->name('Строительство деревянных домов')
                ->description('Услуги по строительству деревянных домов под ключ')
                ->provider(Schema::localBusiness()
                    ->name('Деревянное домостроение')
                    ->url(url('/'))
                )
            );

        return $reviewSchema->toScript();
    }

    /**
     * Генерирует микроразметку для хлебных крошек
     */
    public function generateBreadcrumbs(array $breadcrumbs): string
    {
        $listItems = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $listItems[] = Schema::listItem()
                ->position($index + 1)
                ->name($breadcrumb['name'])
                ->item($breadcrumb['url'] ?? null);
        }

        $breadcrumbList = Schema::breadcrumbList()
            ->itemListElement($listItems);

        return $breadcrumbList->toScript();
    }

    /**
     * Генерирует микроразметку для списка товаров (каталог)
     */
    public function generateItemList($houses, string $name = 'Каталог домов'): string
    {
        $listItems = [];
        
        foreach ($houses as $index => $house) {
            $listItems[] = Schema::listItem()
                ->position($index + 1)
                ->url(route('house.show', $house->slug))
                ->name($house->title);
        }

        $itemList = Schema::itemList()
            ->name($name)
            ->itemListElement($listItems);

        return $itemList->toScript();
    }

    /**
     * Генерирует микроразметку для FAQ (если есть)
     */
    public function generateFAQ(array $faqs): string
    {
        $questions = [];
        
        foreach ($faqs as $faq) {
            $questions[] = Schema::question()
                ->name($faq['question'])
                ->acceptedAnswer(Schema::answer()
                    ->text($faq['answer'])
                );
        }

        $faqPage = Schema::fAQPage()
            ->mainEntity($questions);

        return $faqPage->toScript();
    }

    /**
     * Генерирует микроразметку для веб-страницы
     */
    public function generateWebPage(string $title, string $content, string $url): string
    {
        $webPage = Schema::webPage()
            ->name($title)
            ->description(strip_tags(\Illuminate\Support\Str::limit($content, 160)))
            ->url($url)
            ->inLanguage('ru')
            ->isPartOf(Schema::webSite()
                ->name('Деревянное домостроение')
                ->url(URL::to('/'))
            )
            ->publisher(Schema::organization()
                ->name('Деревянное домостроение')
                ->url(URL::to('/'))
            );

        return $webPage->toScript();
    }

    /**
     * Генерирует микроразметку для локального бизнеса
     */
    public function generateLocalBusiness(): string
    {
        $localBusiness = Schema::localBusiness()
            ->name('Деревянное домостроение')
            ->description('Строительство деревянных домов из бруса в Московской области')
            ->url(URL::to('/'))
            ->logo(asset('images/logo.png'))
            ->address(Schema::postalAddress()
                ->addressCountry('RU')
                ->addressRegion('Московская область')
            )
            ->geo(Schema::geoCoordinates()
                // Добавить координаты если есть
                // ->latitude(55.7558)
                // ->longitude(37.6176)
            )
            ->openingHours('Mo-Fr 09:00-18:00')
            ->contactPoint(Schema::contactPoint()
                ->contactType('customer service')
                ->areaServed('RU')
                ->availableLanguage('ru')
            );

        return $localBusiness->toScript();
    }

    /**
     * Генерирует микроразметку для основной навигации сайта
     */
    public function generateSiteNavigation(): string
    {
        $navigationElements = [
            [
                'name' => 'Главная',
                'url' => route('home')
            ],
            [
                'name' => 'О нас',
                'url' => url('/about')
            ],
            [
                'name' => 'Каталог',
                'url' => route('catalog')
            ],
            [
                'name' => 'Контакты',
                'url' => url('/contact')
            ]
        ];

        $siteNavigation = Schema::siteNavigationElement()
            ->name('Основная навигация');

        return $siteNavigation->toScript();
    }

    /**
     * Генерирует микроразметку для навигации в подвале
     */
    public function generateFooterNavigation(): string
    {
        $footerElements = [
            [
                'name' => 'Главная',
                'url' => route('home')
            ],
            [
                'name' => 'О нас',
                'url' => url('/about')
            ],
            [
                'name' => 'Каталог',
                'url' => route('catalog')
            ],
            [
                'name' => 'Контакты',
                'url' => url('/contact')
            ],
            [
                'name' => 'Карта сайта',
                'url' => url('/sitemap.html')
            ],
            [
                'name' => 'Политика конфиденциальности',
                'url' => url('/policy')
            ]
        ];

        $footerNavigation = Schema::siteNavigationElement()
            ->name('Навигация подвала');

        return $footerNavigation->toScript();
    }
}