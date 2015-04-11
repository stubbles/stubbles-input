<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\console;
/**
 * Tests for stubbles\input\console\BaseConsoleRequest.
 *
 * @since  2.0.0
 * @group  console
 */
class BaseConsoleRequestTest extends \PHPUnit_Framework_TestCase
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
    private $serverBackup;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->serverBackup       = $_SERVER;
        $this->baseConsoleRequest = new BaseConsoleRequest(
                ['foo' => 'bar', 'roland' => 'TB-303'],
                ['SCRIPT_NAME' => 'example.php',
                 'PHP_SELF'    => 'example.php'
                ]
        );
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        $_SERVER = $this->serverBackup;
    }

    /**
     * @test
     */
    public function requestMethodIsAlwaysCli()
    {
        assertEquals('cli', $this->baseConsoleRequest->method());
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        assertEquals(['foo', 'roland'],
                            $this->baseConsoleRequest->paramNames()
        );
    }

    /**
     * @test
     */
    public function createFromRawSourceUsesServerArgsForParams()
    {
        $_SERVER['argv'] = ['foo' => 'bar', 'roland' => 'TB-303'];
        assertEquals(
                ['foo', 'roland'],
                BaseConsoleRequest::fromRawSource()->paramNames()
        );
    }

    /**
     * @test
     */
    public function returnsListOfEnvNames()
    {
        assertEquals(
                ['SCRIPT_NAME', 'PHP_SELF'],
                $this->baseConsoleRequest->envNames()
        );
    }

    /**
     * @test
     */
    public function returnsEnvErrors()
    {
        assertInstanceOf(
                'stubbles\input\errors\ParamErrors',
                $this->baseConsoleRequest->envErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingEnv()
    {
        assertFalse($this->baseConsoleRequest->hasEnv('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingEnv()
    {
        assertTrue($this->baseConsoleRequest->hasEnv('SCRIPT_NAME'));
    }

    /**
     * @test
     */
    public function validateEnvReturnsValueValidator()
    {
        assertInstanceOf(
                'stubbles\input\ValueValidator',
                $this->baseConsoleRequest->validateEnv('SCRIPT_NAME')
        );
    }

    /**
     * @test
     */
    public function validateEnvReturnsValueValidatorForNonExistingParam()
    {
        assertInstanceOf(
                'stubbles\input\ValueValidator',
                $this->baseConsoleRequest->validateEnv('baz')
        );
    }

    /**
     * @test
     */
    public function readEnvReturnsValueReader()
    {
        assertInstanceOf(
                'stubbles\input\ValueReader',
                $this->baseConsoleRequest->readEnv('SCRIPT_NAME')
        );
    }

    /**
     * @test
     */
    public function readEnvReturnsValueReaderForNonExistingParam()
    {
        assertInstanceOf(
                'stubbles\input\ValueReader',
                $this->baseConsoleRequest->readEnv('baz')
        );
    }

    /**
     * @test
     */
    public function createFromRawSourceUsesServerForEnv()
    {
        $_SERVER = ['argv'        => ['foo' => 'bar', 'roland' => 'TB-303'],
                    'SCRIPT_NAME' => 'example.php'
                   ];
        assertEquals(
                ['argv', 'SCRIPT_NAME'],
                BaseConsoleRequest::fromRawSource()->envNames()
        );
    }
}
