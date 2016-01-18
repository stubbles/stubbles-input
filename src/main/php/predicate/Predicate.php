<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\predicate;
/**
 * Evaluates if a given value fulfills a criteria.
 *
 * @api
 * @method  \stubbles\input\predicate\Predicate  and(\stubbles\input\predicate\Predicate|callable $predicate)
 * @method  \stubbles\input\predicate\Predicate  or(\stubbles\input\predicate\Predicate|callable $predicate)
 */
abstract class Predicate
{
    /**
     * casts given predicate to a predicate instance
     *
     * @param   \stubbles\input\predicate\Predicate|callable  $predicate
     * @return  \stubbles\input\predicate\Predicate
     * @throws  \InvalidArgumentException
     */
    public static function castFrom($predicate)
    {
        if ($predicate instanceof self) {
            return $predicate;
        } elseif (is_callable($predicate)) {
            return new CallablePredicate($predicate);
        }

        throw new \InvalidArgumentException(
                'Given predicate is neither a callable nor an instance of ' . __CLASS__
        );
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public abstract function test($value);

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function __invoke($value)
    {
        return $this->test($value);
    }

    /**
     * provide utility methods "and" and "or" to combine predicates
     *
     * @param   string   $method
     * @param   mixed[]  $arguments
     * @return  \stubbles\input\predicate\Predicate
     * @throws  \BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'and':
                $other= self::castFrom(isset($arguments[0]) ? $arguments[0] : null);
                return new CallablePredicate(
                        function($value) use ($other)
                        {
                            return $this->test($value) && $other->test($value);
                        }
                );

            case 'or':
                $other= self::castFrom(isset($arguments[0]) ? $arguments[0] : null);
                return new CallablePredicate(
                        function($value) use ($other)
                        {
                            return $this->test($value) || $other->test($value);
                        }
                );

            default:
                throw new \BadMethodCallException(
                        'Call to undefined method '
                        . get_class($this) . '->' . $method . '()'
                );
        }
    }
}
