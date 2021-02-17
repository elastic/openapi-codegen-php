<?php

/**
 * This file is part of the Elastic OpenAPI PHP code generator.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\OpenApi\Codegen\Tests\Unit\Connection\Handler;

use PHPUnit\Framework\TestCase;
use Elastic\OpenApi\Codegen\Connection\Handler\RequestSerializationHandler;
use Elastic\OpenApi\Codegen\Serializer\SmartSerializer;

/**
 * Unit tests for the request serialization handler.
 *
 * @package Elastic\OpenApi\Codegen\Test\Unit\Connection\Handler
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2
 */
class RequestSerializationHandlerTest extends TestCase
{
    /**
     * Check data serialization accross various requests of the dataprovider.
     *
     * @dataProvider requestDataProvider
     */
    public function testSerializeRequest($request, $expectedBody)
    {
        $handler = $this->getHandler();
        $this->assertEquals($expectedBody, $handler($request));
    }

    /**
     * @return array
     */
    public function requestDataProvider()
    {
        $data = [
            [['http_method' => 'POST', 'body' => ['foo' => 'bar']], '{"foo":"bar"}'],
            [['http_method' => 'GET', 'query_params' => ['foo' => 'bar']], null],
            [['http_method' => 'POST', 'body' => ['foo' => 'bar'], 'query_params' => ['foo' => 'bar']], '{"foo":"bar"}'],
            [['http_method' => 'POST', 'body' => ['foo1' => 'bar1'], 'query_params' => ['foo2' => 'bar2']], '{"foo1":"bar1","foo2":"bar2"}'],
            [[], null],
        ];

        return $data;
    }

    /**
     * @return \Elastic\OpenApi\Codegen\Connection\Handler\RequestSerializationHandler
     */
    private function getHandler()
    {
        $handler = function ($request) {
            return isset($request['body']) ? $request['body'] : null;
        };

        $serializer = $this->getSerializer();

        return new RequestSerializationHandler($handler, $serializer);
    }

    /**
     * @return \Elastic\OpenApi\Codegen\Serializer\SmartSerializer
     */
    private function getSerializer()
    {
        return new SmartSerializer();
    }
}
