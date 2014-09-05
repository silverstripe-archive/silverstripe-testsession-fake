# Test Session Fakes

## Introduction

Allows substition of arbitrary PHP objects in the [SilverStripe framework](http://silverstripe.org)
as part of the request bootstrap, by replacing their implementation using SilverStripe's
[dependency injector](http://doc.silverstripe.org/framework/en/trunk/reference/injector).

The fakes are activated through the [testsession](https://github.com/silverstripe-labs/silverstripe-testsession) module,
which creates testing flags within the project structure and hence allows these objects
to be recreated across multiple requests. A lightweight ["fake database"](https://github.com/chillu/fakedatabase) 
optionally allows these objects to share state across multiple requests.

The modules is useful to test web service implementations, for example
calling "create user" on a web service fake in the first request,
storing its data in the fake database, which can then be used
to list this new user on subsequent web service calls.

## Configuration

In its most basic usage, you provide a mapping of service names (usually PHP classes)
to their fake implementations, through YAML config:

Example: A class and its fake
```php
class MyService {
	function someTask() {
		// do something real, like querying a web service
	}
}

class MyServiceFake {
	function someTask() {
		// do something fake, like returning a hardcoded value
	}
}
```

mysite/_config/config.yml
```yml
FakeManager:
  services:
    MyService: MyServiceFake
```

## Usage

### Predefined Fake Databases

For manual testing sessions, it can be useful to predefine some fake data instead
of build it up incrementally on the fly. For example, you might want to respond
a list of existing users by a fake webservice call, but still allow other services
to add new users to the same list.

mysite/_config/config.yml
```yml
FakeManager:
  database_template_paths:
    - mysite/tests/fixtures/MyFakeDatabase.json
```

The format is the same as the files which are generated on the fly.
The following example could be queried through `$db->get('Currency', 'NZD')->value`.

```js
{
	"Currency": {
		"NZD": {
			"value": 1.23
		}
	}
}
```