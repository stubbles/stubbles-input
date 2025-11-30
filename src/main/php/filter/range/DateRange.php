<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use DateTime;
use stubbles\date\Date;
/**
 * Description of a date range.
 *
 * @api
 * @since  2.0.0
 */
class DateRange extends AbstractRange
{
    use NonTruncatingRange;
    private ?Date $minDate;

    private ?Date $maxDate;

    public function __construct(
        int|string|DateTime|Date|null $minDate = null,
        int|string|DateTime|Date|null $maxDate = null
    ) {
        $this->minDate = null === $minDate ? null : Date::castFrom($minDate, 'minDate');
        $this->maxDate = null === $maxDate ? null : Date::castFrom($maxDate, 'maxDate');
    }

    /**
     * checks if given value is below min border of range
     */
    protected function belowMinBorder(mixed $value): bool
    {
        if (null === $this->minDate) {
            return false;
        }

        return $this->minDate->isAfter(Date::castFrom($value, 'value'));
    }

    /**
     * checks if given value is above max border of range
     */
    protected function aboveMaxBorder(mixed $value): bool
    {
        if (null === $this->maxDate) {
            return false;
        }

        return $this->maxDate->isBefore(Date::castFrom($value, 'value'));
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function minBorderViolation(): array
    {
        if (null === $this->minDate) {
            return [];
        }

        return ['DATE_TOO_EARLY' => ['earliestDate' => $this->minDate->asString()]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function maxBorderViolation(): array
    {
        if (null === $this->maxDate) {
            return [];
        }

        return ['DATE_TOO_LATE' => ['latestDate' => $this->maxDate->asString()]];
    }
}
