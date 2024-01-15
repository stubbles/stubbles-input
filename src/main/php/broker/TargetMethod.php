<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker;

use ReflectionMethod;
use stubbles\reflect\annotation\Annotation;
/**
 * Represents a target method for which a param value should be brokered.
 *
 * @since  4.0.0
 */
class TargetMethod
{
    public function __construct(
        private ReflectionMethod $method,
        private Annotation $annotation
    ) { }

    /**
     * returns param name
     *
     * @api
     */
    public function paramName(): string
    {
        return $this->annotation->paramName();
    }

    /**
     * returns description of param
     *
     * @api
     */
    public function paramDescription(): ?string
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
     */
    public function valueDescription(): ?string
    {
        if ($this->annotation->hasValueByName('valueDescription')) {
            return $this->annotation->getValueByName('valueDescription');
        }

        return null;
    }

    /**
     * returns type the method expects
     */
    public function expectedType(): string
    {
        return strtolower($this->annotation->getAnnotationName());
    }

    /**
     * returns the request annotation with which the method is annotated
     *
     * @api
     */
    public function annotation(): Annotation
    {
        return $this->annotation;
    }

    /**
     * checks if param is required
     *
     * @api
     */
    public function isRequired(): bool
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
     */
    public function requiresParameter(): bool
    {
        return $this->method->getNumberOfParameters() > 0;
    }

    /**
     * passes procured value to the instance
     *
     * @api
     */
    public function invoke(object $object, mixed $value): void
    {
        if (null !== $value) {
            $this->method->invoke($object, $value);
        }
    }
}
