stubbles/input
==============

One of the most common task in applications is to read, validate and filter
input data. _stubbles/input_ provides means to do exactly that, by providing a
Request API in order to validate and filter input values.


Build status
------------

[![Build Status](https://secure.travis-ci.org/stubbles/stubbles-input.png)](http://travis-ci.org/stubbles/stubbles-input)
[![Coverage Status](https://coveralls.io/repos/stubbles/stubbles-input/badge.png?branch=master)](https://coveralls.io/r/stubbles/stubbles-input?branch=master)

[![Latest Stable Version](https://poser.pugx.org/stubbles/input/version.png)](https://packagist.org/packages/stubbles/input)
[![Latest Unstable Version](https://poser.pugx.org/stubbles/input/v/unstable.png)](//packagist.org/packages/stubbles/input)


Installation
------------

_stubbles/input_ is distributed as [Composer](https://getcomposer.org/)
package. To install it as a dependency of your package use the following
command:

    composer require "stubbles/input": "^6.0"


Requirements
------------

_stubbles/input_ requires at least PHP 5.6.


Request parameters
------------------

The Request API provides access to request parameters in different ways:

### `getParamNames()`

Returns a list of all parameter names.

### `paramErrors()`

Returns a list of all parameter errors that occurred during filtering single
request parameters.

### `hasParam($paramName)`

Checks if a parameter with the given name is present in the current request.

### `validateParam($paramName)`

Returns a value validator with methods that can be used to check if the parameter
with the given name is valid.

See [validate parameters details](docs/validate_params.md).

### `readParam($paramName)`

Returns a value reader that can be used to read the parameter with the given
name. In case the parameter does not exist the reader will return `null`.

See [read parameters details](docs/read_params.md).
