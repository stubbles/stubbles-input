<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\TextParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class TextParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new TextParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Text';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): string
    {
        return 'Do you expect me to talk?';
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
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

    #[Test]
    public function returnsNullIfParamNotSetAndRequired(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['required' => true])
            )
        );
    }

    #[Test]
    public function returnsNullIfShorterThanMinLength(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                $this->createRequestAnnotation(['minLength' => 40])
            )
        );
    }

    #[Test]
    public function returnsNullIfLongerThanMaxLength(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                $this->createRequestAnnotation(['maxLength' => 10])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
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

    #[Test]
    public function returnsValueWithTagsIfAllowed(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('Do <u>you</u> expect me to <b>talk</b>?'),
                $this->createRequestAnnotation([
                    'minLength'   => 10,
                    'maxLength'   => 40,
                    'allowedTags' => 'b, u'
                ])
            ),
            equals('Do <u>you</u> expect me to <b>talk</b>?')
        );
    }
}
