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
use stubbles\values\Parse;
use stubbles\values\Value;
/**
 * Basic class for filters on request variables of type array.
 *
 * When input param is null return value is null, if input param is an empty
 * string return value is an empty array. For all other values the given param
 * will be split using the separator (defaults to ',') and each array element
 * will be trimmed to remove superfluous whitespace.
 *
 * @since  2.0.0
 */
class ArrayFilter extends Filter
{
    /**
     * default separator to be used to split string
     */
    const SEPARATOR_DEFAULT = ',';
    /**
     * separator to be used to split parameter value
     *
     * @type  string
     */
    private $separator      = self::SEPARATOR_DEFAULT;

    /**
     * constructor
     *
     * @param  string  $separator  optional  separator to be used to split parameter value
     */
    public function __construct(string $separator = self::SEPARATOR_DEFAULT)
    {
        $this->setSeparator($separator);
    }

    /**
     * sets separator to be used
     *
     * @param   string  $separator
     * @return  \stubbles\input\filter\ArrayFilter
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * apply filter on given value
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        if ($value->isNull()) {
            return $this->null();
        }

        return $this->filtered(array_map(
                'trim',
                Parse::toList($value->value(), $this->separator)
        ));
    }
}
