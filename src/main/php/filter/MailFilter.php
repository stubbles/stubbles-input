<?php
declare(strict_types=1);
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

use function stubbles\peer\isMailAddress;
/**
 * Class for filtering mail addresses.
 *
 * The return value is empty when:
 * - given param value is null or an empty string,
 * - given param value doesn't contain a valid mail address.
 */
class MailFilter implements Filter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  string  the checked mail address to check
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        $value = $param->value();
        if (!isMailAddress($value)) {
            $param->addError($this->detectErrorId($value));
            return null;
        }

        return $value;
    }

    /**
     * detects the error
     * @param   string  $value
     * @return  string
     */
    private function detectErrorId(string $value): string
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

        if (strpos($value, '.@') !== false || strpos($value, '@.') !== false) {
            return 'MAILADDRESS_DOT_NEXT_TO_AT_SIGN';
        }

        if (strpos($value, '..') !== false) {
            return 'MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS';
        }

        return 'MAILADDRESS_INCORRECT';
    }
}
