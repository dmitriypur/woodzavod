<?php

namespace App\Services;

use App\Models\House;
use App\Models\Review;
use App\Models\Category;
use App\Settings\GeneralSettings;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Graph;
use Illuminate\Support\Facades\URL;

class SchemaOrgService
{
    public function __construct(
        protected GeneralSettings $settings
    ) {}

    /**
     * Генерирует микроразметку для организации
     */
    public function generateOrganization(): string
    {
        $organization = Schema::organization()
            ->name($this->settings->site_name ?? 'Деревянное домостроение')
            ->url(URL::to('/'))
            ->logo(asset('images/logo.png'))
            ->description('Строительство деревянных домов из бруса в Кировской области');

        // Добавляем адрес если есть данные
        if ($this->settings->city || $this->settings->address) {
            $address = Schema::postalAddress()
                ->addressCountry('RU');
            
            if ($this->settings->city) {
                $address->addressLocality($this->settings->city);
            }
            
            if ($this->settings->address) {
                $address->streetAddress($this->settings->address);
            }
            
            if ($this->settings->postal_code) {
                $address->postalCode($this->settings->postal_code);
            }
            
            $organization->address($address);
        }

        // Добавляем контактную точку
        $contactPoint = Schema::contactPoint()
            ->contactType('customer service')
            ->areaServed('RU')
            ->availableLanguage('ru');
            
        if ($this->settings->phone) {
            $contactPoint->telephone($this->settings->phone);
        }
        
        if ($this->settings->email) {
            $contactPoint->email($this->settings->email);
        }
        
        $organization->contactPoint($contactPoint);

        // Добавляем социальные сети
        $sameAs = array_filter([
            $this->settings->vk,
            $this->settings->telegram,
            $this->settings->youtube,
            $this->settings->rutube,
        ]);
        
        if (!empty($sameAs)) {
            $organization->sameAs($sameAs);
        }

        return $organization->toScript();
    }

    /**
     * Генерирует микроразметку для веб-сайта
     */
    public function generateWebSite(): string
    {
        $website = Schema::webSite()
            ->name($this->settings->site_name ?? 'Деревянное домостроение')
            ->url(URL::to('/'))
            ->description('Строительство деревянных домов из бруса в Кировской области')
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
            ->brand(Schema::brand()->name($this->settings->site_name ?? 'Деревянное домостроение'));

        if ($house->hasMedia('main')) {
            $product->image($house->getFirstMediaUrl('main'));
        }

        // Всегда добавляем offer для валидности Schema.org Product
        $offer = Schema::offer()
            ->priceCurrency('RUB')
            ->availability('https://schema.org/InStock')
            ->seller(Schema::organization()->name($this->settings->site_name ?? 'Деревянное домостроение'));
            
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
            ->itemReviewed(Schema::localBusiness()
                ->name($this->settings->site_name ?? 'Деревянное домостроение')
                ->description('Строительство деревянных домов из бруса под ключ')
                ->url(url('/'))
                ->address(Schema::postalAddress()
                    ->addressCountry('RU')
                    ->addressRegion('Кировская область')
                    ->addressLocality($this->settings->city ?? 'Киров')
                    ->streetAddress($this->settings->address ?? '')
                    ->postalCode($this->settings->postal_code ?? '')
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
                ->name($this->settings->site_name ?? 'Деревянное домостроение')
                ->url(URL::to('/'))
            )
            ->publisher(Schema::organization()
                ->name($this->settings->site_name ?? 'Деревянное домостроение')
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
            ->name($this->settings->site_name ?? 'Деревянное домостроение')
            ->description('Строительство деревянных домов из бруса в Кировской области')
            ->url(URL::to('/'))
            ->logo(asset('images/logo.png'));

        // Добавляем адрес если есть данные
        if ($this->settings->city || $this->settings->address) {
            $address = Schema::postalAddress()
                ->addressCountry('RU')
                ->addressRegion('Кировской область');
            
            if ($this->settings->city) {
                $address->addressLocality($this->settings->city);
            }
            
            if ($this->settings->address) {
                $address->streetAddress($this->settings->address);
            }
            
            if ($this->settings->postal_code) {
                $address->postalCode($this->settings->postal_code);
            }
            
            $localBusiness->address($address);
        }

        // Добавляем координаты если есть
        if ($this->settings->coordinates) {
            $coords = explode(',', $this->settings->coordinates);
            if (count($coords) === 2) {
                $localBusiness->geo(Schema::geoCoordinates()
                    ->latitude(trim($coords[0]))
                    ->longitude(trim($coords[1]))
                );
            }
        }

        // Добавляем расписание если есть
        if ($this->settings->schedule) {
            $localBusiness->openingHours($this->settings->schedule);
        }

        // Добавляем контактную точку
        $contactPoint = Schema::contactPoint()
            ->contactType('customer service')
            ->areaServed('RU')
            ->availableLanguage('ru');
            
        if ($this->settings->phone) {
            $contactPoint->telephone($this->settings->phone);
        }
        
        if ($this->settings->email) {
            $contactPoint->email($this->settings->email);
        }
        
        $localBusiness->contactPoint($contactPoint);

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