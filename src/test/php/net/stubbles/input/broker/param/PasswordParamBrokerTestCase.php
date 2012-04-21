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
 * Tests for net\stubbles\input\broker\param\PasswordParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class PasswordParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new PasswordParamBroker();
    }

    /**
     * returns name of filter annotation
     *
     * @return  string
     */
    protected function getFilterAnnotationName()
    {
        return 'PasswordFilter';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function getExpectedFilteredValue()
    {
        return 'topsecret';
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                     $this->createFilterAnnotation(array())
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndNotRequired()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                     $this->createFilterAnnotation(array('required' => false))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfTooLessMinDiffChars()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('topsecret')),
                                                     $this->createFilterAnnotation(array('minDiffChars' => 20))
                          )
        );
    }
}
?>