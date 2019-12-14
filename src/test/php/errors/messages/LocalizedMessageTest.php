<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors\messages;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\input\errors\messages\LocalizedMessage.
 *
 * @since  3.0.0
 * @group  errors
 * @group  errors_message
 */
class LocalizedMessageTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  LocalizedMessage
     */
    private $localizedMessage;

    protected function setUp(): void
    {
        $this->localizedMessage = new LocalizedMessage(
                'en_EN',
                'This is a localized string.'
        );
    }

    /**
     * @test
     */
    public function annotationPresentOnClass(): void
    {
        assertTrue(annotationsOf($this->localizedMessage)->contain('XmlTag'));
    }

    /**
     * @test
     */
    public function annotationPresentOnGetLocaleMethod(): void
    {
        assertTrue(
                annotationsOf($this->localizedMessage, 'locale')
                        ->contain('XmlAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationPresentOngetMessageMethod(): void
    {
        assertTrue(
                annotationsOf($this->localizedMessage, 'message')
                        ->contain('XmlTag')
        );
    }

    /**
     * @test
     */
    public function localeAttributeEqualsGivenLocale(): void
    {
        assertThat($this->localizedMessage->locale(), equals('en_EN'));
    }

    /**
     * @test
     */
    public function contentOfStringEqualsGivenString(): void
    {
        assertThat(
                $this->localizedMessage->message(),
                equals('This is a localized string.')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function conversionToStringYieldsMessage(): void
    {
        assertThat(
                (string) $this->localizedMessage,
                equals('This is a localized string.')
        );
    }
}
