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
use stubbles\values\Value;
/**
 * Class for filtering values based on using a callable as predicate.
 *
 * @since  3.0.0
 */
class PredicateFilter extends Filter
{
    /**
     * predicate to be used
     *
     * @type  callable
     */
    private $predicate;
    /**
     * error id to be used in case validation fails
     *
     * @type  string
     */
    private $errorId;
    /**
     * details for param error in case validation fails
     *
     * @type  array
     */
    private $details;

    /**
     * constructor
     *
     * @param  callable  $predicate  validator to be used
     * @param  string    $errorId    error id to be used in case predicate fails
     * @param  array     $details    details for param error in case predicate fails
     */
    public function __construct(callable $predicate, string $errorId, array $details = [])
    {
        $this->predicate = $predicate;
        $this->errorId   = $errorId;
        $this->details   = $details;
    }

    /**
     * apply filter on given value
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        $predicate = $this->predicate;
        if ($predicate($value->value())) {
            return $this->filtered($value->value());
        }

        return $this->error($this->errorId, $this->details);
    }
}
