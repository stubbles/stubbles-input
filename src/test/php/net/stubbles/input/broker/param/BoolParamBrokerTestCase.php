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
 * Tests for net\stubbles\input\broker\param\BoolParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class BoolParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new BoolParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Bool';
    }

    /**
     * returns expected filtered value
     *
     * @return  bool
     */
    protected function getExpectedValue()
    {
        return true;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertTrue($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(array('default' => true))
                          )
        );
    }
}
?>