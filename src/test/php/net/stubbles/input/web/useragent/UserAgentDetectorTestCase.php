<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\web\useragent;
/**
 * Test for net\stubbles\input\web\useragent\UserAgentDetector.
 *
 * @since  1.2.0
 * @group  web
 * @group  web_useragent
 */
class UserAgentDetectorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  UserAgentDetector
     */
    private $userAgentDetector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->userAgentDetector = new UserAgentDetector();
    }

    /**
     * @test
     */
    public function detectsGooglebotAsBot()
    {
        $userAgentString = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsMsnbotAsBot()
    {
        $userAgentString = 'msnbot/2.0b (+http://search.msn.com/msnbot.htm)';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurp1AsBot()
    {
        $userAgentString = 'Slurp.so/1.0';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurp2jAsBot()
    {
        $userAgentString = 'Slurp/2.0j';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurp2KiteHourlyAsBot()
    {
        $userAgentString = 'Slurp/2.0-KiteHourly';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurp2OwlWeeklyAsBot()
    {
        $userAgentString = 'Slurp/2.0-OwlWeekly';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurp3AuAsBot()
    {
        $userAgentString = 'Slurp/3.0-AU';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurp3AsBot()
    {
        $userAgentString = 'Mozilla/5.0 (compatible; Yahoo! Slurp/3.0; http://help.yahoo.com/help/us/ysearch/slurp)';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsSlurpWithoutVersionAsBot()
    {
        $userAgentString = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsDotbotAsBot()
    {
        $userAgentString = '"Mozilla/5.0 (compatible; DotBot/1.1; http://www.dotnetdotcom.org/, crawler@dotnetdotcom.org)';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertTrue($userAgent->isBot());
    }

    /**
     * @test
     */
    public function detectsOtherUserAgentsAreNotBots()
    {
        $userAgentString = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9) Gecko/2008052906 Firefox/3.0 (.NET CLR 3.5.30729)';
        $userAgent = $this->userAgentDetector->detect($userAgentString);
        $this->assertEquals($userAgentString, $userAgent->getName());
        $this->assertFalse($userAgent->isBot());
    }
}
?>