<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
/**
 * Tests for stubbles\input\validator\HttpUriValidator.
 *
 * @group  validator
 */
class HttpUriValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  HttpUriValidator
     */
    protected $httpUrlValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->httpUrlValidator = new HttpUriValidator();
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return [[null],
                [303],
                [true],
                [false],
                [''],
                ['invalid'],
                ['ftp://example.net']
        ];
    }

    /**
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($invalid)
    {
        $this->assertFalse($this->httpUrlValidator->validate($invalid));
    }

    /**
     * @test
     */
    public function validHttpUrlValidatesToTrue()
    {
        $this->assertTrue($this->httpUrlValidator->validate('http://example.net/'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function validHttpUrlWithDnsEntryValidatesToTrue()
    {
        $this->assertTrue($this->httpUrlValidator->enableDnsCheck()
                                                 ->validate('http://localhost/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function validHttpUrlWithoutDnsEntryValidatesToFalse()
    {
        $this->assertFalse($this->httpUrlValidator->enableDnsCheck()
                                                  ->validate('http://stubbles.doesNotExist/')
        );
    }
}
