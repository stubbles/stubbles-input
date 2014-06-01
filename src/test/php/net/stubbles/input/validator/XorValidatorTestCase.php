<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
require_once __DIR__ . '/CompositeValidatorTestCase.php';
/**
 * Tests for net\stubbles\input\validator\XorValidator.
 *
 * @group  validator
 */
class XorValidatorTestCase extends CompositeValidatorTestCase
{
    /**
     * creates instance to test
     *
     * @return  CompositeValidator
     */
    protected function getTestInstance()
    {
        return new XorValidator();
    }

    /**
     * @test
     */
    public function willValidateToTrueIfSingleValidatorReturnsTrue()
    {
        $this->assertTrue($this->compositeValidator->addValidator($this->createMockValidatorWhichValidatesTo(true))
                                                   ->validate('foo')
        );
    }

    /**
     * @test
     */
    public function willValidateToFalseIfSingleValidatorReturnsFalse()
    {
        $this->assertFalse($this->compositeValidator->addValidator($this->createMockValidatorWhichValidatesTo(false))
                                                    ->validate('foo')
        );
    }

    /**
     * @test
     */
    public function willValidateToTrueIfOneValidatorReturnsTrue()
    {
        $this->assertTrue($this->compositeValidator->addValidator($this->createMockValidatorWhichValidatesTo(true))
                                                   ->addValidator($this->createMockValidatorWhichValidatesTo(false))
                                                   ->validate('foo')
        );
    }

    /**
     * @test
     */
    public function willValidateToFalseIfAllValidatorsReturnFalse()
    {
        $this->assertFalse($this->compositeValidator->addValidator($this->createMockValidatorWhichValidatesTo(false))
                                                    ->addValidator($this->createMockValidatorWhichValidatesTo(false))
                                                    ->validate('foo')
        );
    }

    /**
     * @test
     */
    public function willValidateToFalseIfMoreThanOneValidatorReturnsTrue()
    {
        $this->assertFalse($this->compositeValidator->addValidator($this->createMockValidatorWhichValidatesTo(true))
                                                    ->addValidator($this->createMockValidatorWhichValidatesTo(true))
                                                    ->validate('foo')
        );
    }
}
