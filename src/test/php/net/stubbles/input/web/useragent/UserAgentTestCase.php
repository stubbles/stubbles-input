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
 * Test for net\stubbles\input\web\useragent\UserAgent.
 *
 * @since  1.2.0
 * @group  web
 * @group  web_useragent
 */
class UserAgentTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  UserAgent
     */
    private $userAgent;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->userAgent = new UserAgent('name', false);
    }

    /**
     * @test
     */
    public function iocAnnotationPresentOnClass()
    {
        $this->assertTrue($this->userAgent->getClass()->hasAnnotation('ProvidedBy'));
    }

    /**
     * @test
     */
    public function xmlAnnotationsPresent()
    {
        $class = $this->userAgent->getClass();
        $this->assertTrue($class->hasAnnotation('XmlTag'));
        $this->assertTrue($class->getMethod('getName')->hasAnnotation('XmlAttribute'));
        $this->assertTrue($class->getMethod('isBot')->hasAnnotation('XmlAttribute'));
    }

    /**
     * @test
     */
    public function instanceReturnsGivenNAme()
    {
        $this->assertEquals('name', $this->userAgent->getName());
    }

    /**
     * @test
     */
    public function instanceReturnsGivenBotSetting()
    {
        $this->assertFalse($this->userAgent->isBot());
    }
}
?>