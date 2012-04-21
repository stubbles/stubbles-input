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
use net\stubbles\peer\http\HttpUri;
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\HttpUriParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class HttpUriParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new HttpUriParamBroker();
    }

    /**
     * returns name of filter annotation
     *
     * @return  string
     */
    protected function getFilterAnnotationName()
    {
        return 'HttpUriFilter';
    }

    /**
     * returns expected filtered value
     *
     * @return  HttpUri
     */
    protected function getExpectedFilteredValue()
    {
        return HttpUri::fromString('http://localhost/');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                       $this->createFilterAnnotation(array('default' => 'http://localhost/'))
                            )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfDnsCheckEnabledAndSuccessful()
    {
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('http://localhost/')),
                                                       $this->createFilterAnnotation(array('dnsCheck' => true))
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

    /**
     * @test
     */
    public function returnsNullForInvalidHttpUri()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('invalid')),
                                                     $this->createFilterAnnotation(array())
                          )
        );
    }
}
?>