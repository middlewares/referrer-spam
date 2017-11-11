<?php
declare(strict_types = 1);

namespace Middlewares;

use ComposerLocator;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class ReferrerSpam implements MiddlewareInterface
{
    /**
     * @var array|null
     */
    private $blackList;

    public function __construct(array $blackList = null)
    {
        $this->blackList = $blackList;
    }

    /**
     * Process a request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->hasHeader('Referer')) {
            if ($this->blackList === null) {
                $this->blackList = self::getBlackList();
            }

            // http://php.net/manual/en/function.parse-url.php#114817
            $referer = preg_replace_callback(
                '%[^:/@?&=#]+%usD',
                function ($matches) {
                    return urlencode($matches[0]);
                },
                $request->getHeaderLine('Referer')
            );

            $host = urldecode(parse_url($referer, PHP_URL_HOST));
            $host = preg_replace('/^(www\.)/i', '', $host);

            if (in_array($host, $this->blackList, true)) {
                return Utils\Factory::createResponse(403);
            }
        }

        return $handler->handle($request);
    }

    /**
     * Returns the piwik's referrer spam blacklist.
     */
    private static function getBlackList(): array
    {
        $path = ComposerLocator::getPath('piwik/referrer-spam-blacklist').'/spammers.txt';

        if (!is_file($path)) {
            throw new RuntimeException('Unable to locate the piwik referrer spam blacklist file');
        }

        return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
