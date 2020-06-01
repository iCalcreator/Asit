[comment]: # (This file is part of Asit, manages array collections. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)

## Asit

> Class It manages array collections
* Implement _SeekableIterator_, _Countable and _IteratorAggregate_ methods,
* The collection element may be of any value type.

>Class ItList
* Extends _It_
* Assure collection elements of expected valueType 

> Class Asit manages assoc array collections
* Extends _It_ 
* The assoc element array key is used as (unique) primary key.
* A primary key may be replaced by another (unique) key.
* Has primary key collection element get-/set-methods
* Collection elements are searchable using
  * Iterator (et al.) methods
  * primary key(s)
* For non-assoc arrays,
  * primary key is the (numeric) array index

>Class AsitList
* Extends _Asit_
* Assure collection elements of expected valueType
 
>Class Asittag
* Extends _Asit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element.
* Collection elements are searchable using
  * Iterator (et al.) methods
  * primary key(s)
  * tag(s)
  * primary key(s) + tag(s)

>Class AsittagList
* Extends _Asittag_
* Assure collection elements of expected valueType 

###### Method summary
* [It Summary] 
* [Asit Summary] 
* [Asittag Summary]
* It/Asit/Asitag[List Summary]

###### Sponsorship

Donation using <a href="https://paypal.me/kigkonsult?locale.x=en_US" rel="nofollow">paypal.me/kigkonsult</a> are appreciated. 
For invoice, <a href="mailto:ical@kigkonsult.se">please e-mail</a>.

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

Composer, acquire access
``` php
use Kigkonsult\Asit\Asit;
...
include 'vendor/autoload.php';
```

Otherwise , download and acquire..

``` php
use Kigkonsult\Asit\Asit;
...
include 'pathToSource/kigkonsult/Asit/autoload.php';
```


###### Support

For support go to [github.com Asit]


###### License

This project is licensed under the LGPLv3 License


[Asit Summary]:docs/AsitSummary.md
[Asittag Summary]:docs/AsittagSummary.md
[List Summary]:docs/ListSummary.md
[Composer]:https://getcomposer.org/
[github.com Asit]:https://github.com/iCalcreator/Asit
[It Summary]:docs/ItSummary.md
