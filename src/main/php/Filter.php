<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use stubbles\input\errors\ParamError;
use stubbles\values\Value;
/**
 * Interface for filter.
 *
 * Filters can be used to take request values, validate them and change them
 * into any other value.
 *
 * @api
 */
abstract class Filter
{
    /**
     * apply filter on given value
     *
     * @return  mixed[]  filtered value
     */
    abstract public function apply(Value $value): array;

    /**
     * helper function to return null and no errors
     *
     * @return  mixed[]
     */
    protected function null(): array
    {
        return [null, []];
    }

    /**
     * helper function to return filtered value and no errors
     *
     * @return  mixed[]
     */
    protected function filtered(mixed $filtered): array
    {
        return [$filtered, []];
    }

    /**
     * helper function to return null and list of errors
     *
     * @param   array<ParamError|array<string,array<string,mixed>>>  $errors
     * @return  mixed[]
     */
    protected function errors(array $errors): array
    {
        $final = [];
        foreach ($errors as $errorId => $details) {
            if ($details instanceof ParamError) {
                $final[$errorId] = $details;
            } else {
                $final[$errorId] = ParamError::fromData($errorId, $details);
            }
        }

        return [null, $final];
    }

    /**
     * helper function to return null and one error
     *
     * @param   array<string,mixed>  $details
     * @return  mixed[]
     */
    protected function error(string $error, array $details = []): array
    {
        $error = ParamError::fromData($error, $details);
        return [null, [$error->id() => $error]];
    }
}
