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
use stubbles\input\ValueReader;
use stubbles\lang\SecureString;
use stubbles\lang\reflect\annotation\Annotation;
require_once __DIR__ . '/WebRequest.php';
/**
 * Tests for stubbles\input\broker\param\SecureStringParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  3.0.0
 */
class SecureStringParamBrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new SecureStringParamBroker();
    }

    /**
     * @param  string        $expected
     * @param  SecureString  $actual
     */
    private function assertSecureStringEquals($expected, SecureString $actual)
    {
        assertEquals($expected, $actual->unveil());
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
        return new Annotation('SecureString', 'foo', $values, 'Request');
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  \bovigo\callmap\Proxy
     */
    protected function createRequest($value)
    {
        return NewInstance::of('stubbles\input\Request')
                ->mapCalls(['readParam' => ValueReader::forValue($value)]);
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure(
                NewInstance::of('stubbles\input\Request'),
                $this->createRequestAnnotation(['source' => 'foo'])
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        $this->assertSecureStringEquals(
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
        $this->assertSecureStringEquals(
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
        $this->assertSecureStringEquals(
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
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readHeader' => ValueReader::forValue('topsecret')]);
        $this->assertSecureStringEquals(
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
        $request =  NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readCookie' => ValueReader::forValue('topsecret')]);
        $this->assertSecureStringEquals(
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
                        $this->createRequestAnnotation(['required' => true])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfShorterThanMinLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(['minLength' => 30])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(['maxLength' => 10])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertSecureStringEquals(
                'Do you expect me to talk?',
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(
                                ['minLength' => 10, 'maxLength' => 30]
                        )
                )
        );
    }
}
