<?php
namespace App\Providers;

use Dompdf\Dompdf;
use Barryvdh\DomPDF\ServiceProvider as DomPDFServiceProvider;
use Barryvdh\DomPDF\PDF;

class PdfServiceProvider extends DomPDFServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register(): void
    {
    	$configPath = config_path('dompdf.php');
        $this->mergeConfigFrom($configPath, 'dompdf');

        $this->app->bind('dompdf.options', function(){
            $defines = $this->app['config']->get('dompdf.defines');

            if ($defines) {
                $options = [];
                foreach ($defines as $key => $value) {
                    $key = strtolower(str_replace('DOMPDF_', '', $key));
                    $options[$key] = $value;
                }
            } else {
                $options = $this->app['config']->get('dompdf.options');
            }

            return $options;

        });

        $this->app->bind('dompdf', function() {

            $options = $this->app->make('dompdf.options');
            $dompdf = new Dompdf($options);
            $path = realpath($this->app['config']->get('dompdf.public_path') ?: base_path('public'));
            if ($path === false) {
                throw new \RuntimeException('Cannot resolve public path');
            }
            $dompdf->setBasePath($path);
            return $dompdf;
        });
        $this->app->alias('dompdf', Dompdf::class);

        $this->app->bind('dompdf.wrapper', function ($app) {
            return new PDF($this->app['dompdf'], $this->app['config'], $this->app['files'], $this->app['view']);
        });
    }
}
