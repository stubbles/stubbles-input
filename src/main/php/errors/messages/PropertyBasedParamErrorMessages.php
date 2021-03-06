<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors\messages;
use stubbles\input\errors\ParamError;
use stubbles\values\Properties;
use stubbles\values\ResourceLoader;
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
     * @var  \stubbles\values\ResourceLoader
     */
    private $resourceLoader;
    /**
     * default locale to be used
     *
     * @var  string
     */
    private $defaultLocale;

    /**
     * constructor
     *
     * @param  \stubbles\values\ResourceLoader  $resourceLoader
     * @param  string                           $defaultLocale
     * @Property{defaultLocale}('stubbles.locale')
     */
    public function __construct(ResourceLoader $resourceLoader, string $defaultLocale = 'default')
    {
        $this->resourceLoader = $resourceLoader;
        $this->defaultLocale  = $defaultLocale;
    }

    /**
     * checks if any messages are known for given error
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @return  bool
     */
    public function existFor(ParamError $error): bool
    {
        return $this->properties()->containSection($error->id());
    }

    /**
     * returns a list of available locales for given error
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @return  string[]
     */
    public function localesFor(ParamError $error): array
    {
        return $this->properties()->keysForSection($error->id());
    }

    /**
     * creates a list of message for given param error
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @return  \stubbles\input\errors\messages\LocalizedMessage[]
     */
    public function messagesFor(ParamError $error): array
    {
        return $error->fillMessages($this->properties()->section($error->id()));
    }

    /**
     * creates message for given param error in given locale
     *
     * If no locale is given the method falls back to a default locale.
     *
     * @param   \stubbles\input\errors\ParamError  $error
     * @param   string                             $locale
     * @return  \stubbles\input\errors\messages\LocalizedMessage
     */
    public function messageFor(ParamError $error, string $locale = null): LocalizedMessage
    {
        $usedLocale = $this->selectLocale($error->id(), $locale);
        return $error->fillMessage(
                (string) $this->properties()->value($error->id(), $usedLocale, ''),
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
    private function selectLocale(string $errorId, string $requestedLocale = null): string
    {
        $properties = $this->properties();
        if (null !== $requestedLocale) {
            if ($properties->containValue($errorId, $requestedLocale)) {
                return $requestedLocale;
            }

            $baseLocale = $this->baseLocale($requestedLocale);
            if ($properties->containValue($errorId, $baseLocale)) {
                return $baseLocale;
            }
        }

        if ($properties->containValue($errorId, $this->defaultLocale)) {
            return $this->defaultLocale;
        }

        $baseLocale = $this->baseLocale($this->defaultLocale);
        if ($properties->containValue($errorId, $baseLocale)) {
            return $baseLocale;
        }

        return 'default';
    }

    private function baseLocale(string $locale): string
    {
       return substr($locale, 0, (int) strpos($locale, '_')) . '_*';
    }

    /**
     * parses properties from property files
     *
     * @return  \stubbles\values\Properties
     */
    private function properties(): Properties
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
