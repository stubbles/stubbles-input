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
 * Tests for stubbles\input\validator\IpV6Validator.
 *
 * @since  1.7.0
 * @group  validator
 * @group  bug258
 */
class sIpV6ValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IpValidator
     */
    protected $ipV6Validator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->ipV6Validator = new IpV6Validator();
    }

    /**
     * @test
     */
    public function stringIsNoIpAndResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('foo'));
    }

    /**
     * @test
     */
    public function nullIsNoIpAndResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate(null));
    }

    /**
     * @test
     */
    public function booleansAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate(true));
        $this->assertFalse($this->ipV6Validator->validate(false));
    }

    /**
     * @test
     */
    public function singleNumbersAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate(4));
        $this->assertFalse($this->ipV6Validator->validate(6));
    }

    /**
     * @test
     */
    public function ipv4ResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('1.2.3.4'));
    }

    /**
     * @test
     */
    public function invalidIpWithMissingPartResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate(':1'));
    }

    /**
     * @test
     */
    public function invalidIpResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('::ffffff:::::a'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexquadAtStartResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('XXXX::a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexquadAtEndResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('9982::a574:382b:23c1:aa49:4592:4efe:XXXX'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexquadResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('a574::XXXX:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexDigitResultsInFalse()
    {
        $this->assertFalse($this->ipV6Validator->validate('a574::382X:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function correctIpResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('febc:a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function localhostIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('::1'));
    }

    /**
     * @test
     */
    public function shortenedIpResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('febc:a574:382b::4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function evenMoreShortenedIpResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('febc::23c1:aa49:0:0:9982'));
    }

    /**
     * @test
     */
    public function singleShortenedIpResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('febc:a574:2b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPrefixIpResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('::382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPostfixIpResultsInTrue()
    {
        $this->assertTrue($this->ipV6Validator->validate('febc:a574:382b:23c1:aa49::'));
    }
}
