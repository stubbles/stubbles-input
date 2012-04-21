<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\expectation;
use net\stubbles\input\ParamError;
use net\stubbles\input\filter\Range;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Datespan;
use net\stubbles\lang\types\datespan\Day;
/**
 * Description of a date expectation.
 *
 * @api
 * @since  2.0.0
 */
class DatespanExpectation extends ValueExpectation implements Range
{
    /**
     * minimum date
     *
     * @type  Date
     */
    private $minDate;
    /**
     * maximum date
     *
     * @type  Date
     */
    private $maxDate;

    /**
     * creates instance from an annotation
     *
     * @param   Annotation  $annotation
     * @return  DatespanExpectation
     */
    public static function fromAnnotation(Annotation $annotation)
    {
        if ($annotation->isRequired()) {
            $self = self::createAsRequired();
        } else {
            $self = self::create();
        }

        return $self->useDefault($annotation->getDefault());
    }

    /**
     * creates an expectation where a value is required
     *
     * @return  DatespanExpectation
     */
    public static function createAsRequired()
    {
        return new self(true);
    }

    /**
     * creates an expectation where no value is required
     *
     * @return  DatespanExpectation
     */
    public static function create()
    {
        return new self(false);
    }

    /**
     * use default value if no value available
     *
     * @param   Datespan  $default
     * @return  DatespanExpectation
     */
    public function useDefault($default)
    {
        if (null === $default) {
            return $this;
        }

        if (is_string($default)) {
            $this->default = new Day($default);
            return $this;
        }

        if (($default instanceof Datespan)) {
            $this->default = $default;
            return $this;
        }

        throw new IllegalArgumentException('Given default value must be a string representing a day or a datespan instance.');
    }

    /**
     * sets minimum date
     *
     * @param   Date  $minDate  earliest allowed date
     * @return  DatespanExpectation
     */
    public function notBefore(Date $minDate)
    {
        $this->minDate = $minDate;
        return $this;
    }

    /**
     * sets maximum date
     *
     * @param   Date  $maxDate  latest allowed date
     * @return  DatespanExpectation
     */
    public function notAfter(Date $maxDate)
    {
        $this->maxDate = $maxDate->change()->timeTo('23:59:59');
        return $this;
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     * @throws  RuntimeException
     */
    public function belowMinBorder($value)
    {
        if (null === $value || null === $this->minDate) {
            return false;
        }

        if (!($value instanceof Datespan)) {
            throw new RuntimeException('Given value must be of instance net\\stubbles\\lang\\types\\datespan\\Datespan');
        }

        return $this->minDate->isAfter($value->getStart());
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     * @throws  RuntimeException
     */
    public function aboveMaxBorder($value)
    {
        if (null === $value || null === $this->maxDate) {
            return false;
        }

        if (!($value instanceof Datespan)) {
            throw new RuntimeException('Given value must be of instance net\\stubbles\\lang\\types\\datespan\\Datespan');
        }

        return $this->maxDate->isBefore($value->getEnd());
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMinParamError()
    {
        return new ParamError('DATE_TOO_EARLY', array('earliestDate' => $this->minDate->asString()));
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMaxParamError()
    {
        return new ParamError('DATE_TOO_LATE', array('latestDate' => $this->maxDate->asString()));
    }
}
?>