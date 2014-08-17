<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\web\useragent;
use stubbles\lang;
/**
 * Test for stubbles\input\web\useragent\UserAgent.
 *
 * @since  1.2.0
 * @group  web
 * @group  web_useragent
 */
class UserAgentTest extends \PHPUnit_Framework_TestCase
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
        $this->userAgent = new UserAgent('name', false, true);
    }

    /**
     * @test
     */
    public function iocAnnotationPresentOnClass()
    {
        $this->assertTrue(
                lang\reflect($this->userAgent)->hasAnnotation('ProvidedBy')
        );
    }

    /**
     * @test
     */
    public function xmlAnnotationPresentClass()
    {
        $this->assertTrue(
                lang\reflect($this->userAgent)->hasAnnotation('XmlTag')
        );
    }

    /**
     * data provider
     *
     * @return  array
     */
    public function getXmlRelatedMethodAnnotations()
    {
        return [['name', 'XmlAttribute'],
                ['isBot', 'XmlAttribute'],
                ['acceptsCookies', 'XmlAttribute'],
                ['__toString', 'XmlIgnore']
        ];
    }

    /**
     * @test
     * @dataProvider  getXmlRelatedMethodAnnotations
     */
    public function xmlAnnotationsPresentOnMethods($method, $annotation)
    {
        $this->assertTrue(
                lang\reflect($this->userAgent, $method)->hasAnnotation($annotation)
        );
    }

    /**
     * @test
     */
    public function instanceReturnsGivenName()
    {
        $this->assertEquals('name', $this->userAgent->name());
    }

    /**
     * @test
     */
    public function castToStringReturnsName()
    {
        $this->assertEquals('name', (string) $this->userAgent);
    }

    /**
     * @test
     */
    public function instanceReturnsGivenBotSetting()
    {
        $this->assertFalse($this->userAgent->isBot());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function instanceReturnsGivenCookieAcceptanceSetting()
    {
        $this->assertTrue($this->userAgent->acceptsCookies());
    }
}
