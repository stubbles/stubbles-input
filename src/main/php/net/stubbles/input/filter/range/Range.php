<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\range;
/**
 * Interface for range definitions.
 *
 * Range definitions can be used to set up valid ranges for values.
 *
 * @api
 * @since  2.0.0
 */
interface Range
{
    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function belowMinBorder($value);

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function aboveMaxBorder($value);

    /**
     * checks whether value can be truncated to maximum value
     *
     * @return  bool
     * @since   2.3.1
     */
    public function allowsTruncate();

    /**
     * truncates given value to max border
     *
     * @param   string  $value
     * @return  string
     * @since   2.3.1
     */
    public function truncateToMaxBorder($value);

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMinParamError();

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMaxParamError();
}
?>