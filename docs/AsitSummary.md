[comment]: # (This file is part of Asit, manages array collections. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## Asit Summary

Class Asit 
* Extends [It]
* implements assoc array collection element get-/set-methods

The assoc element array key is used as (unique) primary key.
A primary key may be replaced by another (unique) key.

Collection elements are searchable using
* Iterator (et al.) methods
* primary key(s)

For non-assoc arrays,
* primary key is the (numeric) array index

Asit class extends
* [Asittag], secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* [AsitList], assure collection elements of expected valueType

#### Primary key methods

```Asit::assertPkey( pKey )```
* Assert primary key, int and string allowed
* ```pKey``` _mixed_
* Return void
* static

```Asit::pKeyExists( pKey )```
* ```pKey``` _int_|_string_
* Return _bool_ true if primary key is set

```Asit::getPkeys( [ sort ] )```
* ```sort``` _int_ default _SORT_REGULAR_
* Return _array_  primary keys

```Asit::getCurrentPkey()```
* Return primary key for ```current```
* To be used in parallel with the Iterator ```current``` method, below
* Return _int_|_string_
* Throws RuntimeException

#### Get-methods

```Asit::get( [ pKeys ] )```
* Return (non-assoc) array of element(s) in collection
* If primary keys (pKeys) are given, the return collection element matching the primary keys.
* ```pKeys``` _int_|_string_|_array_
* Return _array_

```Asit::pKeyGet( pKeys )```
* Return (non-assoc array) sub-set of element(s) in collection using primary keys
* Convenient ```get``` method alias
* ```pKeys``` _int_|_string_|_array_
* Return _array_

#### Set methods

```Asit::append( element [, pKey ] )```
* Append element to (array) collection, opt with primary key (pKey)
* Note, last appended element is always ```current```
* ```element``` _mixed_
* ```pKey``` _int_|_string_  MUST be unique
* Return _static_
* Throws InvalidArgumentException
    
```Asit::replacePkey( oldPkey, newPkey )```
* Replace (set) primary key for collection element
* ```oldPkey``` _int_|_string_
* ```newPkey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException

```Asit::setCurrentPkey( pKey )```
* Set (i.e. reset) primary key for ```current``` element
* To be used in parallel with the Iterator ```current``` method, below
* ```pKey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException
* Throws RuntimeException

#### Current element primary key methods summary

```Asit::getCurrentPkey()```
* Return primary key for ```current```

```Asit::setCurrentPkey( pKey )```
* Set (i.e. reset) primary key for ```current``` element

```Asit::append( element, pKey )```
* Note, last appended element is always ```current```

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key

#### Iterator et al. related methods

```Asit::GetPkeyIterator()```
* Return an external iterator ( pKey => element )
* Return _Traversable_

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key
* ```pKey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException

---
Go to [README] - [Asittag] summary - [AsitList] Summary 

[It]:ItSummary.md
[AsitList]:ListSummary.md
[Asittag]:AsittagSummary.md
[README]:../README.md
