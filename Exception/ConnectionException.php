<?php
/**
 * This file is part of the Elastic OpenAPI PHP code generator.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\OpenApi\Codegen\Exception;

/**
 * Wrapper for connection exceptions raised by the client.
 *
 * @package Elastic\OpenApi\Codegen\Exception
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 */
class ConnectionException extends \Exception implements ClientException
{
}
