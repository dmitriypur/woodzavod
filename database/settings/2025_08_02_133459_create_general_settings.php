<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Деревянное домостроение.');
        $this->migrator->add('general.phone', '');
        $this->migrator->add('general.email', '');
        $this->migrator->add('general.vk', '');
        $this->migrator->add('general.telegram', '');
        $this->migrator->add('general.youtube', '');
        $this->migrator->add('general.rutube', '');
        $this->migrator->add('general.city', '');
        $this->migrator->add('general.postal_code', '');
        $this->migrator->add('general.address', '');
        $this->migrator->add('general.coordinates', '');
        $this->migrator->add('general.schedule', '');
        $this->migrator->add('general.favicon', '');
    
    }
};
