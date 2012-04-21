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
require_once __DIR__ . '/MultipleSourceFilterBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\TextParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class TextParamBrokerTestCase extends MultipleSourceFilterBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new TextParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Text';
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
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                       $this->createRequestAnnotation(array('default' => 'No Mr Bond, I expect you to die!'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                     $this->createRequestAnnotation(array('required' => true))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfShorterThanMinLength()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('Do <u>you</u> expect me to <b>talk</b>?')),
                                                     $this->createRequestAnnotation(array('minLength' => 40))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('Do <u>you</u> expect me to <b>talk</b>?')),
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
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('Do <u>you</u> expect me to <b>talk</b>?')),
                                                       $this->createRequestAnnotation(array('minLength' => 10,
                                                                                           'maxLength' => 40
                                                                                     )
                                                       )
                            )
        );
    }

    /**
     * @test
     */
    public function returnsValueWithTagsIfAllowed()
    {
        $this->assertEquals('Do <u>you</u> expect me to <b>talk</b>?',
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('Do <u>you</u> expect me to <b>talk</b>?')),
                                                       $this->createRequestAnnotation(array('minLength'   => 10,
                                                                                           'maxLength'   => 40,
                                                                                           'allowedTags' => 'b, u'
                                                                                     )
                                                       )
                            )
        );
    }
}
?>