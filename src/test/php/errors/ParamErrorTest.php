<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors;
use PHPUnit\Framework\TestCase;
use stubbles\input\errors\messages\LocalizedMessage;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\input\errors\ParamError.
 *
 * @group  errors
 */
class ParamErrorTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  ParamError
     */
    private $paramError;

    protected function setUp(): void
    {
        $this->paramError = new ParamError('id', ['foo' => 'bar']);
    }

    /**
     * @test
     */
    public function returnsGivenId(): void
    {
        assertThat($this->paramError->id(), equals('id'));
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function returnsGivenDetails(): void
    {
        assertThat($this->paramError->details(), equals(['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithDetails(): void
    {

        assertThat(
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                ),
                equals([
                        new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                        new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                ])
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithFlattenedArrayDetails(): void
    {
        $this->paramError = new ParamError('id', ['foo' => ['bar', 'baz']]);
        assertThat(
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                ),
                equals([
                        new LocalizedMessage('en_*', 'An error of type bar, baz occurred.'),
                        new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar, baz aufgetreten.')
                ])
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithObjectDetails(): void
    {
        $this->paramError = new ParamError('id', ['foo' => new \stdClass()]);
        assertThat(
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                ),
                equals([
                        new LocalizedMessage('en_*', 'An error of type stdClass occurred.'),
                        new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ stdClass aufgetreten.')
                ])
        );
    }

    /**
     * @test
     */
    public function doesNotReplacePlaceHolderInMessagesIfDetailsNotSet(): void
    {
        $this->paramError = new ParamError('id');
        assertThat(
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                ),
                equals([
                        new LocalizedMessage('en_*', 'An error of type {foo} occurred.'),
                        new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ {foo} aufgetreten.')
                ])
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass(): void
    {
        assertTrue(annotationsOf($this->paramError)->contain('XmlTag'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnIdMethod(): void
    {
        assertTrue(
                annotationsOf($this->paramError, 'id')->contain('XmlAttribute')
        );
    }
}
