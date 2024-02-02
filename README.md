[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)

## Asit

The Asit package manages class (array) collection of elements extending the Iterator interface.

- no, single or multi primary (assoc) keys
- (multi-)label collection elements using tags
- no or specified collection (list) element value type
- simple class usage: ```class MyClass extend Asit {}``` 

###### Base classes summary

* Overview [summary here].


* [It] - collection of elements
* [Asit/Asmit] - assoc collection of elements, opt multi key
* [Asittag/Asmittag] - as above but with tags
* It/Asit/Asmit/Asitag/Asmitag[List] - as above but with spec. collection element valueType 

###### Sponsorship

Donations using _[buy me a coffee]_ or _[paypal me]_ are appreciated.
For invoice, please e-mail.

###### INSTALL

``` php
composer require kigkonsult/asit:dev-master
```

Composer, in your `composer.json`:

``` json
{
    "require": {
        "kigkonsult/asit": "dev-master"
    }
}
```
Version 2.2.x supports PHP 8.0, 2.0 7.4, 1.8.2 7.0. 

Composer, acquire access
``` php
use Kigkonsult\Asit\Asit;
...
include 'vendor/autoload.php';
```

Otherwise , download and acquire

``` php
use Kigkonsult\Asit\Asit;
...
include 'pathToSource/kigkonsult/Asit/autoload.php';
```


###### Support

For support go to [github.com Asit]


###### License

This project is licensed under the LGPLv3 License


[ArrayIterator]:https://www.php.net/manual/en/class.arrayiterator
[Asit/Asmit]:docs/AsitSummary.md
[Asittag/Asmittag]:docs/AsittagSummary.md
[buy me a coffee]:https://www.buymeacoffee.com/kigkonsult
[Composer]:https://getcomposer.org/
[github.com Asit]:https://github.com/iCalcreator/Asit
[It]:docs/ItSummary.md
[List]:docs/ListSummary.md
[paypal me]:https://paypal.me/kigkonsult
[summary here]:docs/PackageSummary.md
