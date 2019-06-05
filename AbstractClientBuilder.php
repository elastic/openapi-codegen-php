<?php
/**
 * This file is part of the Elastic OpenAPI PHP code generator.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\OpenApi\Codegen;

/**
 * A base client builder implementation.
 *
 * @package Elastic\OpenApi\Codegen
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2
 */
abstract class AbstractClientBuilder
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $tracer;

    /**
     * @var \Elastic\OpenApi\Codegen\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $host;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->serializer = new \Elastic\OpenApi\Codegen\Serializer\SmartSerializer();
        $this->logger = new \Psr\Log\NullLogger();
        $this->tracer = new \Psr\Log\NullLogger();
    }

    /**
     * @return \Elastic\OpenApi\Codegen\Serializer\SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getTracer()
    {
        return $this->tracer;
    }

    /**
     * @param \Elastic\OpenApi\Codegen\Serializer\SerializerInterface $serializer
     *
     * @return $this
     */
    public function setSerializer(\Elastic\OpenApi\Codegen\Serializer\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $tracer
     *
     * @return $this
     */
    public function setTracer(\Psr\Log\LoggerInterface $tracer)
    {
        $this->tracer = $tracer;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Return the configured clien.
     */
    abstract public function build();

    /**
     * @return callable
     */
    protected function getHandler()
    {
        $handler = new \GuzzleHttp\Ring\Client\CurlHandler();

        $handler = new Connection\Handler\RequestSerializationHandler($handler, $this->serializer);
        $handler = new Connection\Handler\RequestHostHandler($handler, $this->host);
        $handler = new Connection\Handler\ConnectionErrorHandler($handler);
        $handler = new Connection\Handler\ResponseSerializationHandler($handler, $this->serializer);

        return $handler;
    }

    /**
     * @return \Elastic\OpenApi\Codegen\Connection\Connection
     */
    protected function getConnection()
    {
        return new Connection\Connection($this->getHandler(), $this->getLogger(), $this->getTracer());
    }

    /**
     * @return \Elastic\OpenApi\Codegen\Endpoint\Builder
     */
    abstract protected function getEndpointBuilder();
}
