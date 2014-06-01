<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
use stubbles\lang;
use stubbles\lang\types\LocalizedString;
/**
 * Tests for stubbles\input\ParamError.
 *
 * @group  core
 */
class ParamErrorTestCase extends \PHPUnit_Framework_TestCase
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
        $this->paramError = new ParamError('id', array('foo' => 'bar'));
    }

    /**
     * @test
     */
    public function returnsGivenId()
    {
        $this->assertEquals('id', $this->paramError->getId());
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithDetails()
    {

        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type bar occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                            ),
                            $this->paramError->fillMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                  'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                            )
                                               )
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithFlattenedArrayDetails()
    {
        $this->paramError = new ParamError('id', array('foo' => array('bar', 'baz')));
        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type bar, baz occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ bar, baz aufgetreten.')
                            ),
                            $this->paramError->fillMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                  'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                            )
                                               )
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInMessagesWithObjectDetails()
    {
        $this->paramError = new ParamError('id', array('foo' => new \stdClass()));
        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type stdClass occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ stdClass aufgetreten.')
                            ),
                            $this->paramError->fillMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                 'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                            )
                                               )
        );
    }

    /**
     * @test
     */
    public function doesNotReplacePlaceHolderInMessagesIfDetailsNotSet()
    {
        $this->paramError = new ParamError('id');
        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type {foo} occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ {foo} aufgetreten.')
                            ),
                            $this->paramError->fillMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                  'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                            )
                                               )
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue(lang\reflect($this->paramError)->hasAnnotation('XmlTag'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetIdMethod()
    {
        $this->assertTrue(lang\reflect($this->paramError, 'getId')->hasAnnotation('XmlAttribute'));
    }
}
