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
use stubbles\input\ValueReader;
use stubbles\lang\reflect;
/**
 * Test for stubbles\input\web\useragent\UserAgentProvider.
 *
 * @since  1.2.0
 * @group  web
 * @group  web_useragent
 * @deprecated  since 4.1.0, will be removed with 5.0.0
 */
class UserAgentProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  UserAgentProvider
     */
    private $userAgentProvider;
    /**
     * mocked request instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockWebRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockWebRequest    = $this->getMock('stubbles\input\web\WebRequest');
        $this->userAgentProvider = new UserAgentProvider($this->mockWebRequest);
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue(
                reflect\constructorAnnotationsOf($this->userAgentProvider)
                        ->contain('Inject')
        );
    }

    /**
     * @test
     */
    public function providerReturnsUserAgent()
    {
        $this->mockWebRequest->expects($this->once())
                             ->method('readHeader')
                             ->with($this->equalTo('HTTP_USER_AGENT'))
                             ->will($this->returnValue(ValueReader::forValue('foo')));
        $this->mockWebRequest->expects($this->once())
                             ->method('cookieNames')
                             ->will($this->returnValue(['chocolateChip']));
        $this->assertEquals(new UserAgent('foo', true), @$this->userAgentProvider->get());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function providerReturnsBotUserAgent()
    {
        $this->mockWebRequest->expects($this->once())
                             ->method('readHeader')
                             ->with($this->equalTo('HTTP_USER_AGENT'))
                             ->will($this->returnValue(ValueReader::forValue('Googlebot /v1.1')));
        $this->mockWebRequest->expects($this->once())
                             ->method('cookieNames')
                             ->will($this->returnValue([]));
        $this->assertEquals(new UserAgent('Googlebot /v1.1', false), @$this->userAgentProvider->get());
    }

    /**
     * @since  4.2.0
     * @test
     * @expectedException  PHPUnit_Framework_Error_Deprecated
     * @group  issue_66
     */
    public function providerTriggersDeprecatedWarning()
    {
        $this->userAgentProvider->get();
    }
}
