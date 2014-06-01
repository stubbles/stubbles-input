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
 * Tests for stubbles\input\broker\param\IntegerParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class IntegerParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new IntegerParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Integer';
    }

    /**
     * returns expected filtered value
     *
     * @return  int
     */
    protected function getExpectedValue()
    {
        return 303;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(303,
                            $this->paramBroker->procure($this->mockRequest(null),
                                                        $this->createRequestAnnotation(['default' => 303])
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
        $this->assertNull($this->paramBroker->procure($this->mockRequest('303'),
                                                      $this->createRequestAnnotation(['minValue' => 400])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfGreaterThanMaxValue()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('303'),
                                                      $this->createRequestAnnotation(['maxValue' => 300])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertEquals(303,
                            $this->paramBroker->procure($this->mockRequest('303'),
                                                        $this->createRequestAnnotation(['minValue' => 300,
                                                                                        'maxValue' => 400
                                                                                       ]
                                                        )
                            )
        );
    }
}
