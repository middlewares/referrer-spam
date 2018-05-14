<?php
declare(strict_types = 1);

namespace Middlewares;

use ComposerLocator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
            // encode IDN to punycode (russian url for example)
            $host = (new \TrueBV\Punycode())->encode($host);

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
        $path = ComposerLocator::getPath('matomo/referrer-spam-blacklist').'/spammers.txt';

        if (!is_file($path)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Unable to locate the matomo referrer spam blacklist file');
            // @codeCoverageIgnoreEnd
        }

        return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
