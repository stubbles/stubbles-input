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
    private $textFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->textFilter = new TextFilter();
        parent::setUp();
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

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('', $this->createValueReader(null)->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('baz', $this->createValueReader(null)
                                        ->asText('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asText();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')
                               ->asText(null, new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asText(null, new StringLength(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asTextReturnsValidValue()
    {
        $this->assertEquals('foo', $this->createValueReader('foo<b>')->asText());

    }

    /**
     * @test
     */
    public function asTextWithAllowedTagsReturnsValidValue()
    {
        $this->assertEquals('foo<b>', $this->createValueReader('foo<b>')->asText(null, null, array('b')));

    }
}
?>