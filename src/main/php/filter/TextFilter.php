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
/**
 * Class for filtering texts (strings containing line feeds).
 *
 * This filter removes windows line breaks and html tags from the value. Via
 * allowTags() a list of allowed tags that will not be removed can be
 * specified. Use the allowed tags option very careful. It does not protect
 * you against possible XSS attacks!
 *
 * In case the given param value is null an empty string is returned.
 */
class TextFilter extends StringFilter
{
    /**
     * list of allowed tags
     *
     * @type  string[]
     */
    private $allowedTags = [];

    /**
     * set the list of allowed tags
     *
     * Use this option very careful. It does not protect you against
     * possible XSS attacks!
     *
     * @param   string[]  $allowedTags
     * @return  \stubbles\input\filter\TextFilter
     */
    public function allowTags(array $allowedTags): self
    {
        $this->allowedTags = $allowedTags;
        return $this;
    }

    /**
     * returns list of non allowed characters
     *
     * @return  char[]
     */
    protected function nonAllowedCharacters(): array
    {
        return [chr(13)];
    }

    /**
     * returns allowed tags for use with strip_tags()
     *
     * @return  string
     */
    protected function allowedTags(): string
    {
        if (count($this->allowedTags) > 0) {
            return '<' . join('><', $this->allowedTags) . '>';
        }

        return '';
    }
}
