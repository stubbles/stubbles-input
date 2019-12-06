<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors\messages;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use stubbles\input\errors\ParamError;
use stubbles\values\ResourceLoader;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\annotationsOfConstructorParameter;
/**
 * Tests for stubbles\input\errors\messages\PropertyBasedParamErrorMessages.
 *
 * @since  1.3.0
 * @group  errors
 * @group  errors_message
 */
class PropertyBasedParamErrorMessagesTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  PropertyBasedParamErrorMessages
     */
    private $errorMessages;

    protected function setUp(): void
    {
        $this->errorMessages = new PropertyBasedParamErrorMessages(
                $this->createResourceLoader()
        );
    }

    private function createResourceLoader(): ResourceLoader
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
                ->returns(['availableResourceUris' => [
                        vfsStream::url('root/package1/input/error/message.ini'),
                        vfsStream::url('root/package2/input/error/message.ini')
                ]]
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

        assertThat(
                $this->errorMessages->localesFor(
                        new ParamError('id', ['foo' => 'bar'])
                ),
                equals(['default', 'en_*', 'de_DE'])
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfLocalesForNonExistingError()
    {

        assertEmptyArray(
                $this->errorMessages->localesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsListOfLocalizedMessagesForExistingError()
    {

        assertThat(
                $this->errorMessages->messagesFor(new ParamError('id', ['foo' => 'bar'])),
                equals([
                        new LocalizedMessage('default', 'An error of type bar occurred.'),
                        new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                        new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                ])
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageListForNonExistingError()
    {

        assertEmptyArray(
                $this->errorMessages->messagesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingLocale()
    {

        assertThat(
                $this->errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'de_DE'
                ),
                equals(new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingBaseLocale()
    {

        assertThat(
                $this->errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'en_UK'
                ),
                equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocale()
    {
        $errorMessages = new PropertyBasedParamErrorMessages($this->createResourceLoader(), 'en_*');
        assertThat(
                $errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'fr_FR'
                ),
                equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocaleIfNoLocaleGiven()
    {
        $errorMessages = new PropertyBasedParamErrorMessages($this->createResourceLoader(), 'en_*');
        assertThat(
                $errorMessages->messageFor(new ParamError('id', ['foo' => 'bar'])),
                equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    /**
     * @test
     * @since  8.0.1
     */
    public function returnsMessageInBaseLocaleIfNoLocaleGivenAndNoSpecificLocaleMessageAvailable()
    {
        $errorMessages = new PropertyBasedParamErrorMessages($this->createResourceLoader(), 'en_EN');
        assertThat(
                $errorMessages->messageFor(new ParamError('id', ['foo' => 'bar'])),
                equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInFallbackLocale()
    {

        assertThat(
                $this->errorMessages->messageFor(
                        new ParamError('id', ['foo' => 'bar']),
                        'fr_FR'
                ),
                equals(new LocalizedMessage('default', 'An error of type bar occurred.'))
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageForNonExistingError()
    {

        assertThat(
                $this->errorMessages->messageFor(
                        new ParamError('doesNotExist'),
                        'en_*'
                ),
                equals(new LocalizedMessage('default', ''))
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $annotations = annotationsOfConstructorParameter(
                'defaultLocale',
                $this->errorMessages
        );
        assertTrue($annotations->contain('Property'));
        assertThat(
                $annotations->firstNamed('Property')->getValue(),
                equals('stubbles.locale')
        );
    }
}
