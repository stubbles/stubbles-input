<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for stubbles\input\validator\FileValidator.
 *
 * @since  2.0.0
 * @group  validator
 * @group  filesystem
 * @deprecated  since 3.0.0, will be removed with 4.0.0
 */
class FileValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup();
        vfsStream::newDirectory('basic')
                 ->at($root);
        vfsStream::newFile('foo.txt')
                 ->at($root);
        vfsStream::newFile('bar.txt')
                 ->at($root->getChild('basic'));
    }

    /**
     * @test
     */
    public function validatesToFalseForNull()
    {
        $fileValidator = new FileValidator();
        $this->assertFalse($fileValidator->validate(null));
    }

    /**
     * @test
     */
    public function validatesToFalseForEmptyString()
    {
        $fileValidator = new FileValidator();
        $this->assertFalse($fileValidator->validate(''));
    }

    /**
     * @test
     */
    public function validatesToTrueIfRelativePathExists()
    {
        $fileValidator = new FileValidator(vfsStream::url('root/basic'));
        $this->assertTrue($fileValidator->validate('../foo.txt'));
    }

    /**
     * @test
     */
    public function validatesToFalseIfFileDoesNotExistRelatively()
    {
        $fileValidator = new FileValidator(vfsStream::url('root/basic'));
        $this->assertFalse($fileValidator->validate('foo.txt'));
    }

    /**
     * @test
     */
    public function validatesToFalseIfFileDoesNotExistGlobally()
    {
        $fileValidator = new FileValidator();
        $this->assertFalse($fileValidator->validate(__DIR__ . '/doesNotExist.txt'));
    }

    /**
     * @test
     */
    public function validatesToTrueIfFileDoesExistRelatively()
    {
        $fileValidator = new FileValidator(vfsStream::url('root/basic'));
        $this->assertTrue($fileValidator->validate('bar.txt'));
    }

    /**
     * @test
     */
    public function validatesToTrueIfFileDoesExistGlobally()
    {
        $fileValidator = new FileValidator();
        $this->assertTrue($fileValidator->validate(__FILE__));
    }

    /**
     * @test
     */
    public function validatesToFalseIfIsRelativeDir()
    {
        $fileValidator = new FileValidator(vfsStream::url('root'));
        $this->assertFalse($fileValidator->validate('basic'));
    }

    /**
     * @test
     */
    public function validatesToFalseIfIsGlobalDir()
    {
        $fileValidator = new FileValidator();
        $this->assertFalse($fileValidator->validate(__DIR__));
    }
}
