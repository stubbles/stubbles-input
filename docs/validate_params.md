Parameter validation
--------------------

Parameter validation can be used to check if the parameter with the given name
is valid. In case the parameter does not exist or is not valid the validation
will evaluate to `false`, otherwise to `true`.


#### `contains($contained): bool`

Returns `true` if the request parameter contains the value given with `$contained`.

```php
if ($request->validateParam($paramName)->contains('foo')) {
    // do something, given parameter exists and contains foo
}
```


#### `containsAnyOf(array $elements): bool`

Returns `true` if the request parameter contains any of the values given with `$elements`.

```php
if ($request->validateParam($paramName)->containsAnyOf(['foo', 'bar', 'baz'])) {
    // do something, given parameter exists and contains foo, bar or baz
}
```


#### `isEqualTo($expected): bool`

Returns `true` if the request parameter is equal to the value given with `$expected`.

```php
if ($request->validateParam($paramName)->isEqualTo('foo')) {
    // do something, given parameter exists and equals foo
}
```


#### `isHttpUri(): bool`

Returns `true` if the request parameter represents an HTTP URI.

```php
if ($request->validateParam($paramName)->isHttpUri()) {
    // do something, given parameter exists and is a HTTP URI
}
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### `isExistingHttpUri(): bool`

Returns `true` if the request parameter represents an HTTP URI and this URI does exist.

```php
if ($request->validateParam($paramName)->isExistingHttpUri()) {
    // do something, given parameter exists and is an existing HTTP URI
}
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### `isIpAddress(): bool`

Returns `true` if the request parameter represents a valid IP address, either IPv4 or IPv6.

```php
if ($request->validateParam($paramName)->isIpAddress()) {
    // do something, given parameter exists and is a valid IP address
}
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### `isIpV4Address(): bool`

Returns `true` if the request parameter represents a valid IPv4 address.

```php
if ($request->validateParam($paramName)->isIpV4Address()) {
    // do something, given parameter exists and is a valid IPv4 address
}
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### `isIpV6Address(): bool`

Returns `true` if the request parameter represents a valid IPv6 address.

```php
if ($request->validateParam($paramName)->isIpV6Address()) {
    // do something, given parameter exists and is a valid IPv6 address
}
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### `isMailAddress(): bool`

Returns `true` if the request parameter represents a valid mail address.

```php
if ($request->validateParam($paramName)->isMailAddress()) {
    // do something, given parameter exists and is a valid mail address
}
```

Note: requires _[stubbles/peer](https://github.com/stubbles/stubbles-peer)_.


#### `isOneOf(array $allowedValues): bool`

Returns `true` if the request parameter is equal to one of the values given with `$allowedValues`.

```php
if ($request->validateParam($paramName)->isOneOf(['foo', 'bar', 'baz'])) {
    // do something, given parameter exists and is either of value foo, bar or baz
}
```


#### `matches(string $regex): bool`

Returns `true` if the request parameter matches the regular expression given with `$regex`.

```php
if ($request->validateParam($paramName)->matches('/^[0-9]+$/')) {
    // do something, given parameter exists and is matched by given regular expression
}
```


#### `with(callable $predicate): bool`

_Available since release 3.0.0_

Returns `true` if the request parameter can be validated with the given closure.

```php
if ($request->validateParam($paramName)->with(function($value) { return 303 == $value; })) {
    // do something, given parameter exists and is approved by given callable
}
```
