<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\predicate {

    /**
     * negates evaluation of given predicate
     *
     * @api
     * @param   \stubbles\input\predicate\Predicate|callable  $predicate
     * @return  \stubbles\input\predicate\Predicate
     * @since   6.0.0
     */
    function not($predicate)
    {
        $predicate = Predicate::castFrom($predicate);
        return new CallablePredicate(
                function($value) use ($predicate)
                {
                    return !$predicate->test($value);
                }
        );
    }

    /**
     * creates predicate to test that $needle is contained in another value
     *
     * @api
     * @param   mixed  $needle
     * @return  \stubbles\input\predicate\Predicate
     * @since   6.0.0
     */
    function contains($needle)
    {
        return new CallablePredicate(
                function($value) use ($needle)
                {
                    if (null === $value) {
                        return is_null($needle);
                    }

                    if (is_string($value)) {
                        return false !== strpos($value, (string) $needle);
                    }

                    if (is_array($value) || $value instanceof \Traversable) {
                        foreach ($value as $element) {
                            if ($element === $needle) {
                                return true;
                            }
                        }
                    }

                    return false;
                }
        );
    }

    /**
     * creates predicate to test that something contains any of the allowed values
     *
     * @api
     * @param   array  $contained
     * @return  \stubbles\input\predicate\Predicate
     * @since   6.0.0
     */
    function containsAnyOf(array $contained)
    {
        return new CallablePredicate(
                function($value) use ($contained)
                {
                    if (!is_scalar($value) || null === $value) {
                        return false;
                    }

                    foreach ($contained as $needle) {
                        if (is_bool($needle) && $value === $needle) {
                            return true;
                        } elseif (!is_bool($needle) && ($value === $needle || false !== strpos($value, (string) $needle))) {
                            return true;
                        }
                    }

                    return false;
                }
        );
    }

    /**
     * creates predicate to test that $expected is equal to another value
     *
     * @param   scalar|null  $expected
     * @return  \stubbles\input\predicate\Predicate
     * @throws  \InvalidArgumentException
     * @since   6.0.0
     */
    function equals($expected)
    {
        if (!is_scalar($expected) && null != $expected) {
            throw new \InvalidArgumentException(
                    'Can only compare scalar values and null.'
            );
        }

        return new CallablePredicate(
                function($value) use ($expected)
                {
                    return $expected === $value;
                }
        );
    }

    /**
     * creates predicate to test that some value is one of the allowed values
     *
     * @param   array  $allowedValues
     * @return  \stubbles\input\predicate\Predicate
     * @since   6.0.0
     */
    function isOneOf(array $allowedValues)
    {
        return new CallablePredicate(
                function($value) use ($allowedValues)
                {
                    if (!is_array($value)) {
                        return in_array($value, $allowedValues);
                    }

                    foreach ($value as $val) {
                        if (!in_array($val, $allowedValues)) {
                            return false;
                        }
                    }

                    return true;
                }
        );
    }
}
