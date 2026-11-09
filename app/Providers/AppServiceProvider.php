<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving('dompdf.wrapper', function ($document) {
            $document->getDomPDF()->setCallbacks([
                ['event' => 'end_document', 'f' => function ($page, $pages, $canvas, $fonts) {
                    $font = $fonts->getFont('DejaVu Sans', 'normal');
                    $text = $page.'/'.$pages;
                    $width = $fonts->getTextWidth($text, $font, 8);
                    $canvas->text(($canvas->get_width() - $width) / 2, $canvas->get_height() - 29, $text, $font, 8, [0.43, 0.48, 0.55]);
                }],
            ]);
        });
    }
}
