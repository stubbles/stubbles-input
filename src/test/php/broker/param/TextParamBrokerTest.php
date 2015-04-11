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
 * Tests for stubbles\input\broker\param\TextParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class TextParamBrokerTest extends MultipleSourceParamBrokerTest
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
    protected function expectedValue()
    {
        return 'Do you expect me to talk?';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertEquals(
                'No Mr Bond, I expect you to die!',
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['default' => 'No Mr Bond, I expect you to die!']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['required' => true])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfShorterThanMinLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                        $this->createRequestAnnotation(['minLength' => 40])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                        $this->createRequestAnnotation(['maxLength' => 10])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        assertEquals(
                'Do you expect me to talk?',
                $this->paramBroker->procure(
                        $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                        $this->createRequestAnnotation(
                                ['minLength' => 10, 'maxLength' => 40]
                        )
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueWithTagsIfAllowed()
    {
        assertEquals(
                'Do <u>you</u> expect me to <b>talk</b>?',
                $this->paramBroker->procure(
                        $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                        $this->createRequestAnnotation(
                                ['minLength'   => 10,
                                 'maxLength'   => 40,
                                 'allowedTags' => 'b, u'
                                ]
                        )
                )
        );
    }
}
