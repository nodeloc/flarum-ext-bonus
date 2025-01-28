<?php

/*
 * This file is part of nodeloc/flarum-ext-bonus.
 *
 * Copyright (c) 2025 James.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Nodeloc\Bonus;


use Flarum\Extend;
use Illuminate\Console\Scheduling\Event;
use Nodeloc\Bonus\Command\MoneyDistributor;

// extend.php
return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    new Extend\Locales(__DIR__.'/locale'),
    (new Extend\Routes('api'))
        ->get('/bonus-list', 'bonus-list.index', Controllers\BonusListController::class)
        ->post('/bonus-list-items', 'bonus-list.create', Controllers\ItemStoreController::class)
        ->patch('/bonus-list-items/{id:[0-9]+}', 'bonus-list.update', Controllers\ItemUpdateController::class)
        ->delete('/bonus-items/{id:[0-9]+}', 'bonus-list.delete', Controllers\ItemDeleteController::class),
    (new Extend\Console())
        ->command(MoneyDistributor::class)
        ->schedule('bonus:postBonus', function (Event $event)  {
            $event->everyMinute();
        })
];
