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
 * Tests for net\stubbles\input\filter\IntegerFilter.
 *
 * @package  filter
 */
class IntegerFilterTestCase extends FilterTestCase
{
    /**
     * @return  array
     */
    public function getValueResultTuples()
    {
        return array(array(8, 8),
                     array('8', 8),
                     array('', 0),
                     array(null, null),
                     array(true, 1),
                     array(false, 0),
                     array(1.564, 1)
        );
    }

    /**
     * @param  scalar  $value
     * @param  float   $expected
     * @test
     * @dataProvider  getValueResultTuples
     */
    public function value($value, $expected)
    {
        $integerFilter = new IntegerFilter();
        $this->assertEquals($expected,
                            $integerFilter->apply($this->createParam($value)));
    }
}
?>