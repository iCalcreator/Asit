[comment]: # (This file is part of Asit, manages array collections. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## Asit/Asmit Summary

>Class **Asit** 
* Extends [It]
* implements assoc array collection element get-/set-methods

The assoc element array key is used as (unique) primary key.
A primary key may be replaced by another (unique) key.

>Class **Asmit**
* extends Asit 
* accepts multiple (unique) primary keys for (each) collection element

Collection elements are searchable using
* Iterator (et al.) methods
* primary key(s)

For non-assoc arrays,
* primary key is the (numeric) array index

Asit class extends :
* [Asittag], secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* [AsitList], assure collection elements of expected valueType

Asmit class extends :
* [Asmittag], secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* [AsmitList], assure collection elements of expected valueType

#### Primary key methods

```Asit::assertPkey( pKey )```
* Assert primary key, int and string allowed
* ```pKey``` _mixed_
* Return void
* Static

```Asit::pKeyExists( pKey )```
* ```pKey``` _int_|_string_
* Return _bool_ true if primary key is set

```Asmit::countPkeys( pKey )```
* Return count of primary keys for collection element, not found return 0
* ```Asmit``` only
* ```pKey``` _int_|_string_
* Throws InvalidArgumentException
* Return _int_

```Asmit::removePkey( pKey )```
* ```Asmit``` only
* Remove primary key for collection element but not last
* ```pKey``` _int_|_string_
* Throws InvalidArgumentException
* Return _int_

```Asit::getPkeys( [ sortFlag ] )```
* ```sortFlag``` _int_ default _SORT_REGULAR_
* Return _array_  all primary keys

```Asit::getCurrentPkey( [ firstFound ] )```
* Return primary key for ```current```
* To be used in parallel with the Iterator ```current``` method, below
* ```firstFound``` _bool_ ```Asmit``` only, one (firstFound=true, default) or (array) all
* Return _int_|_string_|_array_
* Throws RuntimeException

#### Get-methods

```Asit::get( [ pKeys [, sortParam ]] )```
* Return (non-assoc) array of element(s) in collection, using the opt. primary keys for selection.
* ```pKeys``` _int_|_string_|_array_
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable
* Return _array_

```Asit::pKeyGet( pKeys [, sortParam ] )```
* Return (non-assoc array) sub-set of element(s) in collection using primary keys
* Convenient ```get``` method alias
* ```pKeys``` _int_|_string_|_array_
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable
* Return _array_

#### Set methods

```Asit::append( element [, pKey ] )```
* Append element to (array) collection, opt with primary key (pKey)
* Note, last appended element is always ```current```
* ```element``` _mixed_
* ```pKey``` _int_|_string_  MUST be unique
* Return _static_
* Throws InvalidArgumentException
    
```Asit::setCollection( collection )```
* Set (array) collection using array key as primary key
* ```collection``` _array_
* Return _static_
* Throws InvalidArgumentException

```Asit::replacePkey( oldPkey, newPkey )```
* Replace (set) primary key for collection element
* ```oldPkey``` _int_|_string_
* ```newPkey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException

```Asit::setCurrentPkey( pKey )```
* ```Asit``` : reset primary key for ```current``` element
* ```Asmit``` : add primary key for ```current``` element
* To be used in parallel with the Iterator ```current``` method, below
* ```pKey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException
* Throws RuntimeException

#### Current element primary key methods summary

```Asit::getCurrentPkey()```
* Return primary key for ```current```

```Asit::setCurrentPkey( pKey )```
* ```Asit``` : reset primary key for ```current``` element
* ```Asmit``` : add primary key for ```current``` element

```Asit::append( element, pKey )```
* Append element to (array) collection, opt with primary key (pKey)
* Note, last appended element is always ```current```

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key

#### Iterator et al. related methods

```Asit::getPkeyIterator()```
* Return an external iterator ( pKey => element )
* For ```Asmit``` and in case of multiple primary keys for element, first found is used
* Return _Traversable_

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key
* ```pKey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException

---
Go to [README] - [It] summary - [Asittag]/[Asmittag] summary - [AsitList]/[AsmitList] summary

[It]:ItSummary.md
[AsitList]:ListSummary.md
[AsmitList]:ListSummary.md
[Asittag]:AsittagSummary.md
[Asmittag]:AsittagSummary.md
[README]:../README.md
