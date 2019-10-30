<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\input\Filter;
use stubbles\values\Value;
/**
 * Class for filtering strings (singe line).
 *
 * This filter removes all line breaks, slashes and any HTML tags. In case the
 * given param value is null it returns an empty string.
 */
class StringFilter extends Filter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->filtered('');
        }

        return $this->filtered(strip_tags(
                str_replace(
                        $this->nonAllowedCharacters(),
                        '',
                        stripslashes($value->value())
                ),
                $this->allowedTags()
        ));
    }

    /**
     * returns list of non allowed characters
     *
     * @return  char[]
     */
    protected function nonAllowedCharacters(): array
    {
        return [chr(10), chr(13)];
    }

    /**
     * returns allowed tags for use with strip_tags()
     *
     * @return  string
     */
    protected function allowedTags()
    {
        return null;
    }
}
