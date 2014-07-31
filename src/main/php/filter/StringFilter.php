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
/**
 * Class for filtering strings (singe line).
 *
 * This filter removes all line breaks, slashes and any HTML tags. In case the
 * given param value is null it returns an empty string.
 */
class StringFilter implements Filter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  string  filtered string
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return '';
        }

        return strip_tags(str_replace($this->getNonAllowedCharacters(),
                                      '',
                                      stripslashes($param->value())
                          ),
                          $this->getAllowedTags()
        );
    }

    /**
     * returns list of non allowed characters
     *
     * @return  char[]
     */
    protected function getNonAllowedCharacters()
    {
        return [chr(10), chr(13)];
    }

    /**
     * returns allowed tags for use with strip_tags()
     *
     * @return  string
     */
    protected function getAllowedTags()
    {
        return null;
    }
}
