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
use net\stubbles\peer\MalformedUrlException;
use net\stubbles\peer\http\HttpUrl;
/**
 * Validator to ensure that a string is a http uri.
 *
 * @api
 */
class HttpUriValidator extends BaseObject implements Validator
{
    /**
     * whether to check dns for existence of given url or not
     *
     * @type  bool
     */
    private $checkDns = false;

    /**
     * enables dns check for validation
     *
     * Enabling the dns check means that even if the HTTP URI is syntactically
     * valid it must have an DNS entry to be valid at all.
     *
     * @return  HttpUriValidator
     */
    public function enableDnsCheck()
    {
        $this->checkDns = true;
        return $this;
    }

    /**
     * validate that the given value is a http url
     *
     * @param   string  $value
     * @return  bool
     */
    public function validate($value)
    {
        if (empty($value)) {
            return false;
        }

        try {
            $url = HttpUrl::fromString($value);
            if (true === $this->checkDns) {
                return $url->hasDnsRecord();
            }
        } catch (MalformedUrlException $murle) {
            return false;
        }

        return true;
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array();
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