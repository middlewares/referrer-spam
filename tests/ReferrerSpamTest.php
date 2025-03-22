<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\ReferrerSpam;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class ReferrerSpamTest extends TestCase
{
    /**
     * @return array<array<bool|string>>
     */
    public function referrerSpamProvider(): array
    {
        return [
            [false, 'http://www.0n-line.tv'],
            [false, 'http://холодныйобзвон.рф'],
            [true, 'http://youtube.com'],
            [true, 'invalid-url'],
        ];
    }

    /**
     * @dataProvider referrerSpamProvider
     */
    public function testReferrerSpam(bool $allowed, string $refererHeader): void
    {
        $response = Dispatcher::run(
            [
                new ReferrerSpam(),
            ],
            Factory::createServerRequest('GET', '/')->withHeader('Referer', $refererHeader)
        );

        if ($allowed) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    public function testWithoutHeader(): void
    {
        $response = Dispatcher::run(
            [
                new ReferrerSpam(),
            ],
            Factory::createServerRequest('GET', '/')
        );

        $this->assertEquals(200, $response->getStatusCode());
    }
}
