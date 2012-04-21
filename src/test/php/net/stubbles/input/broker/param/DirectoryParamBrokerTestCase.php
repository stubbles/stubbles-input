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
use net\stubbles\input\validator\ValueReader;
require_once __DIR__ . '/MultipleSourceReaderBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\DirectoryParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class DirectoryParamBrokerTestCase extends MultipleSourceReaderBrokerTestCase
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
                            $this->paramBroker->handle($this->mockRequest(ValueReader::mockForValue(null)),
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
                            $this->paramBroker->handle($this->mockRequest(ValueReader::mockForValue('../')),
                                                       $this->createRequestAnnotation(array('basePath'      => __DIR__,
                                                                                            'allowRelative' => true
                                                                                      )
                                                       )
                            )
        );
    }

    /**
     * @test
     */
    public function doesNotAllowRelativeByDefault()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueReader::mockForValue('../')),
                                                     $this->createRequestAnnotation(array('basePath' => __DIR__))
                          )
        );
    }
}
?>