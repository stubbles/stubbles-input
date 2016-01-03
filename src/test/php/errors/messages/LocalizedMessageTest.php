<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  @package  stubbles\input
 */
namespace stubbles\input\errors\messages;
use function bovigo\assert\assert;
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
class LocalizedMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LocalizedMessage
     */
    private $localizedMessage;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->localizedMessage = new LocalizedMessage(
                'en_EN',
                'This is a localized string.'
        );
    }

    /**
     * @test
     */
    public function annotationPresentOnClass()
    {
        assertTrue(annotationsOf($this->localizedMessage)->contain('XmlTag'));
    }

    /**
     * @test
     */
    public function annotationPresentOnGetLocaleMethod()
    {
        assertTrue(
                annotationsOf($this->localizedMessage, 'locale')
                        ->contain('XmlAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationPresentOngetMessageMethod()
    {
        assertTrue(
                annotationsOf($this->localizedMessage, 'message')
                        ->contain('XmlTag')
        );
    }

    /**
     * @test
     */
    public function localeAttributeEqualsGivenLocale()
    {
        assert($this->localizedMessage->locale(), equals('en_EN'));
    }

    /**
     * @test
     */
    public function contentOfStringEqualsGivenString()
    {
        assert(
                $this->localizedMessage->message(),
                equals('This is a localized string.')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function conversionToStringYieldsMessage()
    {
        assert(
                (string) $this->localizedMessage,
                equals('This is a localized string.')
        );
    }
}
