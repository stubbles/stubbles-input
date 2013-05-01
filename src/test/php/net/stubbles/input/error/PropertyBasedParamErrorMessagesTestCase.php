<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\error;
use net\stubbles\input\ParamError;
use net\stubbles\lang\reflect\ReflectionObject;
use net\stubbles\lang\types\LocalizedString;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for net\stubbles\input\error\PropertyBasedParamErrorMessages.
 *
 * @since  1.3.0
 * @group  error
 */
class PropertyBasedParamErrorMessagesTestCase extends \PHPUnit_Framework_TestCase
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
        $mockResourceLoader  = $this->getMock('net\\stubbles\\lang\\ResourceLoader');
        $mockResourceLoader->expects($this->any())
                           ->method('getResourceUris')
                           ->will($this->returnValue(array(vfsStream::url('root/package1/input/error/message.ini'),
                                                           vfsStream::url('root/package2/input/error/message.ini')
                                                     )
                                  )
                             );
        $this->errorMessages = new PropertyBasedParamErrorMessages($mockResourceLoader);
    }

    /**
     * @test
     */
    public function hasMessagesForErrorsFromBothSources()
    {
        $this->assertTrue($this->errorMessages->existFor(new ParamError('id', array('foo' => 'bar'))));
        $this->assertTrue($this->errorMessages->existFor(new ParamError('id2', array('foo' => 'bar'))));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingError()
    {
        $this->assertTrue($this->errorMessages->existFor(new ParamError('id', array('foo' => 'bar'))));
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingError()
    {
        $this->assertFalse($this->errorMessages->existFor(new ParamError('doesNotExist')));
    }

    /**
     * @test
     */
    public function returnsListOfLocalesForExistingError()
    {

        $this->assertEquals(array('default', 'en_*', 'de_DE'),
                            $this->errorMessages->localesFor(new ParamError('id', array('foo' => 'bar')))
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfLocalesForNonExistingError()
    {

        $this->assertEquals(array(),
                            $this->errorMessages->localesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsListOfLocalizedMessagesForExistingError()
    {

        $this->assertEquals(array(new LocalizedString('default', 'An error of type bar occurred.'),
                                  new LocalizedString('en_*', 'An error of type bar occurred.'),
                                  new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.')
                            ),
                            $this->errorMessages->messagesFor(new ParamError('id', array('foo' => 'bar')))
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageListForNonExistingError()
    {

        $this->assertEquals(array(),
                            $this->errorMessages->messagesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingLocale()
    {

        $this->assertEquals(new LocalizedString('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.'),
                            $this->errorMessages->messageFor(new ParamError('id', array('foo' => 'bar')), 'de_DE')
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingBaseLocale()
    {

        $this->assertEquals(new LocalizedString('en_*', 'An error of type bar occurred.'),
                            $this->errorMessages->messageFor(new ParamError('id', array('foo' => 'bar')), 'en_UK')
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocale()
    {

        $this->assertEquals(new LocalizedString('en_*', 'An error of type bar occurred.'),
                            $this->errorMessages->setLocale('en_*')->messageFor(new ParamError('id', array('foo' => 'bar')), 'fr_FR')
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocaleIfNoLocaleGiven()
    {

        $this->assertEquals(new LocalizedString('en_*', 'An error of type bar occurred.'),
                            $this->errorMessages->setLocale('en_*')->messageFor(new ParamError('id', array('foo' => 'bar')))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInFallbackLocale()
    {

        $this->assertEquals(new LocalizedString('default', 'An error of type bar occurred.'),
                            $this->errorMessages->messageFor(new ParamError('id', array('foo' => 'bar')), 'fr_FR')
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageForNonExistingError()
    {

        $this->assertEquals(new LocalizedString('default', ''),
                            $this->errorMessages->messageFor(new ParamError('doesNotExist'), 'en_*')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue(ReflectionObject::fromInstance($this->errorMessages)
                                          ->getConstructor()
                                          ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetLocaleMethod()
    {
        $setLocaleMethod = ReflectionObject::fromInstance($this->errorMessages)
                                           ->getMethod('setLocale');
        $this->assertTrue($setLocaleMethod->hasAnnotation('Inject'));
        $this->assertTrue($setLocaleMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setLocaleMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.locale',
                            $setLocaleMethod->getAnnotation('Named')->getName()
        );
    }
}
?>