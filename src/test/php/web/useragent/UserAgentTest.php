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
use stubbles\lang\reflect;
/**
 * Test for stubbles\input\web\useragent\UserAgent.
 *
 * @deprecated  since 4.4.0, will be removed with 5.0.0
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
                reflect\annotationsOf($this->userAgent)
                        ->contain('ProvidedBy')
        );
    }

    /**
     * @test
     */
    public function xmlAnnotationPresentClass()
    {
        $this->assertTrue(
                reflect\annotationsOf($this->userAgent)
                        ->contain('XmlTag')
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
                reflect\annotationsOf($this->userAgent, $method)
                        ->contain($annotation)
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
            ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
            ['Microsoft msnbot 3.2'],
            ['Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'],
            ['Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534+ (KHTML, like Gecko) BingPreview/1.0b'],
            ['Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)'],
            ['Pingdom.com_bot_version_1.4_(http://www.pingdom.com/)'],
            ['Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)']
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
