2.3.3 (2013-12-??)
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
