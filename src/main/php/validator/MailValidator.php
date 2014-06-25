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
 * Validator to ensure that a string is a mail address.
 *
 * @api
 * @deprecated  since 3.0.0, use stubbles\predicate\IsMailAddress instead, will be removed with 4.0.0
 */
class MailValidator implements Validator
{
    /**
     * validate that the given value is not longer than the maximum length
     *
     * @param   string  $value
     * @return  bool    true if value is not longer than maximal length, else false
     */
    public function validate($value)
    {
        if (null == $value || strlen($value) == 0) {
            return false;
        }

        $url = @parse_url('mailto://' . $value);
        if (!isset($url['host']) || !preg_match('/^([a-zA-Z0-9-]*)\.([a-zA-Z]{2,4})$/', $url['host'])) {
            return false;
        }

        if (!isset($url['user']) || strlen($url['user']) == 0 || !preg_match('/^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*$/', $url['user'])) {
            return false;
        }

        return true;
    }
}
