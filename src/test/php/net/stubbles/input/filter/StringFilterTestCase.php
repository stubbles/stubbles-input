<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\filter\range\StringLength;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\StringFilter.
 *
 * @group  filter
 */
class StringFilterTestCase extends FilterTestCase
{
    /**
     * the instance to test
     *
     * @type  StringFilter
     */
    private $stringFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->stringFilter = new StringFilter();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsNull()
    {
        $this->assertEquals('', $this->stringFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString()
    {
        $this->assertEquals('', $this->stringFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function removesTags()
    {
        $this->assertEquals("kkk",
                            $this->stringFilter->apply($this->createParam("kkk<b>"))
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        $this->assertEquals("'kkk",
                            $this->stringFilter->apply($this->createParam("\'kkk"))
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        $this->assertEquals("cdekkk",
                            $this->stringFilter->apply($this->createParam("cde\rkkk"))
        );
    }

    /**
     * @test
     */
    public function removesLineBreaks()
    {
        $this->assertEquals("abcdekkk",
                            $this->stringFilter->apply($this->createParam("ab\ncde\nkkk"))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('', $this->createValueReader(null)->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('baz',
                            $this->createValueReader(null)->asString('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asString();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')
                               ->asString(null, new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asString(null, new StringLength(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asStringReturnsValidValue()
    {
        $this->assertEquals('foo', $this->createValueReader('foo')->asString());

    }

}
