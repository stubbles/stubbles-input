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
use stubbles\peer\MalformedUriException;
use stubbles\peer\http\HttpUri;
/**
 * Validator to ensure that a string is a http uri.
 *
 * @api
 * @deprecated  since 3.0.0, use stubbles\predicate\IsHttpUri instead, will be removed with 4.0.0
 */
class HttpUriValidator implements Validator
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
            $uri = HttpUri::fromString($value);
            if (true === $this->checkDns) {
                return $uri->hasDnsRecord();
            }
        } catch (MalformedUriException $murle) {
            return false;
        }

        return true;
    }
}
