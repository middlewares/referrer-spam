<?php
declare(strict_types = 1);

namespace Middlewares;

use ComposerLocator;
use Middlewares\Utils\Traits\HasResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class ReferrerSpam implements MiddlewareInterface
{
    use HasResponseFactory;

    /**
     * @var array|null
     */
    private $blackList;

    /**
     * @throws RuntimeException If no idn to ascii library is detected
     */
    private static function checkIDNtoASCII()
    {
        if (!function_exists('idn_to_ascii') && !method_exists('\TrueBV\Punycode', 'encode')) {
            throw new RuntimeException(
                "To handle Unicode encoded domain name, Intl PHP extension or the lib 'true/punycode' is required"
            );
        }
    }

    public function __construct(array $blackList = null)
    {
        self::checkIDNtoASCII();
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
            $host = $this->encodeIDN($host);

            if (in_array($host, $this->blackList, true)) {
                return $this->createResponse(403);
            }
        }

        return $handler->handle($request);
    }

    /**
     * Encode IDN to ASCII form (russian url for example)
     */
    private function encodeIDN(string $domain): string
    {
        if (function_exists('idn_to_ascii')) {
            return idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        }

        return (new \TrueBV\Punycode())->encode($domain);
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
