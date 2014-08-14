<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\lang\SecureString;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\PasswordFilter.
 *
 * @group  filter
 */
class PasswordFilterTest extends FilterTest
{
    /**
     * the instance to test
     *
     * @type  PasswordFilter
     */
    private $passwordFilter;
    /**
     * mocked password checker
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPasswordChecker;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->mockPasswordChecker = $this->getMock('stubbles\input\filter\PasswordChecker');
        $this->passwordFilter = new PasswordFilter($this->mockPasswordChecker);
        parent::setUp();
    }

    /**
     * @param  string        $expectedPassword
     * @param  SecureString  $actualPassword
     */
    private function assertPasswordEquals($expectedPassword, SecureString $actualPassword)
    {
        $this->assertEquals(
                $expectedPassword,
                $actualPassword->unveil()
        );
    }

    /**
     * @test
     */
    public function value()
    {
        $this->mockPasswordChecker->expects($this->any())
                                  ->method('check')
                                  ->will($this->returnValue([]));
        $this->assertPasswordEquals('foo', $this->passwordFilter->apply($this->createParam('foo')));
        $this->assertPasswordEquals('425%$%"�$%t 32', $this->passwordFilter->apply($this->createParam('425%$%"�$%t 32')));
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        $this->mockPasswordChecker->expects($this->never())
                                  ->method('check');
        $this->assertNull($this->passwordFilter->apply($this->createParam(null)));

    }

    /**
     * @test
     */
    public function returnsNullForEmptyString()
    {
        $this->mockPasswordChecker->expects($this->never())
                                  ->method('check');
        $this->assertNull($this->passwordFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function returnsPasswordIfBothArrayValuesAreEqual()
    {
        $this->mockPasswordChecker->expects($this->once())
                                  ->method('check')
                                  ->will($this->returnValue([]));
        $this->assertPasswordEquals(
                'foo',
                $this->passwordFilter->apply($this->createParam(['foo', 'foo']))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBothArrayValuesAreDifferent()
    {
        $this->mockPasswordChecker->expects($this->never())
                                  ->method('check');
        $this->assertNull($this->passwordFilter->apply($this->createParam(['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenBothArrayValuesAreDifferent()
    {
        $this->mockPasswordChecker->expects($this->never())
                                  ->method('check');
        $param = $this->createParam(['foo', 'bar']);
        $this->passwordFilter->apply($param);
        $this->assertTrue($param->hasError('PASSWORDS_NOT_EQUAL'));
    }

    /**
     * @test
     */
    public function returnsNullIfCheckerReportsErrors()
    {
        $this->mockPasswordChecker->expects($this->once())
                                  ->method('check')
                                  ->will($this->returnValue(['PASSWORD_TOO_SHORT' => ['minLength' => 8]]));
        $this->assertNull($this->passwordFilter->apply($this->createParam('bar')));
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueIsNotAllowed()
    {
        $this->mockPasswordChecker->expects($this->once())
                                  ->method('check')
                                  ->will($this->returnValue(['PASSWORD_TOO_SHORT' => ['minLength' => 8]]));
        $param = $this->createParam('bar');
        $this->passwordFilter->apply($param);
        $this->assertTrue($param->hasError('PASSWORD_TOO_SHORT'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asPassword($this->mockPasswordChecker));
    }

    /**
     * @test
     * @expectedException  BadMethodCallException
     */
    public function asPasswordWithDefaultValueThrowsBadMethodCallException()
    {
        $this->createValueReader(null)->defaultingTo('secret')->asPassword($this->mockPasswordChecker);
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asPassword($this->mockPasswordChecker);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsInvalid()
    {
        $this->mockPasswordChecker->expects($this->once())
                                  ->method('check')
                                  ->will($this->returnValue(['PASSWORD_TOO_SHORT' => ['minLength' => 8]]));
        $this->assertNull($this->createValueReader('foo')->asPassword($this->mockPasswordChecker));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsInvalid()
    {
        $this->mockPasswordChecker->expects($this->once())
                                  ->method('check')
                                  ->will($this->returnValue(['PASSWORD_TOO_SHORT' => ['minLength' => 8]]));
        $this->createValueReader('foo')->asPassword($this->mockPasswordChecker);
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asPasswordReturnsValidValue()
    {
        $this->mockPasswordChecker->expects($this->once())
                                  ->method('check')
                                  ->will($this->returnValue([]));
        $this->assertPasswordEquals(
                'abcde',
                $this->createValueReader('abcde')->asPassword($this->mockPasswordChecker)
        );
    }
}
