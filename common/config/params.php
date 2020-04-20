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
    'rottenPeriod' => 60 * 60 * 5
];
