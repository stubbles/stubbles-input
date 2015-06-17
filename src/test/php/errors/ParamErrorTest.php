<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\errors;
use stubbles\lang\reflect;
use stubbles\input\errors\messages\LocalizedMessage;
/**
 * Tests for stubbles\input\errors\ParamError.
 *
 * @group  errors
 */
class ParamErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamError
     */
    private $paramError;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramError = new ParamError('id', ['foo' => 'bar']);
    }

    /**
     * @test
     */
    public function returnsGivenId()
    {
        assertEquals('id', $this->paramError->id());
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function returnsGivenDetails()
    {
        assertEquals(['foo' => 'bar'], $this->paramError->details());
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithDetails()
    {

        assertEquals(
                [new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                 new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                ],
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                )
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithFlattenedArrayDetails()
    {
        $this->paramError = new ParamError('id', ['foo' => ['bar', 'baz']]);
        assertEquals(
                [new LocalizedMessage('en_*', 'An error of type bar, baz occurred.'),
                 new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar, baz aufgetreten.')
                ],
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                )
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithObjectDetails()
    {
        $this->paramError = new ParamError('id', ['foo' => new \stdClass()]);
        assertEquals(
                [new LocalizedMessage('en_*', 'An error of type stdClass occurred.'),
                 new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ stdClass aufgetreten.')
                ],
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                )
        );
    }

    /**
     * @test
     */
    public function doesNotReplacePlaceHolderInMessagesIfDetailsNotSet()
    {
        $this->paramError = new ParamError('id');
        assertEquals(
                [new LocalizedMessage('en_*', 'An error of type {foo} occurred.'),
                 new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ {foo} aufgetreten.')
                ],
                $this->paramError->fillMessages(
                        ['en_*'  => 'An error of type {foo} occurred.',
                         'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                        ]
                )
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        assertTrue(
                reflect\annotationsOf($this->paramError)->contain('XmlTag')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnIdMethod()
    {
        assertTrue(
                reflect\annotationsOf($this->paramError, 'id')
                        ->contain('XmlAttribute')
        );
    }
}
