<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use bovigo\callmap\NewInstance;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\lang\Secret;
use stubbles\lang\reflect\annotation\Annotation;
require_once __DIR__ . '/WebRequest.php';
/**
 * Tests for stubbles\input\broker\param\PasswordParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class PasswordParamBrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new PasswordParamBroker();
    }

    /**
     * @param  string  $expectedPassword
     * @param  Secret  $actualPassword
     */
    private function assertPasswordEquals($expectedPassword, Secret $actualPassword)
    {
        assertEquals($expectedPassword, $actualPassword->unveil());
    }

    /**
     * creates request annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    protected function createRequestAnnotation(array $values = [])
    {
        $values['paramName'] = 'foo';
        return new Annotation('Password', 'foo', $values, 'Request');
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  \bovigo\callmap\Proxy
     */
    protected function createRequest($value)
    {
        return NewInstance::of(Request::class)
                ->mapCalls(['readParam' => ValueReader::forValue($value)]);
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure(
                NewInstance::of(Request::class),
                $this->createRequestAnnotation(['source' => 'foo'])
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        $this->assertPasswordEquals(
                'topsecret',
                $this->paramBroker->procureParam(
                        new Param('name', 'topsecret'),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertPasswordEquals(
                'topsecret',
                $this->paramBroker->procure(
                        $this->createRequest('topsecret'),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        $this->assertPasswordEquals(
                'topsecret',
                $this->paramBroker->procure(
                        $this->createRequest('topsecret'),
                        $this->createRequestAnnotation(['source' => 'param'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $request = NewInstance::of(WebRequest::class)
                ->mapCalls(['readHeader' => ValueReader::forValue('topsecret')]);
        $this->assertPasswordEquals(
                'topsecret',
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'header'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $request =  NewInstance::of(WebRequest::class)
                ->mapCalls(['readCookie' => ValueReader::forValue('topsecret')]);
        $this->assertPasswordEquals(
                'topsecret',
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'cookie'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndNotRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['required' => false])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfTooLessMinDiffChars()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('topsecret'),
                        $this->createRequestAnnotation(['minDiffChars' => 20])
                )
        );
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function returnsNullIfTooShort()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('topsecret'),
                        $this->createRequestAnnotation(['minLength' => 20])
                )
        );
    }
}
