Read parameters
---------------

More often validating that a certain parameter is valid it is required to read
its value and even transform it into a certain type. A value reader can be used
to do just that. These methods are offered by the value reader.

The return value of each method is either the parameter value in case the
parameter exists and is accepted by the filter. In case the filter can not
finish its work correctly it will add a param error to the error stack.

### Default values

Before any of the following methods are called a default value can be set:

```php
$int = $request->readParam($paramName)->defaultingTo(303)->asInt();
```

This default value will be used in case the parameter is not set, and will
override any default that a method may use in such a case.

Please note that the value reader will throw a `\LogicException` if the type of
the default value does not match the type of how the value as read. E.g, the
following example will throw an exception:

```php
$int = $request->readParam($paramName)->defaultingTo(new \stdClass())->asInt();

// LogicException: Default value is not of type int but of type stdClass
```


### Mark values as required

Sometimes it is useful to store an error saying that a parameter value is
required. The value reader allows to do that:

```php
$int = $request->readParam($paramName)->required()->asInt();
```

In case the parameter is not set this will lead to an error being stored in the
parameter error list with _FIELD_EMPTY_, and the subsequent value of `$int` will
be `null`.


### `asArray(string $separator = ',')`

Returns the request parameter as `array` value. In case the the parameter is not
set the method returns `null`. In case the parameter is an empty string the
return value is an empty array.

```php
$array = $request->readParam($paramName)->asArray();
```

By default the parameter value will be split on commas. This can be changed by
providing another separator value via the `$separator` parameter.


### `asBool($default = null)`

Returns the request parameter as `boolean` value. In case the the parameter is
not set the method returns `false`.

```php
$bool = $request->readParam($paramName)->asBool()
```


### `asInt(NumberRange $range = null)`

Returns the request parameter as `int` value. In case the the parameter is not
set the method returns `0`.

```php
$int = $request->readParam($paramName)->asInt();
```

#### Specifying valid value ranges

Additionally it is possible to specify valid ranges for the value to filter:

```php
// accepts all values between and including 1 to 10
$int = $request->readParam($paramName)->asInt(new NumberRange(1, 10));
// accepts all positive numbers >= 1
$int = $request->readParam($paramName)->asInt(new NumberRange(1, null));
// accepts all negative numbers <= -1
$int = $request->readParam($paramName)->asInt(new NumberRange(null, -1));
```

#### Possible filter errors

 * _VALUE_TOO_SMALL_  in case a min value has been set and the value is smaller
 * _VALUE_TOO_GREAT_  in case a max value has been set and the value is greater


## `asFloat(NumberRange $range = null, $decimals = null)`

Returns the request parameter as `float` value. In case the the parameter is not
set the method returns `0.0`.

```php
    $float = $request->readParam($paramName)->asFloat();
```

#### Specifying valid value ranges

Additionally it is possible to specify valid ranges for the value to filter:

```php
// allows all values between and including 1 to 10
$float = $request->readParam($paramName)->asFloat(new NumberRange(1, 10));
// accepts all positive numbers >= 1
$float = $request->readParam($paramName)->asFloat(new NumberRange(1, null));
// accepts all negative numbers <= -1
$float = $request->readParam($paramName)->asFloat(new NumberRange(null, -1));
```

#### Possible filter errors

 * _VALUE_TOO_SMALL_  in case a min value has been set and the value is smaller
 * _VALUE_TOO_GREAT_  in case a max value has been set and the value is greater


### `asString(StringLength $length = null)`

Returns the request parameter as `string` value. In case the the parameter is
not set returns an empty string.

```php
$string = $request->readParam($paramName)->asString();
```

#### Specifying valid string lengths

Additionally it is possible to specify a valid length for the value to filter:

```php
// allows all strings with a length between and including 1 to 10
$string = $request->readParam($paramName)->asString(new StringLength(1, 10));
// accepts all strings with length >= 1
$string = $request->readParam($paramName)->asString(new StringLength(1, null));
// accepts all strings with length <= 10
$string = $request->readParam($paramName)->asString(new StringLength(null, 10));
```

#### Possible filter errors

 * _STRING_TOO_SHORT_  in case a min length has been set and the value is shorter
 * _STRING_TOO_LONG_  in case a max length has been set and the value is longer


### `asText(StringLength $length = null, $allowedTags = [])`

Returns the request parameter as `string` value. In case the the parameter is
not set the method returns an empty string. The difference to `asString()` is
that this allows line breaks, and does not filter tags when they are allowed.

```php
$text = $request->readParam($paramName)->asText();
```

#### Specifying valid text lengths

Additionally it is possible to specify a valid length for the value to filter:

```php
// allows all strings with a length between and including 1 to 10
$text = $request->readParam($paramName)->asText(new StringLength(1,10));
// accepts all strings with length >= 1
$text = $request->readParam($paramName)->asText(new StringLength(1, null));
// accepts all strings with length <= 10
$text = $request->readParam($paramName)->asText(new StringLength(null, 10));
```

#### Possible filter errors

 * _STRING_TOO_SHORT_  in case a min length has been set and the value is shorter
 * _STRING_TOO_LONG_  in case a max length has been set and the value is longer


### `asSecret(StringLength $length = null)`

This will read the parameter as string, but return it enclosed as an instance of
[`stubbles\values\Secret`](https://github.com/stubbles/stubbles-values#stubblesvaluessecret).

```php
$secretToken = $request->readParam($paramName)->asSecret();
```

#### Specifying valid secret lengths

Additionally it is possible to specify a valid length for the value to filter:

```php
// allows all strings with a length between and including 1 to 10
$string = $request->readParam($paramName)->asSecret(new StringLength(1, 10));
// accepts all strings with length >= 1
$string = $request->readParam($paramName)->asSecret(new StringLength(1, null));
// accepts all strings with length <= 10
$string = $request->readParam($paramName)->asSecret(new StringLength(null, 10));
```

#### Possible filter errors

 * _STRING_TOO_SHORT_  in case a min length has been set and the value is shorter
 * _STRING_TOO_LONG_  in case a max length has been set and the value is longer


### `asPassword(PasswordChecker $checker)`

This will read the parameter as string, but return it enclosed as an instance of
[`stubbles\values\Secret`](https://github.com/stubbles/stubbles-values#stubblesvaluessecret)
and applying the the given password checker to verify the given value can become
an allowed password.

```php
 $password = $request->readParam($paramName)->asPassword(new SimplePasswordChecker());
```

Possible filter errors depend on the used implementation for the password checker.


### `asJson($maxLength = 20000)`

Returns the request parameter as `array` or `\stdClass` value, depending on the
JSON structure. In case the the parameter is not set or contains invalid JSON
the method returns `null`.

```php
$decodedJson = $request->readParam($paramName)->asJson();
```

#### Specifying valid JSON string length

Additionally it is possible to specify a valid length for the value to filter:

```php
// accept all json strings up to a length of 1000 bytes
$decodedJson = $request->readParam($paramName)->asJson(1000);
```

If no length is specified the default of 20,000 bytes is used.

#### Possible filter errors

 * _JSON_INPUT_TOO_BIG_  in case the parameter value is longer than 20,000 characters
 * _JSON_INVALID_  in case the parameter value is not valid JSON
 * _JSON_SYNTAX_ERROR_ in case the JSON structure seems to have an syntax error


### `asHttpUri()`

Returns the request parameter as `stubbles\peer\http\HttpUri` instance. In case
the the parameter is not set or is not a valid HTTP URI the method returns `null`.

```php
$httpUri = $request->readParam($paramName)->asHttpUri();
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.

#### Possible filter errors

 * _HTTP_URI_INCORRECT_  in case the parameter value is not a valid HTTP URI


### `asExistingHttpUri()`

Returns the request parameter as `stubbles\peer\http\HttpUri` instance. In case
the the parameter is not set, not a valid HTTP URI or has no DNS record the
method returns `null`.

```php
$httpUri = $request->readParam($paramName)->asExistingHttpUri();
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### Possible filter errors

 * _HTTP_URI_INCORRECT_  in case the parameter value is not a valid HTTP URI
 * _HTTP_URI_NOT_AVAILABLE_  in case the parameter value is a valid HTTP URI but does not exist (e.g. has no DNS entry)


### `asMailAddress()`

Returns the request parameter as `string` if it is a valid mail address. In case
the the parameter is not set or not a valid mail address the method returns `null`.

```php
$mailAddress = $request->readParam($paramName)->asMailAddress();
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.

#### Possible filter errors

 * _MAILADDRESS_MISSING_ in case the parameter is required but not set
 * _MAILADDRESS_CANNOT_CONTAIN_SPACES_ in case the value contains any spaces
 * _MAILADDRESS_CANNOT_CONTAIN_UMLAUTS_ in case the value contains german umlauts
 * _MAILADDRESS_MUST_CONTAIN_ONE_AT_ in case the value contains less or more than one _@_ character
 * _MAILADDRESS_DOT_NEXT_TO_AT_SIGN_ in case the value contains a dot right before or after the _@_ character
 * _MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS_ in case the contains two dots next to each other
 * _MAILADDRESS_INCORRECT_ in case the exact error could not be determined


### `ifIsIpAddress()`

Returns the parameter value if it is a valid IP address, either IPv4 or IPv6.

```php
$ipAddress = $request->readParam($paramName)->ifIsIpAddress();
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.

#### Possible filter errors

 * _INVALID_IP_ADDRESS_ in case the parameter is not a valid IP address


### `asDate(DateRange $range = null)`

Returns the request parameter as a `stubbles\date\Date` instance. In case the
parameter is not set the method returns `null`.

```php
$date = $request->readParam($paramName)->asDate();
```

Note: requires _[stubbles/date](https://github.com/stubbles/stubbles-date)_.

#### Specifying valid dates

Additionally it is possible to specify a valid date range for the value to filter:

```php
// allows all dates that resemble yesterday, today and tomorrow
$date = $request->readParam($paramName)
                ->asDate(new DateRange(new Date('yesterday'), new Date('tomorrow')));
// accepts all dates in the future including tomorrow
$date = $request->readParam($paramName)->asDate(new DateRange(new Date('tomorrow'), null));
// accepts all dates in the past including yesterday
$date = $request->readParam($paramName)->asDate(new DateRange(null, new Date('yesterday')));
```

#### Possible filter errors

 * _DATE_TOO_EARLY_  in case a range was provided and the date is earlier than the allowed min date
 * _DATE_TOO_LATE_  in case a range was provided and the date is later than the allowed max date


### `asDay(DatespanRange $range = null)`

Returns the request parameter as a `stubbles\date\span\Day` instance. In case the
parameter is not set the method returns `null`.

```php
$day = $request->readParam($paramName)->asDay();
```

Note: requires _[stubbles/date](https://github.com/stubbles/stubbles-date)_.

#### Specifying valid days

Additionally it is possible to specify a valid date range for the value to filter:

```php
// allows all days that resemble yesterday, today and tomorrow
$day = $request->readParam($paramName)
               ->asDay(new DatespanRange(new Date('yesterday'), new Date('tomorrow')));
// accepts all days in the future including tomorrow
$day = $request->readParam($paramName)->asDay(new DatespanRange(new Date('tomorrow'), null));
// accepts all days in the past including yesterday
$day = $request->readParam($paramName)->asDay(new DatespanRange(null, new Date('yesterday')));
```

#### Possible filter errors

 * _DATE_TOO_EARLY_  in case a range was provided and the dayis earlier than the allowed min date
 * _DATE_TOO_LATE_  in case a range was provided and the day is later than the allowed max date
 * _DATE_INVALID_ if the value does result in a valid day


### `ifIsOneOf(array $allowedValues)`

Returns the parameter value if it is one of the list of `$allowedValues`, and `null`
otherwise.

```php
$value = $request->readParam($paramName)->ifIsOneOf(['foo', 'bar', 'baz']);
```

#### Possible filter errors

 * _FIELD_NO_SELECT_ in case the parameter is not one of the allowed values


### `ifMatches(string $regex)`

Returns the parameter value if it satisfies the regular expression given with
`$regex`. Returns `null` otherwise.

```php
    $value = $request->readParam($paramName)->ifMatches('/^[0-9]+$/');
```

### Possible filter errors

 * _FIELD_WRONG_VALUE_ in case the parameter does not satisfy the provided regular expression


### withFilter(Filter $filter)

Returns the request parameter as the type which the given `$filter` returns. The
given filter has to cope with non-set values.

```php
$value = $request->readParam($paramName)->withFilter($filter);
```

#### Possible filter errors

 * Any other error the user-defined filter adds to the param instance.


### when(callable $predicate, $errorId, array $details = [])

_Available since release 3.0.0_

Returns the value when the predicate which is called with the value returns
`true`, and `null` otherwise.

```php
  $value = $request->readParam($paramName)->when(
      function(Param $param)
      {
           return $param->getValue() == 'Roland TB-303';
      },
      'FIELD_WRONG_VALUE'
  );
```

#### Possible filter errors

 * The error specified as second parameter in case the predicate returns `false`.


### `unsecure()`

Returns the parameter value without any validation. This should be used with
greatest care.

```php
$value = $request->readParam($paramName)->unsecure();
```
