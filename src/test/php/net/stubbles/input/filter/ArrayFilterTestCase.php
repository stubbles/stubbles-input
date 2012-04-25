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
 * Tests for net\stubbles\input\filter\ArrayFilter.
 *
 * @since  2.0.0
 * @group  filter
 */
class ArrayFilterTestCase extends FilterTestCase
{
    /**
     * @return  array
     */
    public function getValueResultTuples()
    {
        return array(array(null, null),
                     array('', array()),
                     array('foo', array('foo')),
                     array(' foo ', array('foo')),
                     array('foo, bar', array('foo', 'bar')),
        );
    }

    /**
     * @param  scalar  $value
     * @param  array   $expected
     * @test
     * @dataProvider  getValueResultTuples
     */
    public function value($value, $expected)
    {
        $arrayFilter = new ArrayFilter();
        $this->assertEquals($expected,
                            $arrayFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function usingDifferentSeparator()
    {
        $arrayFilter = new ArrayFilter();
        $this->assertEquals(array('foo', 'bar'),
                            $arrayFilter->setSeparator('|')
                                        ->apply($this->createParam('foo|bar')));
    }

    /**
     * @test
     */
    public function asArrayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = array('foo' => 'bar');
        $this->assertEquals($default,
                            $this->createValueReader(null)
                                 ->asArray($default)
        );
    }

    /**
     * @test
     */
    public function asArrayReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asArray());
    }

    /**
     * @test
     */
    public function asArrayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asArray();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asArrayReturnsEmptyArrayIfParamIsEmpty()
    {
        $this->assertEquals(array(), $this->createValueReader('')->asArray());
    }

    /**
     * @test
     */
    public function asArrayReturnsValidValue()
    {
        $value = array('foo', 'bar');
        $this->assertEquals($value, $this->createValueReader('foo, bar')->asArray());

    }

    /**
     * @test
     */
    public function asArrayReturnsValidValueWithDifferentSeparator()
    {
        $value = array('foo', 'bar');
        $this->assertEquals($value, $this->createValueReader('foo|bar')->asArray(null, '|'));

    }
}
?>