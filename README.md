# Symfony Annotated Commands

Create CLI applications with many commands easily. This library is a lightweight wrapper around Symfony Console 
Component, that can be used alone or within Symfony based web application.

## Contributing

### Running Tests

To run all the tests, install the vendors (with Composer) and execute:
```
$ vendor/bin/phpunit --testdox
```

### Public API

All classes that are intended to be used by a user should be marked with `@api` PHPDoc tag. Classes without this mark 
are internal and should not be used by the end user (we do not guarantee that the interface will stay the same between 
versions).

## Alternatives

* https://github.com/consolidation/annotated-command — similar approach, but from a different angle
