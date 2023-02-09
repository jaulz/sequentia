<?php

namespace Jaulz\Sequentia\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::statement('CREATE EXTENSION IF NOT EXISTS hstore');

    $migration = include __DIR__ . '/../database/migrations/create_sequentia_extension.php.stub';
    $migration->up();
});

test('increments sequence', function () {
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->text('title');
    });

    Schema::table('posts', function (Blueprint $table) {
        $table->sequentia('sequence');
    });

    collect([null,null,null,null])->keys()->each(function($index) {
        $post = DB::table('posts')->insertReturning([
            'title' => 'test'
        ])->first();

        expect($post->sequence)->toBe($index + 1);
    });
});

test('respects groups', function () {
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->integer('category_id');
        $table->text('title');
    });

    Schema::table('posts', function (Blueprint $table) {
        $table->sequentia('sequence');
        $table->sequentia('category_sequence', ['category_id']);
    });

    for ($index = 0; $index < 100; $index++) {
        $categoryId = $index % 2;
        $post = DB::table('posts')->insertReturning([
            'title' => 'test',
            'category_id' => $categoryId,
        ])->first();

        expect($post->sequence)->toBe($index + 1);
        expect($post->category_sequence)->toBe(intdiv($index, 2) + 1);
    }
});