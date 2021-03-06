# cms
Jungle Mini Code CMS
## Установка ##

```php
<?php

use JMCode\Cms\App;

include (dirname(__DIR__) . '\\vendor\\autoload.php');

$app = new App();

/**
* Доступные методы
* all - Общий метод (get, post, put, delete}
* use - Расширение стеков
* $app->{get, post, put, delete, all, use}
*/

// Основные страницы
$app->get('/', APP_DIR_PATH . '\\index.php');
$app->get('/faq', APP_DIR_PATH . '\\faq.php');
$app->get('/about', APP_DIR_PATH . '\\about.php');

// Дополнение роутера
$app->use('/news', APP_DIR_PATH . '\\news\\router.php');
$app->use('/admins', APP_DIR_PATH . '\\admins\\router.php');
$app->use('/maps', APP_DIR_PATH . '\\maps\\router.php');

// Если запрпос не найдено (Выводим "Страница не найдено" 404)
$app->use(function($req, $res, $next) {
    return $next(new Error('Страница не найдено', 404));
});

$app->use(function($err, $req, $res, $next) {
    // Если ошибка было передано то срабатывает данная функция с 4 параметрами
});

$app->run();
```
