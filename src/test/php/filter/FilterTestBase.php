<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use PHPUnit\Framework\TestCase;
use stubbles\input\ValueReader;
use stubbles\input\errors\ParamErrors;
use stubbles\values\Value;
/**
 * Base class for tests of stubbles\input\Filter instances.
 *
 * @since  2.0.0
 */
abstract class FilterTestBase extends TestCase
{
    protected ParamErrors $paramErrors;

    protected function setUp(): void
    {
        $this->paramErrors = new ParamErrors();
    }

    protected function createParam(mixed $value): Value
    {
        return Value::of($value);
    }

    /**
     * helper function to create request value instance
     */
    protected function readParam(mixed $value): ValueReader
    {
        return $this->read(Value::of($value));
    }

    /**
     * helper function to create request value instance
     */
    protected function read(Value $value): ValueReader
    {
        return new ValueReader($this->paramErrors, 'bar', $value);
    }
}
