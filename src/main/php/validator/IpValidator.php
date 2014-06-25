<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
use stubbles\input\Validator;
/**
 * Class for validating that something is an ip address, either v4 or v6.
 *
 * @api
 * @deprecated  since 3.0.0, use stubbles\predicate\IsIpAddress instead, will be removed with 4.0.0
 */
class IpValidator implements Validator
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
}
