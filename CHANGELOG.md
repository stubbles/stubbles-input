3.1.0 (2014-08-10)
------------------

  * added shortcuts for accessing headers where a redirect version must be preferred over the changed version
    *  added `stubbles\input\web\WebRequest::validateRedirectHeader()`
    *  added `stubbles\input\web\WebRequest::readRedirectHeader()`


3.0.2 (2014-08-10)
------------------

  * fixed bug that `requiresValue` of request broker annotations was not true after upgrade of stubbles/core to 4.1.x


3.0.1 (2014-08-04)
------------------

  * fixed doc comments that yielded incorrect results for code completion hints


3.0.0 (2014-07-31)
------------------

### BC breaks

  * removed namespace prefix `net`, base namespace is now `stubbles\input` only
  * usage of date and datespan filters now requires stubbles/date, using applications must require stubbles/date explicitly
    * `net\stubbles\input\ValueReader::asDate()`
    * `net\stubbles\input\ValueReader::asDay()`
    * `net\stubbles\input\ValueReader::asMonth()`
  * removed `net\stubbles\input\Param::addErrorWithId()`, deprecated since 2.3.3
  * removed `net\stubbles\input\ParamErrors::add()`, deprecated since 2.3.3
  * removed `net\stubbles\input\ParamErrors::get()`, deprecated since 2.3.3
  * changed how default values are set in `net\stubbles\input\ValueReader`
    * a default value can now be set via `net\stubbles\input\ValueReader::defaultingTo()`
    * all default parameters on `as*()` and `ifIs*()` methods have been removed
    * default values for `as*()` methods are now type checked, i.e. they must fit to the type later requested with the according `as*()` method
    * `stubbles\input\ValueReader::asBool()` now returns `null` instead of `false` when no param value set, use `stubbles\input\ValueReader::defaultingTo(false)->asBool()` to retain the old behavior
  * `net\stubbles\input\ValueReader::asPassword()` does not accept single config values any more, but an instance of `net\stubbles\input\filter\PasswordChecker`
  * all instances that filter passwords now return an instance of  `stubbles\lang\SecureString` instead of a basic string
    * `stubbles\input\ValueReader::asPassword()`
    * `stubbles\input\filter\PasswordFilter::apply()`
    * `stubbles\input\broker\param\PasswordParamBroker::procure()`
    * `stubbles\input\broker\param\PasswordParamBroker::procureParam()`
  * `stubbles\input\ValueReader::applyFilter()` is not public any more, use `stubbles\input\ValueReader::withFilter()` instead
  * api rework:
    * deprecated `stubbles\input\Param::getName()`, use `stubbles\input\Param::name()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\Param::getValue()`, use `stubbles\input\Param::value()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\Param::getErrors()`, use `stubbles\input\Param::errors()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\Request::cancel()`, will be removed with 4.0.0
    * deprecated `stubbles\input\Request::isCancelled()`, will be removed with 4.0.0
    * deprecated `stubbles\input\Request::getMethod()`, use `stubbles\input\Request::method()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\ValueReader::withFunction()`, use `stubbles\input\ValueReader::withCallable()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\console\ConsoleRequest::getEnvNames()`, use `stubbles\input\console\ConsoleRequest::envNames()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\web\WebRequest::getProtocolVersion()`, use `stubbles\input\web\WebRequest::protocolVersion()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\web\WebRequest::getUri()`, use `stubbles\input\web\WebRequest::uri()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\web\WebRequest::getHeaderNames()`, use `stubbles\input\web\WebRequest::headerNames()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\web\WebRequest::getCookieNames()`, use `stubbles\input\web\WebRequest::cookieNames()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\web\useragent\UserAgent::getName()` use `stubbles\input\web\useragent\UserAgent::name()` instead, will be removed with 4.0.0
    * deprecated `stubbles\input\Validator` and all of its implementations:
      * in general use predicates delivered by stubbles/core instead
      * for `stubbles\input\validator\ContainsValidator` use `stubbles\predicate\Contains`
      * for `stubbles\input\validator\DirectoryValidator` use `stubbles\predicate\IsExistingDirectory`
      * for `stubbles\input\validator\EqualValidator` use `stubbles\predicate\Equals`
      * for `stubbles\input\validator\FileValidator` use `stubbles\predicate\IsExistingFile`
      * for `stubbles\input\validator\HttpUriValidator` use `stubbles\predicate\IsHttpUri`
      * for `stubbles\input\validator\IpV4Validator` use `stubbles\predicate\IsIpV4Address`
      * for `stubbles\input\validator\IpV6Validator` use `stubbles\predicate\IsIpV6Address`
      * for `stubbles\input\validator\IpValidator` use `stubbles\predicate\IsIpAddress`
      * for `stubbles\input\validator\MailValidator` use `stubbles\predicate\IsMailAddress`
      * for `stubbles\input\validator\PreSelectValidator` use `stubbles\predicate\IsOneOf`
      * for `stubbles\input\validator\RegexValidator` use `stubbles\predicate\Regex`
  * `stubbles\input\web\WebRequest::protocolVersion()` now returns an instance of `stubbles\peer\http\HttpVersion`

### Other changes

  * upgraded to stubbles/core 4.x
  * added `net\stubbles\input\ValueReader::asSecureString()`
  * added `net\stubbles\input\filter\SecureStringFilter`
  * `net\stubbles\input\filter\range\StringLength` can also work with instances of `stubbles\lang\SecureString`
  * request broker now supports turning param values into instances of `stubbles\lang\SecureString` with `@Request[SecureString]`
  * added `net\stubbles\input\filter\PasswordChecker`
  * request broker annotation `@Request[Password]` now supports `minLength` attribute, default value is 8
  * request broker annotation `@Request[OneOf]` now throws an exception when list of allowed values is missing in annotation
  * changed `stubbles\input\web\WebRequest::protocolVersion()` to always report the protocol version when it can be detected, not just supported ones
  * added `stubbles\input\web\WebRequest::originatingIpAddress()`


2.5.1 (2014-05-07)
------------------

  * added `net\stubbles\input\ValueReader::asMonth()`


2.5.0 (2014-05-06)
------------------

  * raised minimum PHP version to 5.4.0
  * the following methods now except anything that can be casted to an instance of `net\stubbles\lang\types\Date` via `net\stubbles\lang\types\Date::castFrom()`
     * `net\stubbles\input\ValueReader::asDate()`
     * `net\stubbles\input\filter\range\DateRange::__construct()`
     * `net\stubbles\input\filter\range\DatespanRange::__construct()`


2.4.1 (2014-02-18)
------------------

  * implemented #49: `net\stubbles\input\filter\BoolFilter` should allow yes and no


2.4.0 (2014-01-21)
------------------

  * upgraded to stubbles-core 3.4.0


2.3.3 (2013-12-02)
------------------

  * fixed #45 ParamBrokerMap should work with lowercase versions of built-in types as well
  * fixed #46 possibility to add param errors without creating a ParamError instance
     * added `net\stubbles\input\Param::add()` now also accepts an error id instead of a ParamError instance only
     * added `net\stubbles\input\ParamErrors::append()`, replaces `net\stubbles\input\ParamErrors::add()`
     * deprecated `net\stubbles\input\ParamErrors::add()`, will be removed with 2.4.0
     * deprecated `net\stubbles\input\Param::addErrorWithId()`, will be removed with 2.4.0
     * added `net\stubbles\input\ParamErrors::asList()`, replaces `net\stubbles\input\ParamErrors::get()`
     * deprecated `net\stubbles\input\ParamErrors::get()`, will be removed with 2.4.0


2.3.2 (2013-11-01)
------------------

  * fixed bug when port is also in `$_SERVER['HTTP_HOST']` because user agent sent according host header


2.3.1 (2013-10-24)
------------------

  * added `net\stubbles\input\filter\range\StringLength::truncate()`


2.3.0 (2013-05-02)
------------------

  * upgraded stubbles/core to ~3.0


2.2.1 (2013-02-06)
------------------

  * change dependency to stubbles/core from 2.1.* to ~2.1


2.2.0 (2012-09-06)
------------------

  * implemented issue #33: allow usage of closures for filtering and validating
     * added `net\stubbles\input\ValueReader::withFunction()`
     * added `net\stubbles\input\ValueValidator::withFunction()`


2.1.0 (2012-07-31)
------------------

  * raised stubbles-core to 2.1.*


2.0.2 (2012-07-25)
------------------

  * added `net\stubbles\input\web\BaseWebRequest::getProtocolVersion()`


2.0.1 (2012-07-09)
------------------

  * added `net\stubbles\input\filter\AcceptFilter`


2.0.0 (2012-05-22)
------------------

  * Initial release.
