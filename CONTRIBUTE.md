# Notes for contributors

Please have in mind that this library is PSR-2 compliant. No changes can be merged that are not PSR-2 compliant.

## Testing

The code should have a 100% code coverage. To run the code coverage is not necessary - it will run on pull request. If
There is something not testable we either find a solution or mark it with @ignoreCodeCoverage.

To run the tests you just have to install the dev dependencies with `composer install`. Then you can start both:
Unit tests `composer test` and code sniffer `composer code-style`.

## Documentation

You should update the documentation according to your changes.
 
In order to update the the API reference you will also have to install phpdoc. Globally 
`composer global require phpdocumentor/phpdocumentor` or locally `composer install phpdocumentor/phpdocumentor`.
Please don't commit this to composer.json.

Also you will need an additional method for the `MethodDescriptor` class to make the template work. Have look in this
issue: [phpDocumentor2#1809](https://github.com/phpDocumentor/phpDocumentor2/issues/1809).
