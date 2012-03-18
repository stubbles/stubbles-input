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
}
?>