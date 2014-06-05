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
use stubbles\input\errors\ParamError;
use stubbles\lang;
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
        $mockResourceLoader  = $this->getMock('stubbles\lang\ResourceLoader');
        $mockResourceLoader->expects($this->any())
                           ->method('availableResourceUris')
                           ->will($this->returnValue([vfsStream::url('root/package1/input/error/message.ini'),
                                                      vfsStream::url('root/package2/input/error/message.ini')
                                                     ]
                                  )
                             );
        $this->errorMessages = new PropertyBasedParamErrorMessages($mockResourceLoader);
    }

    /**
     * @test
     */
    public function hasMessagesForErrorsFromBothSources()
    {
        $this->assertTrue($this->errorMessages->existFor(new ParamError('id', ['foo' => 'bar'])));
        $this->assertTrue($this->errorMessages->existFor(new ParamError('id2', ['foo' => 'bar'])));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingError()
    {
        $this->assertTrue($this->errorMessages->existFor(new ParamError('id', ['foo' => 'bar'])));
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

        $this->assertEquals(['default', 'en_*', 'de_DE'],
                            $this->errorMessages->localesFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfLocalesForNonExistingError()
    {

        $this->assertEquals([],
                            $this->errorMessages->localesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsListOfLocalizedMessagesForExistingError()
    {

        $this->assertEquals([new LocalizedMessage('default', 'An error of type bar occurred.'),
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

        $this->assertEquals([],
                            $this->errorMessages->messagesFor(new ParamError('doesNotExist'))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingLocale()
    {

        $this->assertEquals(new LocalizedMessage('de_DE', 'Es ist ein Fehler vom Typ bar aufgetreten.'),
                            $this->errorMessages->messageFor(new ParamError('id', ['foo' => 'bar']), 'de_DE')
        );
    }

    /**
     * @test
     */
    public function returnsMessageInExistingBaseLocale()
    {

        $this->assertEquals(new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                            $this->errorMessages->messageFor(new ParamError('id', ['foo' => 'bar']), 'en_UK')
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocale()
    {

        $this->assertEquals(new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                            $this->errorMessages->setLocale('en_*')->messageFor(new ParamError('id', ['foo' => 'bar']), 'fr_FR')
        );
    }

    /**
     * @test
     */
    public function returnsMessageInDefaultLocaleIfNoLocaleGiven()
    {

        $this->assertEquals(new LocalizedMessage('en_*', 'An error of type bar occurred.'),
                            $this->errorMessages->setLocale('en_*')->messageFor(new ParamError('id', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function returnsMessageInFallbackLocale()
    {

        $this->assertEquals(new LocalizedMessage('default', 'An error of type bar occurred.'),
                            $this->errorMessages->messageFor(new ParamError('id', ['foo' => 'bar']), 'fr_FR')
        );
    }

    /**
     * @test
     */
    public function returnsEmptyMessageForNonExistingError()
    {

        $this->assertEquals(new LocalizedMessage('default', ''),
                            $this->errorMessages->messageFor(new ParamError('doesNotExist'), 'en_*')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue(lang\reflectConstructor($this->errorMessages)->hasAnnotation('Inject'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetLocaleMethod()
    {
        $setLocaleMethod = lang\reflect($this->errorMessages, 'setLocale');
        $this->assertTrue($setLocaleMethod->hasAnnotation('Inject'));
        $this->assertTrue($setLocaleMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setLocaleMethod->hasAnnotation('Property'));
        $this->assertEquals('stubbles.locale',
                            $setLocaleMethod->getAnnotation('Property')->getValue()
        );
    }
}
