<?php

/*
* This file is part of the Cilex framework.
*
* (c) Mike van Riel <mike.vanriel@naenius.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Cilex\Provider;

use Cilex\Application;
use Cilex\ServiceProviderInterface;

use Guzzle\Service\Builder\ServiceBuilder;

/**
 * Guzzle Provider.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class GuzzleServiceProvider implements ServiceProviderInterface
{
    /**
     * register the app
     *
     * @param \Cilex\Application $app
     */
    public function register(Application $app)
    {
        $app['guzzle'] = $app->share(function () use ($app) {
            $serviceBuilder = ServiceBuilder::factory($app['guzzle.configs']);

            return $serviceBuilder;
        });
    }
}
