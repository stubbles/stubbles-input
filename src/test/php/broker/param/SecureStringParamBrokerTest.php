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
use stubbles\input\Param;
use stubbles\input\ValueReader;
use stubbles\lang\SecureString;
use stubbles\lang\reflect\annotation\Annotation;
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
        $this->assertEquals(
                $expected,
                $actual->unveil()
        );
    }

    /**
     * creates request annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    protected function createRequestAnnotation(array $values = [])
    {
        $values['name'] = 'foo';
        return new Annotation('SecureString', 'foo', $values, 'Request');
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockRequest($value)
    {
        $mockRequest = $this->getMock('stubbles\input\Request');
        $mockRequest->expects($this->once())
                    ->method('readParam')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue($value)));
        return $mockRequest;
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure($this->getMock('stubbles\input\Request'),
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
                $this->paramBroker->procureParam(new Param('name', 'topsecret'),
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
                $this->paramBroker->procure($this->mockRequest('topsecret'),
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
                $this->paramBroker->procure($this->mockRequest('topsecret'),
                                            $this->createRequestAnnotation(['source' => 'param'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $mockRequest = $this->getMock('stubbles\input\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('readHeader')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue('topsecret')));
        $this->assertSecureStringEquals(
                'topsecret',
                $this->paramBroker->procure($mockRequest,
                                            $this->createRequestAnnotation(['source' => 'header'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $mockRequest = $this->getMock('stubbles\input\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('readCookie')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue('topsecret')));
        $this->assertSecureStringEquals(
                'topsecret',
                $this->paramBroker->procure($mockRequest,
                                            $this->createRequestAnnotation(['source' => 'cookie'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(['required' => true])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfShorterThanMinLength()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('Do you expect me to talk?'),
                                                      $this->createRequestAnnotation(['minLength' => 30])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('Do you expect me to talk?'),
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
                $this->paramBroker->procure($this->mockRequest('Do you expect me to talk?'),
                                            $this->createRequestAnnotation(['minLength' => 10,
                                                                            'maxLength' => 30
                                                                           ]
                                            )
                )
        );
    }
}
