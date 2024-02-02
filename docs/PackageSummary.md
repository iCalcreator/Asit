[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)

> Class [It] manages array collection
* Implement _SeekableIterator_, _Countable_ and _IteratorAggregate_ methods
* Collection elements are searchable using
  * Iterator (et al.) methods
* The collection element may be of any value type


* example: 
```
use Kigkonsult\Asit\It;

class MyClass extends It {}

$myClass = MyClass::factory();
...
$myClass->append( $element );
...
$myClass->setCollection( $elementArray );
...
...
$myClass->rewind();
while( $myClass->valid()) {
    ...
    $MyClass->next();
} // end while
...
...
$myClass->last();
while( $myClass->valid()) {
    ...
    $MyClass->previous();
} // end while
...
```

[It summary here].

---

>Class [ItList]
* Extends _It_
* Assure collection elements of expected valueType
* example: 
```
use Kigkonsult\Asit\ItList;
class MyClass extends ItList {}
$myClass = MyClass::factory( ElementValueClass::class );
...
$myClass->append( new ElementValueClass());
```

[ItList summary here].

---

> Class [Asit] manages assoc array collections
* Extends _It_
* The assoc element array key is used as (unique) primary key
* A primary key may be replaced by another (unique) key
* Has primary key collection element get-/set-methods
* Collection elements are also searchable using
  * primary key(s)
* For non-assoc arrays,
  * primary key is the (numeric) array index
* example:
```
use Kigkonsult\Asit\Asit;
class MyClass extends Asit {}
$myClass = MyClass::factory();
...
$myClass->append( $element, $pKey );
...
```

[Asit summary here].

---

>Class [AsitList]
* Extends _Asit_
* Assure collection elements of expected valueType
* example:
```
use Kigkonsult\Asit\AsitList;
class MyClass extends AsitList {}
$myClass = MyClass::factory( ElementValueClass::class );
...
$myClass->append( new ElementValueClass(), $pKey );
...
```

[AsitList summary here].

---

> Class [Asmit]
* Extends _Asit_
* Allow multiple (unique) primary keys for (each)) collection element
* example:
```
use Kigkonsult\Asit\Asmit;
class MyClass extends Asmit {}
$myClass = MyClass::factory();
...
$myClass->append( $element, $pKey1 )
    ->addCurrentPkey( $pKey2 );
...
$element = $myClass->pKeyGet( $pkey1 )[0];
```

[AsmitList summary here]

---


>Class [AsmitList]
* Extends _Asmit_
* Assure collection elements of expected valueType
* example:
```
use Kigkonsult\Asit\AsmitList;
class MyClass extends AsmitList {}
$myClass = MyClass::factory( ElementValueClass::class );
...
$myClass->append( new ElementValueClass(), $pKey1 )
    ->addCurrentPkey( $pKey2 );
...
```

[AsmitList summary here].

---

>Class [Asittag]
* Extends _Asit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* Collection elements are also searchable using
  * tag(s)
  * primary key(s) + tag(s)
* example:
```
use Kigkonsult\Asit\Asittag;
class MyClass extends Asittag {}
$myClass = MyClass::factory();
...
$myClass->append( $element, $pKey, $tag );
...
```

---

>Class [AsitTagList]
* Extends _Asittag_
* Assure collection elements of expected valueType
* example:
```
use Kigkonsult\Asit\AsitTagList;
class MyClass extends AsitTagList {}
$myClass = MyClass::factory( ElementValueClass::class );
...
$myClass->append( new ElementValueClass(), $pKey, $tag );
...
```


[AsittagList summary here].

---

>Class [Asmittag]
* Extends _Asmit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* Collection elements are searchable using
  * Iterator (et al.) methods
  * primary key(s)
  * tag(s)
  * primary key(s) + tag(s)
* example:
```
use Kigkonsult\Asit\Asmittag;
class MyClass extends Asmittag {}
$myClass = MyClass::factory();
...
$myClass->append( $element, $pKey1, $tag1 )
    ->addCurrentPkey( $pKey2 )
    ->addCurrentTag( $tag2 );
...
```

[Asmittag summary here].

---

>Class [AsmitTagList]
* Extends _Asmittag_
* Assure collection elements of expected valueType
* example:
```
use Kigkonsult\Asit\AsmittagList;
class MyClass extends AsmittagList { ElementValueClass::class }
$myClass = MyClass::factory();
...
$myClass->append( new ElementValueClass(), $pKey1, $tag1 )
    ->addCurrentPkey( $pKey2 )
    ->addCurrentTag( $tag2 );
```

[AsmitTagList summary here].

---

Note, review pros and cons not using some PHP built-in iterator class, ex [ArrayIterator]!

---


Go to [README] - [It] summary - [Asit] summary - [Asittag]/[Asmittag] summary - [AsitList]/[AsmitList] summary

[It]:ItSummary.md
[It summary here]:ItSummary.md
[ItList]:ListSummary.md
[ItList summary here]:ListSummary.md
[Asit]:AsitSummary.md
[Asit summary here]:AsitSummary.md
[AsitList]:ListSummary.md
[AsitList summary here]:ListSummary.md
[Asmit]:AsitSummary.md
[Asmit summary here]:AsitSummary.md
[AsmitList]:ListSummary.md
[AsmitList summary here]:ListSummary.md
[Asittag]:AsittagSummary.md
[Asittag summary here]:AsittagSummary.md
[Asmittag]:AsittagSummary.md
[Asmittag summary here]:AsittagSummary.md
[AsitTagList]:ListSummary.md
[AsitTagList summary here]:ListSummary.md
[AsmitTagList]:ListSummary.md
[AsmitTagList summary here]:ListSummary.md
[README]:../README.md
