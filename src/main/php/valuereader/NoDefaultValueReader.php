<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\valuereader;
use stubbles\input\filter\PasswordFilter;
use stubbles\input\filter\range\StringLength;
/**
 * Interface for value readings which don't support default values.
 *
 * @since  3.0.0
 */
interface NoDefaultValueReader extends CommonValueReader
{
    /**
     * read as string value
     *
     * @param   StringLength  $length
     * @return  \stubbles\lang\SecureString
     */
    public function asSecureString(StringLength $length = null);

    /**
     * read as password value
     *
     * @param   int       $minDiffChars      minimum amount of different characters within password
     * @param   string[]  $nonAllowedValues  list of values that are not allowed as password
     * @return  \stubbles\lang\SecureString
     */
    public function asPassword($minDiffChars = PasswordFilter::MIN_DIFF_CHARS_DEFAULT, array $nonAllowedValues = []);

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @return  string
     */
    public function asMailAddress();
}
