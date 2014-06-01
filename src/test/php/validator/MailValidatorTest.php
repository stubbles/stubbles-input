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
 * Tests for stubbles\input\validator\MailValidator.
 *
 * @group  validator
 */
class MailValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MailValidator
     */
    protected $mailValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mailValidator = new MailValidator();
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return [['example@example.org'],
                ['example.foo.bar@example.org']
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->mailValidator->validate($value));
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return [['space in@mailadre.ss'],
                ['fäö@mailadre.ss'],
                ['foo@bar@mailadre.ss'],
                ['foo&/4@mailadre.ss'],
                ['foo..bar@mailadre.ss'],
                [null],
                [''],
                ['xcdsfad'],
                ['foobar@thishost.willnever.exist'],
                ['.foo.bar@example.org'],
                ['example@example.org\n'],
                ['example@exa"mple.org'],
                ['example@example.org\nBcc: example@example.com']
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->mailValidator->validate($value));
    }

    /**
     * @test
     * @group  bug223
     * @link  http://stubbles.net/ticket/223
     */
    public function validatesIndependendOfLowerOrUpperCase()
    {
        $this->assertTrue($this->mailValidator->validate('Example@example.ORG'));
        $this->assertTrue($this->mailValidator->validate('Example.Foo.Bar@EXAMPLE.org'));
    }
}
