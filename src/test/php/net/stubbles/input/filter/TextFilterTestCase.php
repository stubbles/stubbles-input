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
 * Tests for net\stubbles\input\filter\TextFilter.
 *
 * @group  filter
 */
class TextFilterTestCase extends FilterTestCase
{
    /**
     * the instance to test
     *
     * @type  TextFilter
     */
    protected $textFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->textFilter = new TextFilter();
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsNull()
    {
        $this->assertEquals('', $this->textFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString()
    {
        $this->assertEquals('', $this->textFilter->apply($this->createParam('')));
    }

    /**
     * data provider for removesTags()
     *
     * @return  string
     */
    public function getAllowedTags()
    {
        return array(array(array(), 'this is bold and cursive and underlined with a link'),
                     array(array('b', 'i'), 'this is <b>bold</b> and <i>cursive</i> and underlined with a link'),
                     array(array('b', 'i', 'a'), 'this is <b>bold</b> and <i>cursive</i> and underlined with a <a href="http://example.org/">link</a>')

        );
    }

    /**
     * @param  string[]  $allowedTags
     * @param  string    $expected
     * @test
     * @dataProvider  getAllowedTags
     */
    public function removesTags(array $allowedTags, $expected)
    {
        $this->assertEquals($expected,
                            $this->textFilter->allowTags($allowedTags)
                                             ->apply($this->createParam('this is <b>bold</b> and <i>cursive</i> and <u>underlined</u> with a <a href="http://example.org/">link</a>'))
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        $this->assertEquals("'kkk",
                            $this->textFilter->apply($this->createParam("\'kkk"))
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        $this->assertEquals("cdekkk",
                            $this->textFilter->apply($this->createParam("cde\rkkk"))
        );
    }

    /**
     * @test
     */
    public function doesNotRemoveLineBreaks()
    {
        $this->assertEquals("ab\ncde\nkkk",
                            $this->textFilter->apply($this->createParam("ab\ncde\nkkk"))
        );
    }
}
?>