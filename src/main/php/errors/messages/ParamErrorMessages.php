<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\errors\messages;
use stubbles\input\errors\ParamError;
/**
 * Interface to translate param errors into error messages.
 *
 * @api
 * @ImplementedBy(stubbles\input\errors\messages\PropertyBasedParamErrorMessages.class)
 */
interface ParamErrorMessages
{
    /**
     * checks if any messages are known for given error
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @return  bool
     */
    public function existFor(ParamError $error);

    /**
     * returns a list of available locales for given error
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @return  string[]
     */
    public function localesFor(ParamError $error);

    /**
     * creates a list of message for given param error
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @return  \stubbles\input\errors\messages\LocalizedMessage[]
     */
    public function messagesFor(ParamError $error);

    /**
     * creates message for given param error in given locale
     *
     * If no locale is given the method falls back to a default locale.
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @param   string      $locale
     * @return  \stubbles\input\errors\messages\LocalizedMessage
     */
    public function messageFor(ParamError $error, $locale = null);
}
