<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\console;
/**
 * Tests for net\stubbles\input\console\BaseConsoleRequest.
 *
 * @since  2.0.0
 * @group  console
 */
class BaseConsoleRequestTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  BaseConsoleRequest
     */
    private $baseConsoleRequest;
    /**
     * backup of $_SERVER['argv']
     *
     * @type array
     */
    private $_serverBackup;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->_serverBackup      = $_SERVER;
        $this->baseConsoleRequest = new BaseConsoleRequest(array('foo' => 'bar', 'roland' => 'TB-303'),
                                                           array('SCRIPT_NAME' => 'example.php',
                                                                 'PHP_SELF'    => 'example.php'
                                                           )
                                    );
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        $_SERVER = $this->_serverBackup;
    }

    /**
     * @test
     */
    public function requestMethodIsAlwaysCli()
    {
        $this->assertEquals('cli', $this->baseConsoleRequest->getMethod());
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(array('foo', 'roland'),
                            $this->baseConsoleRequest->getParamNames()
        );
    }

    /**
     * @test
     */
    public function createFromRawSourceUsesServerArgsForParams()
    {
        $_SERVER['argv'] = array('foo' => 'bar', 'roland' => 'TB-303');
        $this->assertEquals(array('foo', 'roland'),
                            BaseConsoleRequest::fromRawSource()
                                              ->getParamNames()
        );
    }

    /**
     * @test
     */
    public function returnsListOfEnvNames()
    {
        $this->assertEquals(array('SCRIPT_NAME', 'PHP_SELF'),
                            $this->baseConsoleRequest->getEnvNames()
        );
    }

    /**
     * @test
     */
    public function returnsEnvErrors()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ParamErrors',
                                $this->baseConsoleRequest->envErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingEnv()
    {
        $this->assertFalse($this->baseConsoleRequest->hasEnv('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingEnv()
    {
        $this->assertTrue($this->baseConsoleRequest->hasEnv('SCRIPT_NAME'));
    }

    /**
     * @test
     */
    public function validateEnvReturnsValueValidator()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ValueValidator',
                                $this->baseConsoleRequest->validateEnv('SCRIPT_NAME')
        );
    }

    /**
     * @test
     */
    public function validateEnvReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ValueValidator',
                                $this->baseConsoleRequest->validateEnv('baz')
        );
    }

    /**
     * @test
     */
    public function readEnvReturnsValueReader()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ValueReader',
                                $this->baseConsoleRequest->readEnv('SCRIPT_NAME')
        );
    }

    /**
     * @test
     */
    public function readEnvReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ValueReader',
                                $this->baseConsoleRequest->readEnv('baz')
        );
    }

    /**
     * @test
     */
    public function createFromRawSourceUsesServerForEnv()
    {
        $_SERVER = array('argv'        => array('foo' => 'bar', 'roland' => 'TB-303'),
                         'SCRIPT_NAME' => 'example.php'
                   );
        $this->assertEquals(array('argv', 'SCRIPT_NAME'),
                            BaseConsoleRequest::fromRawSource()
                                              ->getEnvNames()
        );
    }
}
?>