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
/**
 * Tests for net\stubbles\input\validator\MailValidator.
 *
 * @group  validator
 */
class MailValidatorTestCase extends \PHPUnit_Framework_TestCase
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
        return array(array('example@example.org'),
                     array('example.foo.bar@example.org')
        );
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
        return array(array('space in@mailadre.ss'),
                     array('fäö@mailadre.ss'),
                     array('foo@bar@mailadre.ss'),
                     array('foo&/4@mailadre.ss'),
                     array('foo..bar@mailadre.ss'),
                     array(null),
                     array(''),
                     array('xcdsfad'),
                     array('foobar@thishost.willnever.exist'),
                     array('.foo.bar@example.org'),
                     array('example@example.org\n'),
                     array('example@exa"mple.org'),
                     array('example@example.org\nBcc: example@example.com')
        );
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
     */
    public function hasNoCriteria()
    {
        $this->assertEquals(array(), $this->mailValidator->getCriteria());
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
?>