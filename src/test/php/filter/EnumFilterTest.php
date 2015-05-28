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
use stubbles\lang\Enum;
require_once __DIR__ . '/FilterTest.php';
class ExampleEnum extends Enum
{
    public static $FOO;

    public static $BAR;

    public static function __static()
    {
        self::$FOO = new self('foo', 303);
        self::$BAR = new self('bar', 909);
    }
}
ExampleEnum::__static();
/**
 * Tests for stubbles\input\filter\EnumFilter.
 *
 * @group  filter
 * @since  5.0.0
 */
class EnumFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\filter\EnumFilter
     */
    private $enumFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->enumFilter = new EnumFilter('stubbles\input\filter\ExampleEnum');
        parent::setUp();
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function createWithNonExistingClassThrowsInvalidArgumentException()
    {
        new EnumFilter('DoesNotExist');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function createWithNonEnumClassThrowsInvalidArgumentException()
    {
        new EnumFilter('stdClass');
    }

    /**
     * @return  scalar
     */
    public function getEmptyValues()
    {
        return [[''], [null]];
    }

    /**
     * @param  scalar  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value)
    {
        assertNull($this->enumFilter->apply($this->createParam($value)));
    }

    /**
     * @return  scalar
     */
    public function validValues()
    {
        return [['foo'], ['FOO']];
    }

    /**
     * @test
     * @dataProvider  validValues
     */
    public function validParamsAreReturnedAsEnumInstance($value)
    {
        assertSame(
                ExampleEnum::$FOO,
                $this->enumFilter->apply($this->createParam($value))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidValue()
    {

        assertNull($this->enumFilter->apply($this->createParam('baz')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidValue()
    {
        $param = $this->createParam('baz');
        $this->enumFilter->apply($param);
        assertTrue($param->hasError('FIELD_NO_SELECT'));
    }

    /**
     * @test
     */
    public function asEnumReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asEnum('stubbles\input\filter\ExampleEnum'));
    }

    /**
     * @test
     */
    public function asEnumReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assertSame(
                ExampleEnum::$BAR,
                $this->readParam(null)
                        ->defaultingTo(ExampleEnum::$BAR)
                        ->asEnum('stubbles\input\filter\ExampleEnum')
        );
    }

    /**
     * @test
     */
    public function asEnumReturnsNullIfParamIsNullAndRequired()
    {
        assertNull(
                $this->readParam(null)
                        ->required()
                        ->asEnum('stubbles\input\filter\ExampleEnum')
        );
    }

    /**
     * @test
     */
    public function asEnumAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asEnum('stubbles\input\filter\ExampleEnum');
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_NO_SELECT'));
    }

    /**
     * @test
     */
    public function asEnumReturnsNullIfParamIsInvalid()
    {
        assertNull(
                $this->readParam('baz')
                        ->asEnum('stubbles\input\filter\ExampleEnum')
        );
    }

    /**
     * @test
     */
    public function asEnumAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('baz')->asEnum('stubbles\input\filter\ExampleEnum');
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asEnumReturnsValidValue()
    {
        assertSame(
                ExampleEnum::$FOO,
                $this->readParam('foo')
                        ->asEnum('stubbles\input\filter\ExampleEnum')
        );

    }
}
