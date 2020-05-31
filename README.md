> Class package Asit manages assoc arrays

Class Asit implements 
* SeekableIterator, Countable and IteratorAggregate methods,
* assoc array collection element get-/set-methods

The collection element may, as for Iterator (et al.), be of any type.

The assoc element array key is used as (unique) primary key.
A primary key may be replaced by another (unique) key.

Collection elements are searchable using
* Iterator (et al.) methods
* primary key(s)

For non-assoc arrays,
* primary key is the (numeric) array index

>Class Asittag extends Asit
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element.

###### Method summary
* [Asit Summary] 
* [Asittag Summary]

###### Sponsorship

Donation using <a href="https://paypal.me/kigkonsult?locale.x=en_US" rel="nofollow">paypal.me/kigkonsult</a> are appreciated. 
For invoice, <a href="mailto:ical@kigkonsult.se">please e-mail</a>.

###### INSTALL

``` php
composer require kigkonsult\Asit:dev-master
```

Composer, in your `composer.json`:

``` json
{
    "require": {
        "kigkonsult\Asit": "dev-master"
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
[Composer]:https://getcomposer.org/
[github.com Asit]:https://github.com/iCalcreator/Asit
