<?php

namespace Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Middleware\MiddlewareInterface;
use Interop\Http\Middleware\DelegateInterface;
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
     *
     * @param RequestInterface  $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, DelegateInterface $delegate)
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
        $spammers = realpath(__DIR__.'/../../../../vendor/piwik/referrer-spam-blacklist/spammers.txt');

        //dev mode
        if ($spammers === false) {
            $spammers = realpath(__DIR__.'/../vendor/piwik/referrer-spam-blacklist/spammers.txt');
        }

        if ($spammers === false) {
            throw new RuntimeException('Unable to locate the piwik referrer spam blacklist file');
        }

        return file($spammers, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
