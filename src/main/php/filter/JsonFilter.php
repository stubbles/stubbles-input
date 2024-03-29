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
 * Class for decoding JSON.
 *
 * @link  http://www.json.org/
 * @link  http://www.ietf.org/rfc/rfc4627.txt
 */
class JsonFilter extends Filter
{
    /**
     * maximum default allowed length of incoming JSON document in bytes
     */
    const DEFAULT_MAX_LENGTH = 20000;

    /**
     * constructor
     *
     * @param  int  $maxLength  maximum allowed length of incoming JSON document in bytes
     */
    public function __construct(private int $maxLength = self::DEFAULT_MAX_LENGTH) { }

    /**
     * apply filter on given value
     *
     * @return  mixed[]
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->null();
        }

        if (strlen($value->value()) > $this->maxLength) {
            return $this->error('JSON_INPUT_TOO_BIG', ['maxLength' => $this->maxLength]);
        }

        $json = $value->value();
        if (!$this->isValidJsonStructure($json)) {
            return $this->error('JSON_INVALID');
        }

        $decodedJson = json_decode($json);
        $errorCode   = json_last_error();
        if (JSON_ERROR_NONE !== $errorCode) {
            return $this->error(
                'JSON_SYNTAX_ERROR',
                [
                    'errorCode' => $errorCode,
                    'errorMsg'  => json_last_error_msg()
                ]
            );
        }

        return $this->filtered($decodedJson);
    }

    /**
     * checks if given json is valid a valid structure
     *
     * JSON can only be an object or an array structure (see JSON spec & RFC),
     * but json_decode() lacks this restriction.
     */
    private function isValidJsonStructure(string $json): bool
    {
        if (
            !$this->startsAndEndsWith($json, '{', '}')
            && !$this->startsAndEndsWith($json, '[', ']')
        ) {
            return false;
        }

        return true;
    }

    private function startsAndEndsWith(string &$json, string $start, string $end): bool
    {
        return $start === $json[0] && $json[strlen($json) - 1] === $end;
    }
}
