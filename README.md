# PhpParsecsvValidator

## Description
This library adds some validation APIs to validate CSV objects.
The objects must be of type `ParseCsv\Csv`.

## How to use
```php
$header = ['title', 'price', 'categories', 'attributes'];
$reqHeaders = ['title', 'price'];

$validator = new CsvValidator($csv);
$validator
    ->validateHeadersAreEqual($header)
    ->validateDataStructure($header)
    ->validateDataForRequiredHeaders($reqHeaders])
;

if (count($validator->getErrors() > 0) {
    // file is invalid. check the errors.
} else {
    // file is valid. continue coding.
}
```