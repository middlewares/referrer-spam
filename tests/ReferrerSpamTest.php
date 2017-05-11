<?php

namespace Middlewares\Tests;

use Middlewares\ReferrerSpam;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class ReferrerSpamTest extends \PHPUnit_Framework_TestCase
{
    public function referrerSpamProvider()
    {
        return [
            [false, 'http://www.0n-line.tv'],
            [false, 'http://холодныйобзвон.рф'],
            [true, 'http://youtube.com'],
        ];
    }

    /**
     * @dataProvider referrerSpamProvider
     */
    public function testReferrerSpam($allowed, $refererHeader)
    {
        $request = Factory::createServerRequest()->withHeader('Referer', $refererHeader);

        $response = Dispatcher::run([
            new ReferrerSpam(),
        ], $request);

        if ($allowed) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(403, $response->getStatusCode());
        }
    }
}
