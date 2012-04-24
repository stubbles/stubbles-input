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
use net\stubbles\input\filter\ValueFilter;
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\DirectoryParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class DirectoryParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new DirectoryParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Directory';
    }

    /**
     * returns expected filtered value
     *
     * @return  bool
     */
    protected function getExpectedValue()
    {
        return __DIR__;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals('/home/user',
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue(null)),
                                                        $this->createRequestAnnotation(array('default' => '/home/user'))
                            )
        );
    }

    /**
     * @test
     */
    public function considersRelativeWhenBasePathGiven()
    {
        $this->assertEquals('../',
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue('../')),
                                                        $this->createRequestAnnotation(array('basePath'      => __DIR__,
                                                                                             'allowRelative' => true
                                                                                       )
                                                        )
                            )
        );
    }
}
?>