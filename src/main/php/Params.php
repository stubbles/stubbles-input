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
use stubbles\values\Value;
/**
 * Wrapper for parameters and their errors.
 *
 * @since  2.0.0
 * @internal
 */
class Params implements \IteratorAggregate, \Countable
{
    /**
     * list of parameters
     *
     * @type  array
     */
    private $params;
    /**
     * list of errors for parameters
     *
     * @type  ParamErrors
     */
    private $errors;

    /**
     * constructor
     *
     * @param  array  $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     */
    public function contain(string $paramName): bool
    {
        return isset($this->params[$paramName]);
    }

    /**
     * returns raw value of parameter or null if not set
     *
     * @param   string  $paramName
     * @return  \stubbles\values\Value
     */
    public function value(string $paramName): Value
    {
        return Value::of($this->params[$paramName] ?? null);
    }

    /**
     * return an array of all param names registered in this request
     *
     * @return  string[]
     */
    public function names(): array
    {
        return array_keys($this->params);
    }

    /**
     * returns error collection for request parameters
     *
     * @return  \stubbles\input\errors\ParamErrors
     */
    public function errors(): ParamErrors
    {
        if (null === $this->errors) {
            $this->errors = new ParamErrors();
        }

        return $this->errors;
    }

    /**
     * returns number of available params
     *
     * @return  int
     */
    public function count(): int
    {
        return count($this->params);
    }

    /**
     * provides an iterator to iterate over all errors
     *
     * @link    http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return  \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->params);
    }
}
