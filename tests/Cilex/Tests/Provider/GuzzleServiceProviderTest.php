<?php
/**
 * This file is part of the Cilex framework.
 *
 * (c) Mike van Riel <mike.vanriel@naenius.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Ben Selby <benmatselby@gmail.com>
 */

namespace Cilex\Tests\Provider;

use Cilex\Application;
use Cilex\Provider\GuzzleServiceProvider;

/**
 * Test file for GuzzleProvider
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class GuzzleServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that an exception is thrown if the config is not present
     *
     * @return void
     */
    public function testRegisterWillThrowExceptionIfConfigIsNotThere()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Identifier "guzzle.configs" is not defined.'
        );

        $app = new Application('Test');

        $app->register(
            new GuzzleServiceProvider(),
            array()
        );

        $config = $app['guzzle'];
    }
}
