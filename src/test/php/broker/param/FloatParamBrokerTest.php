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
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\FloatParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class FloatParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new FloatParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Float';
    }

    /**
     * returns expected filtered value
     *
     * @return  float
     */
    protected function getExpectedValue()
    {
        return 3.03;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(3.03,
                            $this->paramBroker->procure($this->mockRequest(null),
                                                        $this->createRequestAnnotation(['default' => 3.03])
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
    public function returnsNullIfLowerThanMinValue()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('3.03'),
                                                      $this->createRequestAnnotation(['minValue' => 4])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfGreaterThanMaxValue()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('3.03'),
                                                      $this->createRequestAnnotation(['maxValue' => 3])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertEquals(3.03,
                            $this->paramBroker->procure($this->mockRequest('3.03'),
                                                        $this->createRequestAnnotation(['minValue' => 3,
                                                                                        'maxValue' => 4
                                                                                       ]
                                                        )
                            )
        );
    }
}
