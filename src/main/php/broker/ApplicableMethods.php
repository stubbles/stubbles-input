<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker;
use stubbles\lang\reflect\ReflectionMethod;
use stubbles\lang\reflect\matcher\MethodMatcher;
/**
 * Provides access to methods applicable for brokerage.
 */
class ApplicableMethods implements MethodMatcher
{
    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @internal
     * @param   \ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        if (!$method->isPublic() || $method->isStatic()) {
            return false;
        }

        if ($method->isConstructor() || $method->isDestructor()) {
            return false;
        }

        return true;
    }

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @internal
     * @param   \stubbles\lang\reflect\ReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(ReflectionMethod $method)
    {
        return $method->hasAnnotation('Request');
    }
}
