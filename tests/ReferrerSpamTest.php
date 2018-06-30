<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\ReferrerSpam;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class ReferrerSpamTest extends TestCase
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
    public function testReferrerSpam(bool $allowed, string $refererHeader)
    {
        $response = Dispatcher::run(
            [
                new ReferrerSpam(),
            ],
            Factory::createServerRequest()->withHeader('Referer', $refererHeader)
        );

        if ($allowed) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(403, $response->getStatusCode());
        }
    }
}
