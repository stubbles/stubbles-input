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
        $this->userAgent = new UserAgent('name', true);
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
     * @return  array
     */
    public function botsRecognizedByDefault()
    {
        return [
            ['Googlebot /v1.1'],
            ['Microsoft msnbot 3.2'],
            ['Yahoo! Slurp'],
            ['Some DotBot I do not remember']
        ];
    }

    /**
     * @since  4.1.0
     * @test
     * @dataProvider  botsRecognizedByDefault
     */
    public function recognizesSomeBotsByDefault($userAgent)
    {
        $userAgent = new UserAgent($userAgent, true);
        $this->assertTrue($userAgent->isBot());
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
