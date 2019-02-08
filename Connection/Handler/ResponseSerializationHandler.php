<?php
/**
 * This file is part of the Swiftype Common PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swiftype\Connection\Handler;

use GuzzleHttp\Ring\Core;
use Swiftype\Serializer\SerializerInterface;

/**
 * Automatatic serialization of the request params and body.
 *
 * @package Swiftype\Connection\Handler
 *
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 */
class ResponseSerializationHandler
{
    /**
     * @var callable
     */
    private $handler;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param callable            $handler    original handler
     * @param SerializerInterface $serializer serialize
     */
    public function __construct(callable $handler, SerializerInterface $serializer)
    {
        $this->handler = $handler;
        $this->serializer = $serializer;
    }

    public function __invoke($request)
    {
        $handler = $this->handler;
        $response = Core::proxy($handler($request), function ($response) use ($request) {
            if (true === isset($response['body'])) {
                $response['body'] = stream_get_contents($response['body']);
                $headers = isset($response['transfer_stats']) ? $response['transfer_stats'] : [];
                $response['body'] = $this->serializer->deserialize($response['body'], $headers);
            }

            return $response;
        });

        return $response;
    }
}
