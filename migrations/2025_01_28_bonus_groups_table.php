<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('bonus_list', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->unique();
            $table->integer('content');
            $table->integer('amount');
            $table->tinyInteger('schedule_type')->default(0);
            $table->tinyInteger('is_post')->default(0);
            $table->timestamp('schedule_time')->nullable();
            $table->timestamp('last_post_time')->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('bonus_list');
    },
];
