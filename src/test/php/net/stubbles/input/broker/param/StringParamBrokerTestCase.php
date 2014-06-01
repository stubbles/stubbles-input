<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker\param;
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\StringParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class StringParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new StringParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'String';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function getExpectedValue()
    {
        return 'Do you expect me to talk?';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals('No Mr Bond, I expect you to die!',
                            $this->paramBroker->procure($this->mockRequest(null),
                                                        $this->createRequestAnnotation(array('default' => 'No Mr Bond, I expect you to die!'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(array('required' => true))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfShorterThanMinLength()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('Do you expect me to talk?'),
                                                      $this->createRequestAnnotation(array('minLength' => 30))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('Do you expect me to talk?'),
                                                      $this->createRequestAnnotation(array('maxLength' => 10))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertEquals('Do you expect me to talk?',
                            $this->paramBroker->procure($this->mockRequest('Do you expect me to talk?'),
                                                        $this->createRequestAnnotation(array('minLength' => 10,
                                                                                             'maxLength' => 30
                                                                                       )
                                                        )
                            )
        );
    }
}
