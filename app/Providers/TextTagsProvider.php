<?php

namespace App\Providers;

use App\Services\Dummies\DummyTextTagsGenerator;
use App\Services\TextTagsGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TextTagsProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->bind(TextTagsGenerator::class, function (Application $app) {
            // if ($app->environment('testing')) {
            return new DummyTextTagsGenerator;
            // } else {
            //     // return new TensorflowTextTagsGenerator;
            // }
        });
    }
}
