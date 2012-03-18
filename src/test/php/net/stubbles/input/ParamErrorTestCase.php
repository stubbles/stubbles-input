<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input;
use net\stubbles\lang\types\LocalizedString;
/**
 * Tests for net\stubbles\input\ParamError.
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
    public function hasNoMessagesByDefault()
    {
        $this->assertEquals(array(), $this->paramError->getMessages());
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInAddedMessagesWithDetails()
    {

        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type bar occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                            ),
                            $this->paramError->setMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                 'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                           )
                                               )
                                             ->getMessages()
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInAddedMessagesWithFlattenedArrayDetails()
    {
        $this->paramError = new ParamError('id', array('foo' => array('bar', 'baz')));
        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type bar, baz occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ bar, baz aufgetreten.')
                            ),
                            $this->paramError->setMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                 'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                           )
                                               )
                                             ->getMessages()
        );
    }

    /**
     * @test
     */
    public function replacesPlaceHolderInAddedMessagesWithObjectDetails()
    {
        $this->paramError = new ParamError('id', array('foo' => new \stdClass()));
        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type stdClass occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ stdClass aufgetreten.')
                            ),
                            $this->paramError->setMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                 'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                           )
                                               )
                                             ->getMessages()
        );
    }

    /**
     * @test
     */
    public function doesNotReplacePlaceHolderInAddedMessagesIfDetailsNotSet()
    {
        $this->paramError = new ParamError('id');
        $this->assertEquals(array(new LocalizedString('en_*', 'An error of type {foo} occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ {foo} aufgetreten.')
                            ),
                            $this->paramError->setMessages(array('en_*'  => 'An error of type {foo} occurred.',
                                                                 'de_DE' => 'Es ist ein Fehler vom Typ {foo} aufgetreten.'
                                                           )
                                               )
                                             ->getMessages()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->paramError->getClass()->hasAnnotation('XmlTag'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetIdMethod()
    {
        $this->assertTrue($this->paramError->getClass()
                                            ->getMethod('getId')
                                            ->hasAnnotation('XmlAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetMessagesMethod()
    {
        $this->assertTrue($this->paramError->getClass()
                                            ->getMethod('getMessages')
                                            ->hasAnnotation('XmlTag')
        );
    }
}
?>