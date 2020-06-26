[comment]: # (This file is part of Asit, manages array collections. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)

## Asit

> Class It manages array collections
* Implement _SeekableIterator_, _Countable_ and _IteratorAggregate_ methods
* Collection elements are searchable using
  * Iterator (et al.) methods
* The collection element may be of any value type

>Class ItList
* Extends _It_
* Assure collection elements of expected valueType 

> Class Asit manages assoc array collections
* Extends _It_ 
* The assoc element array key is used as (unique) primary key
* A primary key may be replaced by another (unique) key
* Has primary key collection element get-/set-methods
* Collection elements are also searchable using
  * primary key(s)
* For non-assoc arrays,
  * primary key is the (numeric) array index

>Class AsitList
* Extends _Asit_
* Assure collection elements of expected valueType
 
> Class Asmit
* Extends _Asit_
* Allow multiple (unique) primary keys for (each)) collection element

>Class Asittag
* Extends _Asit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* Collection elements are also searchable using
  * tag(s)
  * primary key(s) + tag(s)

>Class AsittagList
* Extends _Asittag_
* Assure collection elements of expected valueType 

>Class Asmittag
* Extends _Asmit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* Collection elements are searchable using
  * Iterator (et al.) methods
  * primary key(s)
  * tag(s)
  * primary key(s) + tag(s)

>Class AsmittagList
* Extends _Asmittag_
* Assure collection elements of expected valueType 

But, review pros and cons not using some PHP built-in iterator class, ex [ArrayIterator]!

###### Method summary
* [It] summary
* [Asit/Asmit] summary 
* [Asittag/Asmittag] summary
* It/Asit/Asmit/Asitag/Asmitag[List] summary

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
[Composer]:https://getcomposer.org/
[github.com Asit]:https://github.com/iCalcreator/Asit
[It]:docs/ItSummary.md
[List]:docs/ListSummary.md
