<?php
/**
 * This file is part of the Swiftype PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swiftype\Connection\Handler;

/**
 * This handler add automatically all URIs data to the request.
 *
 * @package Swiftype\Connection\Handler
 *
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 */
class RequestUrlHandler
{
    /**
     * @var string
     */
    private $uriPrefix = null;

    /**
     * @var callable
     */
    private $handler;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var \GuzzleHttp\Ring\Core
     */
    private $ringUtils;

    /**
     * Constructor.
     *
     * @param callable $handler     original handler
     * @param string   $apiEndpoint API endpoint (eg. http://myserver/).
     * @param string   $uriPrefix   A prefix to be added to all URIs?
     */
    public function __construct(callable $handler, $apiEndpoint, $uriPrefix = null)
    {
        $this->handler = $handler;

        $urlComponents = parse_url($apiEndpoint);

        $this->scheme = $urlComponents['scheme'];
        $this->host = $urlComponents['host'];
        $this->uriPrefix = $uriPrefix;

        if (isset($urlComponents['port'])) {
            $this->host = sprintf('%s:%s', $this->host, $urlComponents['port']);
        }

        $this->ringUtils = new \GuzzleHttp\Ring\Core();
    }

    /**
     * Add host, scheme and uri prefix to the request before calling the original handler.
     *
     * @param array $request original request
     *
     * @return array
     */
    public function __invoke($request)
    {
        $handler = $this->handler;
        $request = $this->ringUtils->setHeader($request, 'host', [$this->host]);
        $request['scheme'] = $this->scheme;

        if ($this->uriPrefix) {
            $request['uri'] = $this->addURIPrefix($request['uri']);
        }

        return $handler($request);
    }

    /**
     * Add prefix for the URI.
     *
     * @param string $uri
     *
     * @return string
     */
    private function addURIPrefix($uri)
    {
        return sprintf('%s%s', '/' == substr($uri, 0, 1) ? rtrim($this->uriPrefix, '/') : $this->uriPrefix, $uri);
    }
}
