<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use stubbles\input\errors\ParamErrors;
/**
 * Base class for requests with parameters.
 */
abstract class ParamRequest implements Request
{
    private Params $params;

    /**
     * @param  array<string,string>  $params
     */
    public function __construct(array $params)
    {
        $this->params = new Params($params);
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
     * @since  1.3.0
     */
    public function paramErrors(): ParamErrors
    {
        return $this->params->errors();
    }

    /**
     * checks whether a request param is set
     *
     * @since  1.3.0
     */
    public function hasParam(string $paramName): bool
    {
        return $this->params->contain($paramName);
    }

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @since  1.3.0
     */
    public function validateParam(string $paramName): ValueValidator
    {
        return new ValueValidator($this->params->value($paramName));
    }

    /**
     * returns request value from params for filtering or validation
     *
     * @since  1.3.0
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
