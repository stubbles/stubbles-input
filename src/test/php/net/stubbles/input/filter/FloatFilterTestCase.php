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
 * Tests for net\stubbles\input\filter\FloatFilter.
 *
 * @package  filter
 */
class FloatFilterTestCase extends FilterTestCase
{
    /**
     * @return  array
     */
    public function getValueResultTuples()
    {
        return array(array('8.4533', 8453),
                     array('8.4538', 8453),
                     array('8.45', 8450),
                     array('8', 8000),
                     array(8.4533, 8453),
                     array(8.4538, 8453),
                     array(8.45, 8450),
                     array(8, 8000),
                     array(null, null),
                     array(true, 1000),
                     array(false, 0)
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
        $floatFilter = new FloatFilter();
        $this->assertEquals($expected,
                            $floatFilter->setDecimals(3)
                                        ->apply($this->createParam($value))
        );
    }

    /**
     * @test
     */
    public function float()
    {
        $floatFilter = new FloatFilter();
        $this->assertEquals(156,
                            $floatFilter->setDecimals(2)
                                        ->apply($this->createParam('1.564'))
        );
    }

    /**
     * @test
     */
    public function decimalsNotSet()
    {
        $floatFilter = new FloatFilter();
        $this->assertEquals(1.564, $floatFilter->apply($this->createParam('1.564')));
    }
}
?>