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
use bovigo\callmap\NewInstance;
use stubbles\input\ValueReader;
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
     * @type  \bovigo\callmap\Proxy
     */
    private $webRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->webRequest        = NewInstance::of('stubbles\input\web\WebRequest');
        $this->userAgentProvider = new UserAgentProvider($this->webRequest);
    }

    /**
     * @test
     */
    public function providerReturnsUserAgent()
    {
        $this->webRequest->mapCalls(
                ['readHeader'  => ValueReader::forValue('foo'),
                 'cookieNames' => ['chocolateChip']
                ]
        );
        assertEquals(
                new UserAgent('foo', true),
                @$this->userAgentProvider->get()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function providerReturnsBotUserAgent()
    {
        $this->webRequest->mapCalls(
                ['readHeader'  => ValueReader::forValue('Googlebot /v1.1'),
                 'cookieNames' => []
                ]
        );
        assertEquals(
                new UserAgent('Googlebot /v1.1', false),
                @$this->userAgentProvider->get()
        );
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
