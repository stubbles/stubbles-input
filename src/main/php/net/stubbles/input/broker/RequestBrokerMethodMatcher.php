<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker;
use net\stubbles\input\Request;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\ReflectionMethod;
use net\stubbles\lang\reflect\matcher\MethodMatcher;
/**
 * Matcher for methods and properties.
 */
class RequestBrokerMethodMatcher extends BaseObject implements MethodMatcher
{
    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   \ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        if ($method->isPublic() === false || $method->isStatic() === true) {
            return false;
        }

        if ($method->isConstructor() === true || $method->isDestructor() === true) {
            return false;
        }

        return true;
    }

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   ReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(ReflectionMethod $method)
    {
        return $method->hasAnnotation('Filter');
    }
}
?>