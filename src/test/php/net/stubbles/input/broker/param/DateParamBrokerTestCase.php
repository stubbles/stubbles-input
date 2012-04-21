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
use net\stubbles\lang\types\Date;
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\DateParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class DateParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new DateParamBroker();
    }

    /**
     * returns name of filter annotation
     *
     * @return  string
     */
    protected function getFilterAnnotationName()
    {
        return 'DateFilter';
    }

    /**
     * returns expected filtered value
     *
     * @return  float
     */
    protected function getExpectedFilteredValue()
    {
        return new Date('2012-04-21 00:00:00+02:00');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(new Date('2012-04-21'),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                       $this->createFilterAnnotation(array('default' => '2012-04-21'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                     $this->createFilterAnnotation(array('required' => true))
                          )
        );
    }
}
?>