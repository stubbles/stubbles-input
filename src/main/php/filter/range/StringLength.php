<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use InvalidArgumentException;
use LogicException;
use stubbles\values\Secret;
/**
 * String length limitation.
 *
 * @api
 * @since  2.0.0
 */
class StringLength extends AbstractRange
{
    private bool $allowsTruncate = false;

    public function __construct(
        private ?int $minLength,
        private ?int $maxLength = null
    ) { }

    /**
     * create instance which treats above max border not as error, but will lead
     * to a truncated value only
     *
     * @throws  InvalidArgumentException
     * @since   2.3.1
     */
    public static function truncate(?int $minLength, ?int $maxLength = null)
    {
        if (0 >= $maxLength) {
            throw new InvalidArgumentException(
                'Max length must be greater than 0, otherwise truncation doesn\'t make sense'
            );
        }

        $self = new self($minLength, $maxLength);
        $self->allowsTruncate = true;
        return $self;
    }

    /**
     * checks if given value is below min border of range
     */
    protected function belowMinBorder(mixed $value): bool
    {
        if (null === $this->minLength) {
            return false;
        }

        return iconv_strlen((string) $value) < $this->minLength;
    }

    /**
     * checks if given value is above max border of range
     */
    protected function aboveMaxBorder(mixed $value): bool
    {
        if (null === $this->maxLength) {
            return false;
        }

        return iconv_strlen((string) $value) > $this->maxLength;
    }

    /**
     * checks whether string can be truncated to maximum length
     *
     * @since   2.3.1
     */
    public function allowsTruncate(mixed $value): bool
    {
        return $this->allowsTruncate && $this->aboveMaxBorder($value);
    }

    /**
     * truncates given value to max length
     *
     * @throws  LogicException
     * @since   2.3.1
     */
    public function truncateToMaxBorder(string $value): string
    {
        if (!$this->allowsTruncate($value)) {
            throw new LogicException('Truncate value to max length not allowed');
        }

        if (null === $this->maxLength) {
            return $value;
        }

        return substr($value, 0, $this->maxLength);
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function minBorderViolation(): array
    {
        if (null === $this->minLength) {
            return [];
        }

        return ['STRING_TOO_SHORT' => ['minLength' => $this->minLength]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function maxBorderViolation(): array
    {
        if (null === $this->maxLength) {
            return [];
        }

        return ['STRING_TOO_LONG' => ['maxLength' => $this->maxLength]];
    }
}
