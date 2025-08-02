@servers(['production' => 'root@77.222.42.47', 'local' => '127.0.0.1'])

@setup
    $repository = 'git@github.com:dmitriypur/woodzavod.git';
    $releases_dir = '/var/www/derevyannoe-domostroenie.ru/releases';
    $app_dir = '/var/www/derevyannoe-domostroenie.ru';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
    $commit = $commit ?? 'HEAD';
@endsetup

@story('deploy')
    clone_repository
    run_composer
    create_env
    update_symlinks
    optimize_application
    migrate_database
    restart_services
@endstory

@story('deploy-local')
    run_composer_local
    optimize_application_local
    migrate_database_local
@endstory

@task('clone_repository', ['on' => 'production'])
    echo 'Клонирование репозитория...'
    [ -d {{ $releases_dir }} ] || mkdir -p {{ $releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    git reset --hard {{ $commit }}
@endtask

@task('run_composer', ['on' => 'production'])
    echo 'Установка зависимостей Composer...'
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-scripts -q -o --no-dev
@endtask

@task('create_env', ['on' => 'production'])
    echo 'Создание .env файла...'
    cd {{ $new_release_dir }}
    cat > .env << 'EOF'
APP_NAME="Деревянное домостроение"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY
APP_DEBUG=false
APP_URL=https://derevyannoe-domostroenie.ru

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=woodzavod
DB_USERNAME=woodzavod
DB_PASSWORD=Lesorub1979!@#
DB_SOCKET=/var/run/mysqld/mysqld.sock

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF
    php artisan key:generate --force
@endtask

@task('update_symlinks', ['on' => 'production'])
    echo 'Обновление символических ссылок...'
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
    chgrp -h www-data {{ $app_dir }}/current
@endtask

@task('optimize_application', ['on' => 'production'])
    echo 'Оптимизация приложения...'
    cd {{ $new_release_dir }}
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    npm install
    npm run build
    npm prune --production
@endtask

@task('migrate_database', ['on' => 'production'])
    echo 'Выполнение миграций...'
    cd {{ $new_release_dir }}
    php artisan migrate --force
@endtask

@task('restart_services', ['on' => 'production'])
    echo 'Перезапуск сервисов...'
    sudo service nginx reload
    sudo service php8.3-fpm reload
@endtask

@task('run_composer_local', ['on' => 'local'])
    echo 'Установка зависимостей Composer...'
    composer install --prefer-dist --no-scripts -q -o
@endtask

@task('optimize_application_local', ['on' => 'local'])
    echo 'Оптимизация приложения...'
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    npm ci
    npm run build
@endtask

@task('migrate_database_local', ['on' => 'local'])
    echo 'Выполнение миграций...'
    php artisan migrate --force
@endtask

@task('rollback', ['on' => 'production'])
    echo 'Откат к предыдущей версии...'
    cd {{ $releases_dir }}
    ln -nfs {{ $releases_dir }}/$(find . -maxdepth 1 -name "20*" | sort | tail -n 2 | head -n 1) {{ $app_dir }}/current
    echo "Откат выполнен к: $(find . -maxdepth 1 -name "20*" | sort | tail -n 2 | head -n 1)"
@endtask

@task('cleanup', ['on' => 'production'])
    echo 'Очистка старых релизов...'
    cd {{ $releases_dir }}
    find . -maxdepth 1 -name "20*" | sort | head -n -5 | xargs rm -rf
    echo 'Очистка завершена'
@endtask

@finished
    // @slack('https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK', '#deployments', "Деплой завершен на {$server}")
@endfinished

@error
    // @slack('https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK', '#deployments', "Ошибка деплоя на {$server}: {$task}")
@enderror