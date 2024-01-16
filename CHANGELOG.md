# Changelog

## 9.0.0 (2024-01-16)

### BC breaks

* raised minimum required PHP version to 8.2
* removed classes and methods deprecated since 7.0.0
  * `stubbles\input\Param`
  * `stubbles\input\ValueReader::forParam()`
  * `stubbles\input\broker\param\ParamBroker::procureParam()`
    * `stubbles\input\broker\param\CustomDatespanParamBroker::procureParam()`
    * `stubbles\input\broker\param\ultipleSourceParamBroker::procureParam()`
* `stubbles\input\filter\range\StringLength::truncate()` now throws an `\ValueError` instead of an `\InvalidArgumentException`

## 8.0.2 (2019-12-16)

* added more phpstan related type hints
* fixed minor bugs due to type offenses

## 8.0.1 (2019-12-06)

* Fixed problem that setting `stubbles.locale` to something like `de_DE` didn't fall back to using messages with `de_*`

## 8.0.0 (2019-11-07)

### BC breaks

* raised minimum required PHP version to 7.3
* `stubbles\input\broker\RequestBroker::procure()` doesn't return the given and procured object instance any more
* `stubbles\input\filter\range\StringLength` doesn't support instances of `stubbles\values\Secret` any more
* `stubbles\input\ValueReader::asSecret()` doesn't take a `stubbles\input\filter\range\StringLength` any more but `stubbles\input\filter\range\SecretMinLength`
* `@Request[Secret]` doesn't support the `maxLength` attribute any more
* `@Request[OneOf]` will throw an exception when callback specified with `allowedSource` doesn't exist or isn't callable

## 7.0.0 (2016-07-31)

### BC breaks

* raised minimum required PHP version to 7.0.0
* introduced scalar type hints and strict type checking
* renamed `stubbles\input\AbstractRequest` to `stubbles\input\ParamRequest`
* removed methods deprecated in 6.0.0
  * `stubbles\input\ValueReader::asSecureString()`, use `stubbles\input\ValueReader::asSecret()` instead
  * `stubbles\input\ValueReader::ifSatisfiesRegex()`, use `stubbles\input\ValueReader::ifMatches()` instead
  * `stubbles\input\ValueValidator::satisfiesRegex()`, use `stubbles\input\ValueValidator::matches()` instead
* removed support for `@Request[SecureString]`, use `@Request[Secret]` instead, was deprecates in 6.0.0
* `stubbles\input\Filter` is now an abstract class, not an interface
* `stubbles\input\Filter::apply()` now accepts `stubbles\values\Value` instead of `stubbles\input\Param` and must return an `array`

### Other changes

* fixed bug that `stubbles\input\ValueReader::asSecret()` removed some characters
* added optional parameter `$checkdnsrr` for `stubbles\input\ValueReader::asExistingHttpUri()` to influence which function is used for dns checks

## 6.0.0 (2016-06-12)

### BC breaks

* Raised minimum required PHP version to 5.6
* deprecated `stubbles\input\ValueReader::asSecureString()`, use `stubbles\input\ValueReader::asSecret()` instead, will be removed with 7.0.0
* deprecated `@Request[SecureString]`, use `@Request[Secret]` instead, will be removed with 7.0.0
* removed `stubbles\input\ValueReader::asEnum()`
* deprecated `stubbles\input\ValueReader::ifSatisfiesRegex()`, use `stubbles\input\ValueReader::ifMatches()` instead, will be removed with 7.0.0
* deprecated `stubbles\input\ValueValidator::satisfiesRegex()`, use `stubbles\input\ValueValidator::matches()` instead, will be removed with 7.0.0
* removed support for `@Request[File]` and `@Request[Directory]`
* removed `stubbles\input\ValueReader::ifIsFile()` and `stubbles\input\ValueReader::ifIsDirectory()`
* moved `stubbles\input\console\ConsoleRequest` to `stubbles\console\input\ConsoleRequest` in stubbles/console
* moved `stubbles\input\console\BaseConsoleRequest` to `stubbles\console\input\BaseConsoleRequest` in stubbles/console

### Other changes

* `stubbles\input\ValueReader::asJson()` now allows to specify the allowed maximum length of the JSON input
* added proper error message texts for JSON filter errors `JSON_INPUT_TOO_BIG`, `JSON_INVALID` and `JSON_SYNTAX_ERROR`

## 5.2.1 (2015-06-22)

* `stubbles\input\broker\RequestBroker::procure()` now returns the procured object

## 5.2.0 (2015-06-22)

* allow retrieval from code source for @Request[OneOf] with new attribute `allowedSource`

## 5.1.0 (2015-06-17)

* added `stubbles\input\errors\ParamError::details()`

## 5.0.0 (2015-05-28)

* removed `stubbles\input\web`, deprecated since 4.4.0
* upgrade stubbles/core to 6.0
* added `stubbles\input\filter\EnumFilter`
* added `stubbles\input\ValueReader::asEnum()`
* added `stubbles\input\broker\param\EnumParamBroker`

## 4.5.0 (2015-05-27)

* allowed serialization of param errors to JSON
* added `stubbles\input\filter\WeekFilter`
* added `stubbles\input\ValueReader::asWeek()`
* added `stubbles\input\broker\param\WeekParamBroker`

## 4.4.0 (2015-04-01)

### BC breaks

* deprecated  `stubbles\input\web`, use request implementation in stubbles/webapp-core instead, will be removed with 5.0.0

## 4.3.0 (2015-03-06)

* added `stubbles\input\ValueValidator::containsAnyOf()`
* added `stubbles\input\broker\param\MonthParamBroker`
* added `stubbles\input\broker\param\DatespanParamBroker`
* added `stubbles\input\ValueReader::asDatespan()`
* upgraded stubbles/core to 5.3
* upgraded stubbles/date to 5.2

## 4.2.0 (2014-09-29)

* added `stubbles\input\web\WebRequest::id()` which reads the value of an X-Request-ID header or generates a random value in case the header is missing or invalid
* `stubbles\input\web\WebRequest::uri()` now passes the `stubbles\peer\MalformedUriException` instead of turning it into a `\RuntimeException`
* implemented #66: Add warning when user agent injection is used
* upgraded stubbles/core to 5.1

## 4.1.1 (2014-09-01)

* fixed issue #68: message of RuntimeException in WebRequest::uri() should contain exception message of catched exception

## 4.1.0 (2014-08-18)

* implemented issue #65
  * added `stubbles\input\web\WebRequest::userAgent()`
  * deprecated user agent injection using `stubbles\input\web\useragent\UserAgentProvider`, will be removed with 5.0.0
* updated bot signatures
  * dropped DotBot
  * added Bing
  * added Pingdom
  * added Yandex

## 4.0.0 (2014-08-17)

### BC breaks

* removed all classes, methods and functions deprecated with 3.0.0
* changed all thrown stubbles/core exceptions to those recommended with stubbles/core 5.0.0
* change of annotation value names in request broker:
  * `name` must now be `paramName`
  * `group` must now be `paramGroup`
  * `description` must now be `paramDescription`
  * `option` must now be `valueDescription`

### Other changes

* upgraded stubbles/core to 5.0.0
* `stubbles\input\broker\RequestBroker` is now officially part of the API
* casting `stubbles\input\web\useragent\UserAgent` to string now returns the actual user agent string

## 3.2.0 (2014-08-14)

* upgraded stubbles/date to 5.0.0

## 3.1.1 (2014-08-10)

* added `stubbles\input\web\WebRequest::hasRedirectHeader()`

## 3.1.0 (2014-08-10)

* added shortcuts for accessing headers where a redirect version must be preferred over the changed version
  * added `stubbles\input\web\WebRequest::validateRedirectHeader()`
  * added `stubbles\input\web\WebRequest::readRedirectHeader()`

## 3.0.2 (2014-08-10)

* fixed bug that `requiresValue` of request broker annotations was not true after upgrade of stubbles/core to 4.1.x

## 3.0.1 (2014-08-04)

* fixed doc comments that yielded incorrect results for code completion hints


## 3.0.0 (2014-07-31)

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

## 2.5.1 (2014-05-07)

* added `net\stubbles\input\ValueReader::asMonth()`

## 2.5.0 (2014-05-06)

* raised minimum PHP version to 5.4.0
* the following methods now except anything that can be casted to an instance of `net\stubbles\lang\types\Date` via `net\stubbles\lang\types\Date::castFrom()`
  * `net\stubbles\input\ValueReader::asDate()`
  * `net\stubbles\input\filter\range\DateRange::__construct()`
  * `net\stubbles\input\filter\range\DatespanRange::__construct()`

## 2.4.1 (2014-02-18)

* implemented #49: `net\stubbles\input\filter\BoolFilter` should allow yes and no

## 2.4.0 (2014-01-21)

* upgraded to stubbles-core 3.4.0

## 2.3.3 (2013-12-02)

* fixed #45 ParamBrokerMap should work with lowercase versions of built-in types as well
* fixed #46 possibility to add param errors without creating a ParamError instance
  * added `net\stubbles\input\Param::add()` now also accepts an error id instead of a ParamError instance only
  * added `net\stubbles\input\ParamErrors::append()`, replaces `net\stubbles\input\ParamErrors::add()`
  * deprecated `net\stubbles\input\ParamErrors::add()`, will be removed with 2.4.0
  * deprecated `net\stubbles\input\Param::addErrorWithId()`, will be removed with 2.4.0
  * added `net\stubbles\input\ParamErrors::asList()`, replaces `net\stubbles\input\ParamErrors::get()`
  * deprecated `net\stubbles\input\ParamErrors::get()`, will be removed with 2.4.0

## 2.3.2 (2013-11-01)

* fixed bug when port is also in `$_SERVER['HTTP_HOST']` because user agent sent according host header

## 2.3.1 (2013-10-24)

* added `net\stubbles\input\filter\range\StringLength::truncate()`

## 2.3.0 (2013-05-02)

* upgraded stubbles/core to ~3.0

## 2.2.1 (2013-02-06)

* change dependency to stubbles/core from 2.1.* to ~2.1

## 2.2.0 (2012-09-06)

* implemented issue #33: allow usage of closures for filtering and validating
  * added `net\stubbles\input\ValueReader::withFunction()`
  * added `net\stubbles\input\ValueValidator::withFunction()`

## 2.1.0 (2012-07-31)

* raised stubbles-core to 2.1.*

## 2.0.2 (2012-07-25)

* added `net\stubbles\input\web\BaseWebRequest::getProtocolVersion()`

## 2.0.1 (2012-07-09)

* added `net\stubbles\input\filter\AcceptFilter`

## 2.0.0 (2012-05-22)

* Initial release.
