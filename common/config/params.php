<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    // цвета яблок
    'appleColors' => [
        1 => 'green',
        2 => 'red',
        3 => 'yellow',
    ],

    // яблоко может появиться в случайное время в промежутке с текущей секунды и на 2 недели вперед
    'timePeriod' => 60 * 60 * 24 * 14,

    // яблоко портится через 5 часов
    'rottenPeriod' => 60 * 60 * 5,

    // При достижении этой цены товар в любом случае отправится в чат
    'minTargetPrice' => 100 * 100,

    // Снижение в процентах от прошлой цены (цена резко снизилась на 80 процентов)
    'targetPercent' => 1 - 0.80,

    // Диапазон цены для выборки товаров
    'minFilterPrice' => 60 * 100,
    'maxFilterPrice' => 300 * 100,
    'limitPages' => 5,
    'sort' => 'popular', // newly

//    'tgBotToken' => "YOUR_TOKEN",
//    'tgChatId' => "@yourchannel"
];
