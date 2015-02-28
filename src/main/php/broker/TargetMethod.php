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
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Represents a target method for which a param value should be brokered.
 *
 * @since  4.0.0
 */
class TargetMethod
{
    /**
     * method which expects the parameter value
     *
     * @type  \stubbles\lang\reflect\ReflectionMethod
     */
    private $method;
    /**
     * metadata about the param
     *
     * @type  \stubbles\lang\reflect\annotation\Annotation
     */
    private $annotation;

    /**
     * constructor
     *
     * @param  \ReflectionMethod                             $method
     * @param  \stubbles\lang\reflect\annotation\Annotation  $annotation
     */
    public function __construct(\ReflectionMethod $method, Annotation $annotation)
    {
        $this->method     = $method;
        $this->annotation = $annotation;
    }

    /**
     * returns param name
     *
     * @api
     * @return  string
     */
    public function paramName()
    {
        return $this->annotation->paramName();
    }

    /**
     * returns description of param
     *
     * @api
     * @return  string
     */
    public function paramDescription()
    {
        if ($this->annotation->hasParamDescription()) {
            return $this->annotation->paramDescription();
        }

        return null;
    }

    /**
     * returns description for the value
     *
     * @api
     * @return  string
     */
    public function valueDescription()
    {
        if ($this->annotation->hasValueByName('valueDescription')) {
            return $this->annotation->getValueByName('valueDescription');
        }

        return null;
    }

    /**
     * returns type the method expects
     *
     * @return  string
     */
    public function expectedType()
    {
        return strtolower($this->annotation->getAnnotationName());
    }

    /**
     * returns the request annotation with which the method is annotated
     *
     * @api
     * @return  \stubbles\lang\reflect\annotation\Annotation
     */
    public function annotation()
    {
        return $this->annotation;
    }

    /**
     * checks if param is required
     *
     * @api
     * @return  bool
     */
    public function isRequired()
    {
        return $this->annotation->isRequired();
    }

    /**
     * whether target method requires a parameter when called
     *
     * In some cases a request annotation could be added to methods which don't
     * require a parameter because they just flip an boolean switch internally.
     * Then the simple presence of the parameter is sufficient.
     *
     * @api
     * @return  bool
     */
    public function requiresParameter()
    {
        return $this->method->getNumberOfParameters() > 0;
    }

    /**
     * passes procured value to the instance
     *
     * @api
     * @param  object  $object  instance to invoke the method on
     * @param  mixed   $value   value to pass to the method
     */
    public function invoke($object, $value)
    {
        if (null !== $value) {
            $this->method->invoke($object, $value);
        }
    }
}
