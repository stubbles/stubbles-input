<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\input\errors\messages\LocalizedMessage;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\input\errors\ParamError.
 */
#[Group('errors')]
class ParamErrorTest extends TestCase
{
    private const MSG_TEMPLATES = [
        'en_*'  => 'An error of type {foo} occurred.',
        'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
    ];

    private ParamError $paramError;

    protected function setUp(): void
    {
        $this->paramError = new ParamError('id', ['foo' => 'bar']);
    }

    #[Test]
    public function returnsGivenId(): void
    {
        assertThat($this->paramError->id(), equals('id'));
    }

    /**
     * @since  5.1.0
     */
    #[Test]
    public function returnsGivenDetails(): void
    {
        assertThat($this->paramError->details(), equals(['foo' => 'bar']));
    }

    #[Test]
    public function replacesPlaceHolderInMessagesWithDetails(): void
    {

        assertThat(
            $this->paramError->fillMessages(self::MSG_TEMPLATES),
            equals([
                new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
            ])
        );
    }

    #[Test]
    public function replacesPlaceHolderInMessagesWithFlattenedArrayDetails(): void
    {
        $this->paramError = new ParamError('id', ['foo' => ['bar', 'baz']]);
        assertThat(
            $this->paramError->fillMessages(self::MSG_TEMPLATES),
            equals([
                new LocalizedMessage('en_*', 'An error of type bar, baz occurred.'),
                new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar, baz aufgetreten.')
            ])
        );
    }

    #[Test]
    public function replacesPlaceHolderInMessagesWithObjectDetails(): void
    {
        $this->paramError = new ParamError('id', ['foo' => new \stdClass()]);
        assertThat(
                $this->paramError->fillMessages(self::MSG_TEMPLATES),
                equals([
                        new LocalizedMessage('en_*', 'An error of type stdClass occurred.'),
                        new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ stdClass aufgetreten.')
                ])
        );
    }

    #[Test]
    public function doesNotReplacePlaceHolderInMessagesIfDetailsNotSet(): void
    {
        $this->paramError = new ParamError('id');
        assertThat(
            $this->paramError->fillMessages(self::MSG_TEMPLATES),
            equals([
                new LocalizedMessage('en_*', 'An error of type {foo} occurred.'),
                new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ {foo} aufgetreten.')
            ])
        );
    }

    #[Test]
    public function annotationsPresentOnClass(): void
    {
        assertTrue(annotationsOf($this->paramError)->contain('XmlTag'));
    }

    #[Test]
    public function annotationsPresentOnIdMethod(): void
    {
        assertTrue(
            annotationsOf($this->paramError, 'id')->contain('XmlAttribute')
        );
    }
}
