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
 * Tests for net\stubbles\input\console\ConsoleRequest.
 *
 * @since  2.0.0
 * @group  console
 */
class ConsoleRequestTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ConsoleRequest
     */
    private $consoleRequest;
    /**
     * backup of $_SERVER['argv']
     *
     * @type array
     */
    private $args;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->args           = $_SERVER['argv'];
        $this->consoleRequest = new ConsoleRequest(array('foo' => 'bar', 'roland' => 'TB-303'));
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        $_SERVER['argv'] = $this->args;
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->consoleRequest->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('argv',
                            $constructor->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function requestMethodIsAlwaysCli()
    {
        $this->assertEquals('cli', $this->consoleRequest->getMethod());
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(array('foo', 'roland'),
                            $this->consoleRequest->getParamNames()
        );
    }

    /**
     * @test
     */
    public function createFromRawSourceUsesServerArgs()
    {
        $_SERVER['argv'] = array('foo' => 'bar', 'roland' => 'TB-303');
        $this->assertEquals(array('foo', 'roland'),
                            ConsoleRequest::fromRawSource()
                                          ->getParamNames()
        );
    }
}
?>