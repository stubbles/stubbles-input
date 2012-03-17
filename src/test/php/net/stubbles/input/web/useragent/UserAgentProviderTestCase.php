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
use net\stubbles\input\Param;
use net\stubbles\input\filter\ValueFilter;
/**
 * Test for net\stubbles\input\web\useragent\UserAgentProvider.
 *
 * @since  1.2.0
 * @group  web
 * @group  web_useragent
 */
class UserAgentProviderTestCase extends \PHPUnit_Framework_TestCase
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
     * filter to retrieve user agent from request
     *
     * @type  UserAgentFilter
     */
    private $userAgentFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockWebRequest    = $this->getMock('net\\stubbles\\input\\web\\WebRequest');
        $this->userAgentFilter   = new UserAgentFilter(new UserAgentDetector());
        $this->userAgentProvider = new UserAgentProvider($this->mockWebRequest, $this->userAgentFilter);
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->userAgentProvider->getClass()
                                                  ->getConstructor()
                                                  ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function providerReturnsUserAgent()
    {
        $this->mockWebRequest->expects($this->once())
                             ->method('filterHeader')
                             ->with($this->equalTo('HTTP_USER_AGENT'))
                             ->will($this->returnValue(ValueFilter::mockForValue('foo')));
        $this->assertEquals(new UserAgent('foo', false), $this->userAgentProvider->get());
    }
}
?>