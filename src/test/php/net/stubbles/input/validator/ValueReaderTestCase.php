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
use net\stubbles\input\Param;
/**
 * Tests for net\stubbles\input\validator\ValueReader.
 *
 * @since  1.3.0
 * @group  validator
 */
class ValueReaderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * helper function to create request value instance
     *
     * @param   string  $value
     * @return  ValueReader
     */
    private function createValueReader($value)
    {
        return new ValueReader(new Param('bar', $value));
    }

    /**
     * @test
     */
    public function ifContainsReturnsValidatedValue()
    {
        $this->assertEquals('303313', $this->createValueReader('303313')->ifContains('303'));
    }

    /**
     * @test
     */
    public function ifContainsReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('303313')->ifContains('323'));
    }

    /**
     * @test
     */
    public function ifContainsReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueReader('303313')->ifContains('323', 'default')
        );
    }

    /**
     * @test
     */
    public function ifContainsReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueReader(null)->ifContains('323', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsValidatedValue()
    {
        $this->assertEquals('303313', $this->createValueReader('303313')->ifIsEqualTo('303313'));
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('303313')->ifIsEqualTo('323313'));
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueReader('303313')->ifIsEqualTo('323313', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueReader(null)->ifIsEqualTo('323313', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsValidatedValue()
    {
        $this->assertEquals('http://example.net/',
                            $this->createValueReader('http://example.net/')->ifIsHttpUri()
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsHttpUri());
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('http://example.org/',
                            $this->createValueReader('invalid')->ifIsHttpUri('http://example.org/')
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('http://example.org/',
                            $this->createValueReader(null)->ifIsHttpUri('http://example.org/')
        );
    }

    /**
     * @test
     */
    public function ifIsExistingHttpUriReturnsValidatedValue()
    {
        $this->assertEquals('http://localhost/',
                            $this->createValueReader('http://localhost/')->ifIsExistingHttpUri()
        );
    }

    /**
     * @test
     */
    public function ifIsExistingHttpUriReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsExistingHttpUri());
    }

    /**
     * @test
     */
    public function ifIsExistingHttpUriReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('http://example.org/',
                            $this->createValueReader('invalid')->ifIsExistingHttpUri('http://example.org/')
        );
    }

    /**
     * @test
     */
    public function ifIsExistingHttpUriReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('http://example.org/',
                            $this->createValueReader(null)->ifIsExistingHttpUri('http://example.org/')
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsValidatedValue()
    {
        $this->assertEquals('127.0.0.1', $this->createValueReader('127.0.0.1')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('127.0.0.1',
                            $this->createValueReader('invalid')->ifIsIpAddress('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('127.0.0.1',
                            $this->createValueReader(null)->ifIsIpAddress('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsValidatedValue()
    {
        $this->assertEquals('example@example.net', $this->createValueReader('example@example.net')->ifIsMailAddress());
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsMailAddress());
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('example@example.org',
                            $this->createValueReader('invalid')->ifIsMailAddress('example@example.org')
        );
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('example@example.org',
                            $this->createValueReader(null)->ifIsMailAddress('example@example.org')
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsValidatedValue()
    {
        $this->assertEquals('as value',
                            $this->createValueReader('as value')->ifIsOneOf(array('as value',
                                                                                  'anothervalue'
                                                                            )
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsOneOf(array('as value',
                                                                               'anothervalue'
                                                                         )
                                                               )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueReader('invalid')->ifIsOneOf(array('as value',
                                                                                 'anothervalue'
                                                                           ),
                                                                           'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueReader(null)->ifIsOneOf(array('as value',
                                                                                 'anothervalue'
                                                                           ),
                                                                           'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsValidatedValue()
    {
        $this->assertEquals('a value',
                            $this->createValueReader('a value')->ifSatisfiesRegex('/^([a-z ])+$/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('303')->ifSatisfiesRegex('/^([a-z ])+$/'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueReader('303')->ifSatisfiesRegex('/^([a-z ])+$/',
                                                                              'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueReader(null)->ifSatisfiesRegex('/^([a-z ])+$/',
                                                                              'default'
                            )
        );
    }

    /**
     * @test
     */
    public function withReturnsValidatedValue()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(true));
        $this->assertEquals('a value',
                            $this->createValueReader('a value')->withValidator($mockValidator)
        );
    }

    /**
     * @test
     */
    public function withReturnsNullIfValidatorCanNotValidateValue()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(false));
        $this->assertNull($this->createValueReader('a value')->withValidator($mockValidator));
    }

    /**
     * @test
     */
    public function withReturnsDefaultValueIfValidationFails()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(false));
        $this->assertEquals('default',
                            $this->createValueReader('a value')->withValidator($mockValidator,
                                                                                         'default'
                            )
        );
    }

    /**
     * @test
     */
    public function unsecure()
    {
        $this->assertEquals('a value', $this->createValueReader('a value')->unsecure());
    }
}
?>