# Affiliatizer

The Affiliatizer provides a way to add affiliate info to any configured url.

## Usage

```php
<?php

use TimKippDev\Affiliatizer\Affiliatizer;

...

$affiliateConfig = [
    'test-append-path.com' => [ 
        'type' => Affiliatizer::AFFILIATIZER_TYPE_APPEND_PATH, // 'append-path'
        'path' => '/affiliate/readme'
    ],
    'test-append-params.com' => [ 
        'type' => Affiliatizer::AFFILIATIZER_TYPE_APPEND_PARAMS, // 'append-params'
        'params' => [
            'aff-tag1' => 'aff-value1',
            'aff-tag2' => 'aff-value2'
        ]
    ],
    'test-redirect.com' => [ 
        'type' => Affiliatizer::AFFILIATIZER_TYPE_REDIRECT, // 'redirect'
        'destination' => 'https://redirect-to-me.com/?url=<URL>'
    ]
];

$affiliatizer = new Affiliatizer($affiliateConfig);

echo $affiliatizer->affiliatizeUrl('https://test-append-path.com/somewhere') . PHP_EOL; 
// https://test-append-path.com/somewhere/affiliate/readme

echo $affiliatizer->affiliatizeUrl('https://test-append-params.com/somewhere') . PHP_EOL; 
// https://test-append-params.com/somewhere?aff-tag1=aff-value1&aff-tag2=aff-value2

echo $affiliatizer->affiliatizeUrl('https://test-redirect.com/somewhere') . PHP_EOL;
// https://redirect-to-me.com/?url=https%3A%2F%2Ftest-redirect.com%2Fsomewhere
```

## Running Tests

Run the following at the root of your project directory:
```bash
php vendor/bin/phpunit
```

You should see similar output:
```
$ php vendor/bin/phpunit
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

Runtime:       PHP 7.1.33
Configuration: /somepath/phpunit.xml

.                                                                   1 / 1 (100%)

Time: 22 ms, Memory: 4.00 MB

OK (1 test, 3 assertions)
```