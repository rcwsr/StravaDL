<?php
namespace StravaDL\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use StravaDL\StravaDownloader;

class StravaDownloaderServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        //change to config file.
        $client_secret = $app['strava.secret'];
        $client_id = $app['strava.id'];

        $app['stravaDL'] = $app->share(function () use ($client_secret, $client_id){
            return new StravaDownloader($client_secret, $client_id);
        });
    }

    public function boot(Application $app){}
}