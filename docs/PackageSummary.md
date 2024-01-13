[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)

> Class [It] manages array collections
* Implement _SeekableIterator_, _Countable_ and _IteratorAggregate_ methods
* Collection elements are searchable using
  * Iterator (et al.) methods
* The collection element may be of any value type

>Class [ItList]
* Extends _It_
* Assure collection elements of expected valueType

> Class [Asit] manages assoc array collections
* Extends _It_
* The assoc element array key is used as (unique) primary key
* A primary key may be replaced by another (unique) key
* Has primary key collection element get-/set-methods
* Collection elements are also searchable using
  * primary key(s)
* For non-assoc arrays,
  * primary key is the (numeric) array index

>Class [AsitList]
* Extends _Asit_
* Assure collection elements of expected valueType

> Class [Asmit]
* Extends _Asit_
* Allow multiple (unique) primary keys for (each)) collection element

>Class [Asittag]
* Extends _Asit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* Collection elements are also searchable using
  * tag(s)
  * primary key(s) + tag(s)

>Class [AsitTagList]
* Extends _Asittag_
* Assure collection elements of expected valueType

>Class [Asmittag]
* Extends _Asmit_
* Also secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* Collection elements are searchable using
  * Iterator (et al.) methods
  * primary key(s)
  * tag(s)
  * primary key(s) + tag(s)

>Class [AsmitTagList]
* Extends _Asmittag_
* Assure collection elements of expected valueType

But, review pros and cons not using some PHP built-in iterator class, ex [ArrayIterator]!

---
Go to [README] - [It] summary - [Asit] summary - [Asittag]/[Asmittag] summary - [AsitList]/[AsmitList] summary

[It]:ItSummary.md
[ItList]:ListSummary.md
[Asit]:AsitSummary.md
[AsitList]:ListSummary.md
[Asmit]:AsitSummary.md
[AsmitList]:ListSummary.md
[Asittag]:AsittagSummary.md
[Asmittag]:AsittagSummary.md
[AsitTagList]:ListSummary.md
[AsmitTagList]:ListSummary.md
[README]:../README.md
