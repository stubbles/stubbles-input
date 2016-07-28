<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
use stubbles\input\errors\ParamErrors;
/**
 * Abstract base class for requests.
 */
abstract class AbstractRequest implements Request
{
    /**
     * list of params
     *
     * @type  Params
     */
    private $params;

    /**
     * constructor
     *
     * @param  \stubbles\input\Params  $params
     */
    public function __construct(Params $params)
    {
        $this->params = $params;
    }

    /**
     * return a list of all param names registered in this request
     *
     * @return  string[]
     */
    public function paramNames(): array
    {
        return $this->params->names();
    }

    /**
     * returns list of errors for request parameters
     *
     * @return  \stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function paramErrors(): ParamErrors
    {
        return $this->params->errors();
    }

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasParam(string $paramName): bool
    {
        return $this->params->contain($paramName);
    }

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @param   string  $paramName  name of parameter
     * @return  \stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateParam(string $paramName): ValueValidator
    {
        return new ValueValidator($this->params->value($paramName));
    }

    /**
     * returns request value from params for filtering or validation
     *
     * @param   string  $paramName  name of parameter
     * @return  \stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readParam(string $paramName): ValueReader
    {
        return new ValueReader(
                $this->params->errors(),
                $paramName,
                $this->params->value($paramName)
        );
    }
}
