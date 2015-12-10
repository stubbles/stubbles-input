<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\errors\messages;
use bovigo\callmap\NewInstance;
use stubbles\input\errors\ParamError;
use stubbles\lang\reflect;
use stubbles\lang\ResourceLoader;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for stubbles\input\errors\messages\PropertyBasedParamErrorMessages.
 *
 * @since  1.3.0
 * @group  errors
 * @group  errors_message
 */
class PropertyBasedParamErrorMessagesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  PropertyBasedParamErrorMessages
     */
    private $errorMessages;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->errorMessages = new PropertyBasedParamErrorMessages(
                $this->createResourceLoader()
        );
    }

    /**
     *
     * @return  \bovigo\callmap\Proxy
     */
    private function createResourceLoader()
    {
        $root = vfsStream::setup();
        vfsStream::newDirectory('package1/input/error')->at($root);
        vfsStream::newDirectory('package2/input/error')->at($root);
        vfsStream::newFile('message.ini')
                 ->withContent('[id]
default = An error of type {foo} occurred.
en_* = An error of type {foo} occurred.
de_DE = Es ist ein Fehler vom Typ {foo} aufgetreten.
')
                 ->at($root->getChild('package1/input/error'));
        vfsStream::newFile('message.ini')
                 ->withContent('[id2]
default = An error of type {foo} occurred.
en_* = An error of type {foo} occurred.
de_DE = Es ist ein Fehler vom Typ {foo} aufgetreten.
')
                 ->at($root->getChild('package2/input/error'));
        return NewInstance::of(ResourceLoader::class)
                ->mapCalls(['availableResourceUris' =>
                                [vfsStream::url('root/package1/input/error/message.ini'),
                                 vfsStream::url('root/package2/input/error/message.ini')
                                ]
                           ]
        );
    }

    /**
     * @test
     */
    public function hasMessagesForErrorsFromBothSources()
    {
        assertTrue(
                $this->errorMessages->existFor(new ParamError('id', ['foo' => 'bar']))
        );
        assertTrue(
                $this->errorMessages->existFor(new ParamError('id2', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingError()
    {
        assertTrue(
                $this->errorMessages->existFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingError()
    {
        assertFalse(
                $this->errorMessages->existFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsListOfLocalesForExistingError()
    {

        assertEquals(
                ['default', 'en_*', 'de_DE'],
                $this->errorMessages->localesFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfLocalesForNonExistingError()
    {

        assertEquals(
                [],
                $this->errorMessages->localesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsListOfLocalizedMessagesForExistingError()
    {

        assertEquals(
                [new LocalizedMessage('default', 'An error of type bar occurred.'),
                 new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                 new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                ],
                $this->errorMessages->messagesFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageListForNonExistingError()
    {

        assertEquals(
                [],
                $this->errorMessages->messagesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingLocale()
    {

        assertEquals(
                new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.'),
                $this->errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'de_DE'
                )
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingBaseLocale()
    {

        assertEquals(
                new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                $this->errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'en_UK'
                )
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocale()
    {
        $errorMessages = new PropertyBasedParamErrorMessages($this->createResourceLoader(), 'en_*');
        assertEquals(
                new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                $errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'fr_FR'
                )
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocaleIfNoLocaleGiven()
    {
        $errorMessages = new PropertyBasedParamErrorMessages($this->createResourceLoader(), 'en_*');
        assertEquals(
                new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                $errorMessages->messageFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInFallbackLocale()
    {

        assertEquals(
                new LocalizedMessage('default', 'An error of type bar occurred.'),
                $this->errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'fr_FR'
                )
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageForNonExistingError()
    {

        assertEquals(
                new LocalizedMessage('default', ''),
                $this->errorMessages->messageFor(
                        new ParamError('doesNotExist'),
                        'en_*'
                )
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $annotations = reflect\annotationsOfConstructorParameter(
                'defaultLocale',
                $this->errorMessages
        );
        assertTrue($annotations->contain('Property'));
        assertEquals(
                'stubbles.locale',
                $annotations->firstNamed('Property')->getValue()
        );
    }
}
