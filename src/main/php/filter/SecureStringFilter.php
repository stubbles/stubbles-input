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
use stubbles\input\Param;
use stubbles\lang\SecureString;
/**
 * Class for filtering strings (singe line).
 *
 * This filter removes all line breaks, slashes and any HTML tags. In case the
 * given param value is null or empty it returns the explicit null version of
 * the SecureString.
 *
 * @since  3.0.0
 */
class SecureStringFilter extends StringFilter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param         $param
     * @return  \stubbles\lang\SecureString  filtered string
     */
    public function apply(Param $param)
    {
        $value = parent::apply($param);
        if (!empty($value)) {
            return SecureString::create($value);
        }

        return SecureString::forNull();
    }
}
