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
use stubbles\lang\reflect;
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
    protected $localizedMessage;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->localizedMessage = new LocalizedMessage('en_EN', 'This is a localized string.');
    }

    /**
     * @test
     */
    public function annotationPresentOnClass()
    {
        $this->assertTrue(
                reflect\annotationsOf($this->localizedMessage)
                        ->contain('XmlTag')
        );
    }

    /**
     * @test
     */
    public function annotationPresentOnGetLocaleMethod()
    {
        $this->assertTrue(
                reflect\annotationsOf($this->localizedMessage, 'locale')
                        ->contain('XmlAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationPresentOngetMessageMethod()
    {
        $this->assertTrue(
                reflect\annotationsOf($this->localizedMessage, 'message')
                        ->contain('XmlTag')
        );
    }

    /**
     * @test
     */
    public function localeAttributeEqualsGivenLocale()
    {
        $this->assertEquals('en_EN', $this->localizedMessage->locale());
    }

    /**
     * @test
     */
    public function contentOfStringEqualsGivenString()
    {
        $this->assertEquals('This is a localized string.',
                            $this->localizedMessage->message()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function conversionToStringYieldsMessage()
    {
        $this->assertEquals('This is a localized string.',
                            (string) $this->localizedMessage
        );
    }
}
