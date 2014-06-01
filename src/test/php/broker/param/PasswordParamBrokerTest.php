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
 * Tests for stubbles\input\broker\param\PasswordParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class PasswordParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new PasswordParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Password';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function getExpectedValue()
    {
        return 'topsecret';
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation()
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndNotRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(['required' => false])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfTooLessMinDiffChars()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('topsecret'),
                                                      $this->createRequestAnnotation(['minDiffChars' => 20])
                          )
        );
    }
}
