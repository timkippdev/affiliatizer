# Affiliatizer

The Affiliatizer provides a way to add affiliate info to any configured url.

## Usage

```php
<?php

use TimKippDev\Affiliatizer\Affiliatizer;

...

$affiliateConfig = [
    'test-replace.com' => [ 
        'type' => Affiliatizer::AFFILIATIZER_TYPE_REPLACEMENT, // 'replace'
        'replacers' => [
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

// pass the original url to the `affiliatizeUrl` method
echo $affiliatizer->affiliatizeUrl('https://test-replace.com/somewhere'); // you should get https://test-replace.com/somewhere?aff-tag1=aff-value1&aff-tag2=aff-value2

echo $affiliatizer->affiliatizeUrl('https://test-redirect.com/somewhere'); // you should get https://redirect-to-me.com/?url=https%3A%2F%2Ftest-redirect.com%2Fsomewhere
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