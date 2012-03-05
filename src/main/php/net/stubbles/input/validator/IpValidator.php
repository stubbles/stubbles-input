<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
use net\stubbles\lang\BaseObject;
/**
 * Class for validating that something is an ip address, either v4 or v6.
 */
class IpValidator extends BaseObject implements Validator
{
    /**
     * validate that the given value is an ip address (either v4 or v6)
     *
     * @param   mixed  $value
     * @return  bool   true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        if (IpV4Validator::validateAddress($value)) {
            return true;
        }

        return IpV6Validator::validateAddress($value);
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('expected' => [expected_value]);
     * </code>
     *
     * @return  array  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array();
    }
}
?>