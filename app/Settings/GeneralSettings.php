<?php

namespace App\Settings;

use Illuminate\Support\Str;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public string $phone;

    public string $vk;

    public string $telegram;

    public string $youtube;

    public string $email;

    public string $city;

    public string $postal_code;

    public string $address;

    public string $coordinates;

    public string $schedule;

    public ?string $favicon;

    public string $whatsapp;

    public string $rutube;


    public static function group(): string
    {
        return 'general';
    }

    public function faviconMimeType(): string
    {
        return Str::after($this->favicon, '.') === 'svg' ? 'image/svg+xml' : 'image/png';
    }

    public function scheduleForSchemaOrg(): array
    {
        return explode('; ', $this->schedule);
    }

}
