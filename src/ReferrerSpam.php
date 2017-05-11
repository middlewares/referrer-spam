<?php

namespace Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use RuntimeException;
use ComposerLocator;

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
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
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

        return $delegate->process($request);
    }

    /**
     * Returns the piwik's referrer spam blacklist.
     *
     * @return array
     */
    private static function getBlackList()
    {
        $path = ComposerLocator::getPath('piwik/referrer-spam-blacklist').'/spammers.txt';

        if (!is_file($path)) {
            throw new RuntimeException('Unable to locate the piwik referrer spam blacklist file');
        }

        return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
