# Backups

Команды для бэкапа приложения, бэкапит базу данных и папку storage/app/public

Так же создает два диска `backups` и `yandex`

Yandex можно использовать и для хранения файлов, параметр `YANDEX_CLOUD_FOLDER` используется только для бэкапов

## Install

`php artisan vendor:publish --provider="PortedCheese\Backups\ServiceProvider" --tag=config`

    YANDEX_CLOUD_ID = Id ключа
    YANDEX_CLOUD_SECRET = Secret ключа
    YANDEX_CLOUD_BUCKET = Имя бакета
    YANDEX_CLOUD_FOLDER = Папка куда будут сохраняться файлы
    YANDEX_CLOUD_REGION = ru-central1 (регион)
    
## Usage

`backup:app {period=daily} {--folder=}` - Создает бэкап и отправляет в облако (если не задан конфиг, то сохранить в папку current)

`restore:app {period=daily} {--from-current} {--folder=}` - Восстанавливает бэкап из облака
    
    Есть api для создания и восстановления бэкапов, нужно включить очередь на сервере
    GET /api/backups - Список всех бэкапов в папке
    POST /api/backups/{period} - Создать бэкап
    PUT /api/backups/{period} - Восстановить бэкап