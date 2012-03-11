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
use net\stubbles\lang\types\LocalizedString;
/**
 * Tests for net\stubbles\input\error\ParamErrors.
 *
 * @group  error
 */
class ParamErrorsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamErrors
     */
    protected $paramErrors;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramErrors = new ParamErrors();
    }

    /**
     * @test
     */
    public function hasNoErrorsInitially()
    {
        $this->assertFalse($this->paramErrors->exist());
        $this->assertEquals(0, $this->paramErrors->count());
        $this->assertEquals(array(), $this->paramErrors->get());
    }

    /**
     * @test
     */
    public function addErrorForSingleRequestValue()
    {
        $paramError = new ParamError('id');
        $this->assertSame($paramError,
                          $this->paramErrors->add($paramError,
                                                   'foo'
                                               )
        );

        $this->assertTrue($this->paramErrors->exist());
        $this->assertTrue($this->paramErrors->existFor('foo'));
        $this->assertTrue($this->paramErrors->existForWithId('foo', 'id'));
        $this->assertEquals(1, $this->paramErrors->count());
        $this->assertEquals(array('foo' => array('id' => $paramError)), $this->paramErrors->get());
        $this->assertEquals(array('id' => $paramError), $this->paramErrors->getFor('foo'));
        $this->assertEquals($paramError, $this->paramErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function addSameErrorForSameValueNameDoesNotResultInTwoErrorsOfSameKind()
    {
        $paramError = new ParamError('id');
        $this->assertSame($paramError,
                          $this->paramErrors->add($paramError,
                                                   'foo'
                                               )
        );
        $this->assertSame($paramError,
                          $this->paramErrors->add($paramError,
                                                   'foo'
                                               )
        );

        $this->assertTrue($this->paramErrors->exist());
        $this->assertEquals(1, $this->paramErrors->count());
        $this->assertEquals(array('foo' => array('id' => $paramError)),
                            $this->paramErrors->get()
        );
    }

    /**
     * @test
     */
    public function existForReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function getForReturnsEmptyArrayIfNoErrorAddedBefore()
    {
        $this->assertEquals(array(), $this->paramErrors->getFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->paramErrors->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorAddedBefore()
    {
        $this->assertNull($this->paramErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorOfThisNameAddedBefore()
    {
        $paramError = new ParamError('id');
        $this->assertSame($paramError,
                          $this->paramErrors->add($paramError,
                                                   'foo'
                                               )
        );
        $this->assertFalse($this->paramErrors->existForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorOfThisNameAddedBefore()
    {
        $paramError = new ParamError('id');
        $this->assertSame($paramError,
                          $this->paramErrors->add($paramError,
                                                   'foo'
                                               )
        );
        $this->assertNull($this->paramErrors->getForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function canIterateOverParamErrors()
    {
        $paramError1 = new ParamError('id1');
        $paramError2 = new ParamError('id2');
        $this->paramErrors->add($paramError1,
                                 'foo'
                             );
        $this->paramErrors->add($paramError2,
                                 'foo'
                             );
        $this->paramErrors->add($paramError1,
                                 'bar'
                             );
        $i = 0;
        foreach ($this->paramErrors as $paramName => $paramErrors) {
            if (0 === $i) {
                $this->assertEquals('foo', $paramName);
                $this->assertEquals(array('id1' => $paramError1,
                                          'id2' => $paramError2
                                    ),
                                    $paramErrors
                );
            } else {
                $this->assertEquals('bar', $paramName);
                $this->assertEquals(array('id1' => $paramError1),
                                    $paramErrors
                );
            }

            $i++;
        }
    }
}
?>