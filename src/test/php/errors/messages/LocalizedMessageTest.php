<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors\messages;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\input\errors\messages\LocalizedMessage.
 *
 * @since  3.0.0
 */
#[Group('errors')]
#[Group('errors_message')]
class LocalizedMessageTest extends TestCase
{
    private const TEST_STRING = 'This is a localized string.';

    private LocalizedMessage $localizedMessage;

    protected function setUp(): void
    {
        $this->localizedMessage = new LocalizedMessage(
            'en_EN',
            self::TEST_STRING
        );
    }

    #[Test]
    public function annotationPresentOnClass(): void
    {
        assertTrue(annotationsOf($this->localizedMessage)->contain('XmlTag'));
    }

    #[Test]
    public function annotationPresentOnGetLocaleMethod(): void
    {
        assertTrue(
            annotationsOf($this->localizedMessage, 'locale')
                ->contain('XmlAttribute')
        );
    }

    #[Test]
    public function annotationPresentOngetMessageMethod(): void
    {
        assertTrue(
            annotationsOf($this->localizedMessage, 'message')
                ->contain('XmlTag')
        );
    }

    #[Test]
    public function localeAttributeEqualsGivenLocale(): void
    {
        assertThat($this->localizedMessage->locale(), equals('en_EN'));
    }

    #[Test]
    public function contentOfStringEqualsGivenString(): void
    {
        assertThat(
            $this->localizedMessage->message(),
            equals(self::TEST_STRING)
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function conversionToStringYieldsMessage(): void
    {
        assertThat(
            (string) $this->localizedMessage,
            equals(self::TEST_STRING)
        );
    }
}
