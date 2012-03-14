<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\Param;
use net\stubbles\lang\BaseObject;
/**
 * Length filter to check that a string is long enough.
 *
 * This filter takes any string and checks if it complies with the min and/or
 * the max length.
 */
class LengthFilter extends BaseObject implements Filter
{
    /**
     * decorated string filter
     *
     * @type  StringFilter
     */
    private $filter;
    /**
     * minimum length
     *
     * @type  int
     */
    private $minLength;
    /**
     * maximum length
     *
     * @type  int
     */
    private $maxLength;

    /**
     * constructor
     *
     * @param  StringFilter  $filter     decorated string filter
     * @param  int           $minLength  minimum length
     * @param  int           $maxLength  maximum length
     */
    public function __construct(StringFilter $filter, $minLength = null, $maxLength = null)
    {
        $this->filter    = $filter;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  string
     */
    public function apply(Param $param)
    {
        $value = $this->filter->apply($param);
        if (empty($value)) {
            return '';
        }

        if ($this->isShorterThanMinLength($value)) {
            $param->addErrorWithId('STRING_TOO_SHORT', array('minLength' => $this->minLength));
            return null;
        } elseif ($this->isLongerThanMaxLength($value)) {
            $param->addErrorWithId('STRING_TOO_LONG', array('maxLength' => $this->maxLength));
            return null;
        }

        return $value;
    }

    /**
     * checks if given string is shorter than minimum length
     *
     * @param   string  $value
     * @return  bool
     */
    private function isShorterThanMinLength($value)
    {
        if (null === $this->minLength) {
            return false;
        }

        return (iconv_strlen($value) < $this->minLength);
    }

    /**
     * checks if given string is longer than maximum length
     *
     * @param   string  $value
     * @return  bool
     */
    private function isLongerThanMaxLength($value)
    {
        if (null === $this->maxLength) {
            return false;
        }

        return (iconv_strlen($value) > $this->maxLength);
    }
}
?>