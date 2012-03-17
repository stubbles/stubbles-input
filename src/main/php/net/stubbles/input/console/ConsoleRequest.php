<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\console;
use net\stubbles\input\AbstractRequest;
use net\stubbles\input\Params;
/**
 * Request implementation for command line.
 *
 * @since  2.0.0
 */
class ConsoleRequest extends AbstractRequest
{
    /**
     * constructor
     *
     * @param  array  $params
     * @Inject
     * @Named('argv')
     */
    public function __construct(array $params)
    {
        parent::__construct(new Params($params));
    }

    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return 'cli';
    }
}
?>