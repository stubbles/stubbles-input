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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

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
 */
#[Group('errors')]
#[Group('errors_message')]
class PropertyBasedParamErrorMessagesTest extends TestCase
{
    private PropertyBasedParamErrorMessages $errorMessages;

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
        /** @var  \org\bovigo\vfs\vfsStreamDirectory  $dir1 */
        $dir1 = $root->getChild('package1/input/error');
        vfsStream::newDirectory('package2/input/error')->at($root);
        /** @var  \org\bovigo\vfs\vfsStreamDirectory  $dir2 */
        $dir2 = $root->getChild('package2/input/error');
        vfsStream::newFile('message.ini')
            ->withContent('[id]
default = An error of type {foo} occurred.
en_* = An error of type {foo} occurred.
de_DE = Es ist ein Fehler vom Typ {foo} aufgetreten.
')
            ->at($dir1);
        vfsStream::newFile('message.ini')
            ->withContent('[id2]
default = An error of type {foo} occurred.
en_* = An error of type {foo} occurred.
de_DE = Es ist ein Fehler vom Typ {foo} aufgetreten.
')
            ->at($dir2);
        return NewInstance::of(ResourceLoader::class)
            ->returns(['availableResourceUris' => [
                vfsStream::url('root/package1/input/error/message.ini'),
                vfsStream::url('root/package2/input/error/message.ini')
            ]]
        );
    }

    #[Test]
    public function hasMessagesForErrorsFromBothSources(): void
    {
        assertTrue(
            $this->errorMessages->existFor(new ParamError('id', ['foo' => 'bar']))
        );
        assertTrue(
            $this->errorMessages->existFor(new ParamError('id2', ['foo' => 'bar']))
        );
    }

    #[Test]
    public function returnsTrueOnCheckForExistingError(): void
    {
        assertTrue(
            $this->errorMessages->existFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    #[Test]
    public function returnsFalseOnCheckForNonExistingError(): void
    {
        assertFalse(
            $this->errorMessages->existFor(new ParamError('doesNotExist'))
        );
    }

    #[Test]
    public function returnsListOfLocalesForExistingError(): void
    {

        assertThat(
            $this->errorMessages->localesFor(
                new ParamError('id', ['foo' => 'bar'])
            ),
            equals(['default', 'en_*', 'de_DE'])
        );
    }

    #[Test]
    public function returnsEmptyListOfLocalesForNonExistingError(): void
    {

        assertEmptyArray(
            $this->errorMessages->localesFor(new ParamError('doesNotExist'))
        );
    }

    #[Test]
    public function returnsListOfLocalizedMessagesForExistingError(): void
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

    #[Test]
    public function returnsEmptyMessageListForNonExistingError(): void
    {

        assertEmptyArray(
            $this->errorMessages->messagesFor(new ParamError('doesNotExist'))
        );
    }

    #[Test]
    public function returnsMessageInExistingLocale(): void
    {

        assertThat(
            $this->errorMessages->messageFor(
                new ParamError('id', ['foo' => 'bar']),
                'de_DE'
            ),
            equals(new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.'))
        );
    }

    #[Test]
    public function returnsMessageInExistingBaseLocale(): void
    {

        assertThat(
            $this->errorMessages->messageFor(
                new ParamError('id', ['foo' => 'bar']),
                'en_UK'
            ),
            equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    #[Test]
    public function returnsMessageInDefaultLocale(): void
    {
        $errorMessages = new PropertyBasedParamErrorMessages(
            $this->createResourceLoader(),
            'en_*'
        );
        assertThat(
            $errorMessages->messageFor(
                new ParamError('id', ['foo' => 'bar']),
                'fr_FR'
            ),
            equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    #[Test]
    public function returnsMessageInDefaultLocaleIfNoLocaleGiven(): void
    {
        $errorMessages = new PropertyBasedParamErrorMessages(
            $this->createResourceLoader(),
            'en_*'
        );
        assertThat(
            $errorMessages->messageFor(new ParamError('id', ['foo' => 'bar'])),
            equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    /**
     * @since  8.0.1
     */
    #[Test]
    public function returnsMessageInBaseLocaleIfNoLocaleGivenAndNoSpecificLocaleMessageAvailable(): void
    {
        $errorMessages = new PropertyBasedParamErrorMessages(
            $this->createResourceLoader(),
            'en_EN'
        );
        assertThat(
            $errorMessages->messageFor(new ParamError('id', ['foo' => 'bar'])),
            equals(new LocalizedMessage('en_*', 'An error of type bar occurred.'))
        );
    }

    #[Test]
    public function returnsMessageInFallbackLocale(): void
    {

        assertThat(
            $this->errorMessages->messageFor(
                new ParamError('id', ['foo' => 'bar']),
                'fr_FR'
            ),
            equals(new LocalizedMessage('default', 'An error of type bar occurred.'))
        );
    }

    #[Test]
    public function returnsEmptyMessageForNonExistingError(): void
    {

        assertThat(
            $this->errorMessages->messageFor(
                new ParamError('doesNotExist'),
                'en_*'
            ),
            equals(new LocalizedMessage('default', ''))
        );
    }

    #[Test]
    public function annotationsPresentOnConstructor(): void
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
