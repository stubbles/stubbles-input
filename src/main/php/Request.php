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
 * Interface for handling input data.
 *
 * @api
 */
interface Request
{
    /**
     * returns the request method
     */
    public function method(): string;

    /**
     * return a list of all param names registered in this request
     *
     * @return  string[]
     */
    public function paramNames(): array;

    /**
     * returns list of errors for request parameters
     *
     * @since  1.3.0
     */
    public function paramErrors(): ParamErrors;

    /**
     * checks whether a request param is set
     *
     * @since  1.3.0
     */
    public function hasParam(string $paramName): bool;

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @since  1.3.0
     */
    public function validateParam(string $paramName): ValueValidator;

    /**
     * returns request value from params for validation
     *
     * @since  1.3.0
     */
    public function readParam(string $paramName): ValueReader;
}
