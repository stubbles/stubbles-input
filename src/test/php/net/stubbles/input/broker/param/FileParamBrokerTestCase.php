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
 * Tests for net\stubbles\input\broker\param\FileParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class FileParamBrokerTestCase extends MultipleSourceReaderBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new FileParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'File';
    }

    /**
     * returns expected filtered value
     *
     * @return  bool
     */
    protected function getExpectedValue()
    {
        return __FILE__;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals('/home/user/foo.txt',
                            $this->paramBroker->procure($this->mockRequest(ValueReader::mockForValue(null)),
                                                        $this->createRequestAnnotation(array('default' => '/home/user/foo.txt'))
                            )
        );
    }

    /**
     * @test
     */
    public function considersRelativeWhenBasePathGiven()
    {
        $this->assertEquals('../RequestBrokerTestCase.php',
                            $this->paramBroker->procure($this->mockRequest(ValueReader::mockForValue('../RequestBrokerTestCase.php')),
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
        $this->assertNull($this->paramBroker->procure($this->mockRequest(ValueReader::mockForValue('../RequestBrokerTestCase.php')),
                                                      $this->createRequestAnnotation(array('basePath' => __DIR__))
                          )
        );
    }
}
?>