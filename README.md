Парсер объявлений с сайта do.ngs.ru
============================

Выполняет сбор заголовков и описаний объявлений и сохраняет в БД.


ТРЕБОВАНИЯ
------------

Проект выполнен на Yii 2.0, PHP 7.0


УСТАНОВКА
------------

Установите проект командой:

~~~
git clone http://github.com/HarryButtowski/parser-ngs.git parser-ngs
~~~

Перейдите во вновь созданный каталог `parser-ngs` и выполните команду:
~~~
composer install
~~~


НАСТРОЙКА
-------------

### База данных

Отредактируйте файл `config/db.php` заполнив актуальными данными, для примера:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

Для создания необходимых таблиц выполните команду:
~~~
php yii migrate/up
~~~