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
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\reflect\ReflectionMethod;
use stubbles\lang\reflect\matcher\MethodMatcher;
/**
 * Provides access to methods applicable for brokerage.
 *
 * @Singleton
 */
class RequestBrokerMethods implements MethodMatcher
{
    /**
     * returns all methods of given instance which are applicable for brokerage
     *
     * @param   object|string  $object
     * @param   string  $group   restrict list to given group
     * @return  ReflectionMethod[]
     * @throws  IllegalArgumentException
     */
    public function get($object, $group = null)
    {
        if (!is_object($object) && !is_string($object)) {
            throw new IllegalArgumentException('Parameter $object must be a concrete object instance or class name.');
        }

        $refClass = \stubbles\lang\reflect($object);
        $methods  = $refClass->getMethodsByMatcher($this);
        if (empty($group)) {
            return $methods;
        }

        return array_filter($methods,
                            function(ReflectionMethod $method) use ($group)
                            {
                                return $method->getAnnotation('Request')->getGroup() === $group;
                            }
        );
    }

    /**
     * returns a list of all request annotations on given object
     *
     * @param   object  $object
     * @param   string  $group   restrict list to given group
     * @return  stubbles\lang\reflect\annotation\Annotation[]
     */
    public function getAnnotations($object, $group = null)
    {
        $annotations = array();
        foreach ($this->get($object, $group) as $method) {
            /* @var $method ReflectionMethod */
             $annotation = $method->getAnnotation('Request');
             $annotation->requiresValue = ($method->getNumberOfParameters() > 0);
             $annotations[] = $annotation;
        }

        return $annotations;
    }

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
        return $method->hasAnnotation('Request');
    }
}
