<?php

namespace Middlewares\Tests;

use Middlewares\ReferrerSpam;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\CallableMiddleware;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;

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
        $request = (new ServerRequest())->withHeader('Referer', $refererHeader);

        $response = (new Dispatcher([
            new ReferrerSpam(),
            new CallableMiddleware(function () {
                return new Response();
            }),
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);

        if ($allowed) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(403, $response->getStatusCode());
        }
    }
}
