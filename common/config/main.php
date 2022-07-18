<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'ru-RU',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'parser' => [
            'class' => 'common\components\Parser',
            'host' => 'https://www.wildberries.ru',
            'categoryUrl' => 'https://www.wildberries.ru/webapi/menu/main-menu-ru-ru.json',
            'productUrl' => 'https://wbx-content-v2.wbstatic.net/ru/',
            'productUrlv2' => 'https://card.wb.ru/cards/detail?spp=0&emp=0&locale=ru&lang=ru&curr=rub&nm=',
            'priceHistoryUrl' => 'https://wbx-content-v2.wbstatic.net/price-history/',
            'productListUrl' => 'https://catalog.wb.ru/catalog/',
            'curlOpt' => [
                'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36',
                'header' => [
                    'Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1',
                    'Accept-Language: en-US,en;q=0.8,ru;q=0.6,uk;q=0.4',
                    'Accept-Charset: Windows-1251, utf-8, *;q=0.1',
                    'Accept-Encoding: deflate, identity, *;q=0',
                ]
            ]
        ],
    ],
];
