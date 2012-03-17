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
/**
 * Test for net\stubbles\input\web\useragent\UserAgentFilter.
 *
 * @since  1.2.0
 * @group  web
 * @group  web_useragent
 */
class UserAgentFilterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  UserAgentFilter
     */
    private $userAgentFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->userAgentFilter = new UserAgentFilter(new UserAgentDetector());
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->userAgentFilter->getClass()
                                                ->getConstructor()
                                                ->hasAnnotation('Inject')
         );
    }

    /**
     * @test
     */
    public function executeReturnsUserAgent()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\web\\useragent\\UserAgent',
                                $this->userAgentFilter->apply(new Param('HTTP_USER_AGENT', 'a user agent'))
        );
    }
}
?>