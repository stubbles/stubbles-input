<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker\param;
use net\stubbles\input\Request;
use net\stubbles\input\validator\ValueReader;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to read parameters based on annotations.
 */
abstract class MultipleSourceReaderBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   Request     $request     instance to handle value with
     * @param   Annotation  $annotation  annotation which contains request param metadata
     * @return  mixed
     */
    public function procure(Request $request, Annotation $annotation)
    {
        $hasValue = $this->getMethod($request, $annotation, 'has');
        if (!$request->$hasValue($annotation->getName())) {
            if ($annotation->isRequired()) {
                $errors = strtolower($this->getSource($annotation)) . 'Errors';
                $request->$errors()->add($this->getEmpyParamError($annotation),
                                         $annotation->getName()
                );
            }

            return null;
        }

        $readValue = $this->getMethod($request, $annotation, 'read');
        return $this->read($request->$readValue($annotation->getName()), $annotation);
    }

    /**
     * creates param error in case value is not set
     *
     * @param   Annotation   $annotation
     * @return  string
     */
    protected abstract function getEmpyParamError(Annotation $annotation);

    /**
     * reads single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains reader metadata
     * @return  mixed
     */
    protected abstract function read(ValueReader $valueReader, Annotation $annotation);
}
?>