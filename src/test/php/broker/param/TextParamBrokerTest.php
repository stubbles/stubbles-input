<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\TextParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class TextParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new TextParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Text';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function expectedValue(): string
    {
        return 'Do you expect me to talk?';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['default' => 'No Mr Bond, I expect you to die!']
                        )
                ),
                equals('No Mr Bond, I expect you to die!')
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
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                        $this->createRequestAnnotation(
                                ['minLength' => 10, 'maxLength' => 40]
                        )
                ),
                equals('Do you expect me to talk?')
        );
    }

    /**
     * @test
     */
    public function returnsValueWithTagsIfAllowed()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                        $this->createRequestAnnotation(
                                ['minLength'   => 10,
                                 'maxLength'   => 40,
                                 'allowedTags' => 'b, u'
                                ]
                        )
                ),
                equals('Do <u>you</u> expect me to <b>talk</b>?')
        );
    }
}
