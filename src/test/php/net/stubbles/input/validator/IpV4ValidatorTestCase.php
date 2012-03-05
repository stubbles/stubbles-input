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
 * Tests for net\stubbles\input\validator\IpV4Validator.
 *
 * @since  1.7.0
 * @group  validator
 * @group  bug258
 */
class IpV4ValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IpV4Validator
     */
    protected $ipV4Validator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->ipV4Validator = new IpV4Validator();
    }

    /**
     * @test
     */
    public function stringIsNoIpAndResultsInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate('foo'));
    }

    /**
     * @test
     */
    public function nullIsNoIpAndResultsInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate(null));
    }

    /**
     * @test
     */
    public function booleansAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate(true));
        $this->assertFalse($this->ipV4Validator->validate(false));
    }

    /**
     * @test
     */
    public function singleNumbersAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate(4));
        $this->assertFalse($this->ipV4Validator->validate(6));
    }

    /**
     * @test
     */
    public function invalidIpWithMissingPartResultsInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate('255.55.55'));
    }

    /**
     * @test
     */
    public function invalidIpWithSuperflousPartResultsInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate('111.222.333.444.555'));
    }

    /**
     * @test
     */
    public function invalidIpWithMissingNumberResultsInFalse()
    {
        $this->assertFalse($this->ipV4Validator->validate('1..3.4'));
    }

    /**
     * @test
     */
    public function greatestIpV4ResultsInTrue()
    {
        $this->assertTrue($this->ipV4Validator->validate('255.255.255.255'));
    }

    /**
     * @test
     */
    public function lowestIpV4ResultsInTrue()
    {
        $this->assertTrue($this->ipV4Validator->validate('0.0.0.0'));
    }

    /**
     * @test
     */
    public function correctIpResultsInTrue()
    {
        $this->assertTrue($this->ipV4Validator->validate('1.2.3.4'));
    }

    /**
     * @test
     */
    public function hasNoCriteria()
    {
        $this->assertEquals(array(), $this->ipV4Validator->getCriteria());
    }
}
?>