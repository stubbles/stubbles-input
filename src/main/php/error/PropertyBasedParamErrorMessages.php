<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\error;
use stubbles\input\ParamError;
use stubbles\lang\Properties;
use stubbles\lang\ResourceLoader;
/**
 * Translates errors into messages which are stored in property files.
 *
 * @since  1.3.0
 * @Singleton
 */
class PropertyBasedParamErrorMessages implements ParamErrorMessages
{
    /**
     * loader for master.xsl resource file
     *
     * @type  ResourceLoader
     */
    private $resourceLoader;
    /**
     * defalt locale to be used
     *
     * @type  string
     */
    private $defaultLocale  = 'default';

    /**
     * constructor
     *
     * @param  ResourceLoader  $resourceLoader
     * @Inject
     */
    public function __construct(ResourceLoader $resourceLoader)
    {
        $this->resourceLoader = $resourceLoader;
    }

    /**
     * sets locale to be used as default locale
     *
     * @param   string  $locale
     * @return  PropertyBasedParamErrorMessages
     * @Inject(optional=true)
     * @Property('stubbles.locale')
     */
    public function setLocale($locale)
    {
        $this->defaultLocale = $locale;
        return $this;
    }

    /**
     * checks if any messages are known for given error
     *
     * @param   ParamError  $error
     * @return  bool
     */
    public function existFor(ParamError $error)
    {
        return $this->properties()->containSection($error->getId());
    }

    /**
     * returns a list of available locales for given error
     *
     * @param   ParamError  $error
     * @return  string[]
     */
    public function localesFor(ParamError $error)
    {
        return $this->properties()->keysForSection($error->getId());
    }

    /**
     * creates a list of message for given param error
     *
     * @param   ParamError  $error
     * @return  LocalizedString[]
     */
    public function messagesFor(ParamError $error)
    {
        return $error->fillMessages($this->properties()->section($error->getId()));
    }

    /**
     * creates message for given param error in given locale
     *
     * If no locale is given the method falls back to a default locale.
     *
     * @param   ParamError  $error
     * @param   string      $locale
     * @return  LocalizedString
     */
    public function messageFor(ParamError $error, $locale = null)
    {
        $usedLocale = $this->selectLocale($error->getId(), $locale);
        return $error->fillMessage(
                $this->properties()->value($error->getId(), $usedLocale),
                $usedLocale
        );
    }

    /**
     * selects locale based on availability of translations
     *
     * @param   string  $errorId
     * @param   string  $requestedLocale
     * @return  string
     */
    private function selectLocale($errorId, $requestedLocale = null)
    {
        $properties = $this->properties();
        if (null !== $requestedLocale) {
            if ($properties->containValue($errorId, $requestedLocale)) {
                return $requestedLocale;
            }

            $baseLocale = substr($requestedLocale, 0, strpos($requestedLocale, '_')) . '_*';
            if ($properties->containValue($errorId, $baseLocale)) {
                return $baseLocale;
            }
        }

        if ($properties->containValue($errorId, $this->defaultLocale)) {
            return $this->defaultLocale;
        }

        return 'default';
    }

    /**
     * parses properties from property files
     *
     * @return  Properties
     */
    private function properties()
    {
        static $properties = null;
        if (null === $properties) {
            $properties = new Properties();
            foreach ($this->resourceLoader->availableResourceUris('input/error/message.ini') as $resourceUri) {
                $properties = $properties->merge(Properties::fromFile($resourceUri));
            }
        }

        return $properties;
    }
}
