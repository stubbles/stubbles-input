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
use stubbles\input\Filter;
use stubbles\input\Param;
use stubbles\input\validator\MailValidator;
/**
 * Class for filtering mail addresses.
 *
 * The return value is empty when:
 * - given param value is null or an empty string,
 * - given param value doesn't contain a valid mail address.
 */
class MailFilter implements Filter
{
    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  string        the checked mail address to check
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        $value = $param->getValue();
        $mailValidator = new MailValidator();
        if (!$mailValidator->validate($value)) {
            $param->addErrorWithId($this->detectErrorId($value));
            return null;
        }

        return $value;
    }

    /**
     * detects the error
     * @param   string  $value
     * @return  string
     */
    private function detectErrorId($value)
    {
        if (preg_match('/\s/i', $value) != false) {
            return 'MAILADDRESS_CANNOT_CONTAIN_SPACES';
        }

        if (preg_match('/[äüöß]/i', $value) != false) {
            return 'MAILADDRESS_CANNOT_CONTAIN_UMLAUTS';
        }

        if (substr_count($value, '@') != 1) {
            return 'MAILADDRESS_MUST_CONTAIN_ONE_AT';
        }

        if (preg_match('/^[' . preg_quote('abcdefghijklmnopqrstuvwxyz1234567890@.+_-') . ']+$/iD', $value) == false) {
            return 'MAILADDRESS_CONTAINS_ILLEGAL_CHARS';
        }

        if (strpos($value, '..') !== false) {
            return 'MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS';
        }

        return 'MAILADDRESS_INCORRECT';
    }
}
