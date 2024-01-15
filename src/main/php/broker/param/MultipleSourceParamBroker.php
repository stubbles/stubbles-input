<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use RuntimeException;
use stubbles\input\Request;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Broker to be used to retrieve parameters based on annotations.
 */
abstract class MultipleSourceParamBroker implements ParamBroker
{
    /**
     * extracts parameter from request and handles it
     */
    public function procure(Request $request, Annotation $annotation): mixed
    {
        $read        = $this->readSourceMethod($request, $annotation);
        $valueReader = $request->$read($annotation->getParamName());
        /* @var $valueReader \stubbles\input\ValueReader */
        if ($annotation->isRequired()) {
            return $this->filter(
                $valueReader->required($annotation->getRequiredErrorId('FIELD_EMPTY')),
                $annotation
            );
        }

        if ($this->supportsDefault() && $annotation->hasValueByName('default')) {
            return $this->filter(
                $valueReader->defaultingTo($this->parseDefault($annotation->getDefault())),
                $annotation
            );
        }

        return $this->filter($valueReader, $annotation);
    }

    /**
     * whether a default value for this param is supported
     */
    protected function supportsDefault(): bool
    {
        return true;
    }

    /**
     * parses default value from annotation
     */
    protected function parseDefault(mixed $value): mixed
    {
        return $value;
    }

    /**
     * retrieves method to call on request instance
     *
     * @throws  RuntimeException
     */
    private function readSourceMethod(Request $request, Annotation $annotation): string
    {
        $method = 'read' . $this->source($annotation);
        if (!method_exists($request, $method)) {
            throw new RuntimeException(
                'Unknown source ' . $annotation->getSource() . ' for '
                . $annotation . ' on ' . get_class($request)
            );
        }

        return $method;
    }

    /**
     * returns source from where to read value
     */
    private function source(Annotation $annotation): string
    {
        if ($annotation->hasValueByName('source')) {
            return ucfirst(strtolower($annotation->getSource()));
        }

        return 'Param';
    }

    /**
     * filters single param
     */
    abstract protected function filter(
        CommonValueReader $valueReader,
        Annotation $annotation
    ): mixed;
}
