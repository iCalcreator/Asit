[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## Asit/Asmit Summary

*  implements the PkeyInterface

>Class **Asit** 
* Extends [It]
* implements _assoc_ array collection element get-/set-methods

The assoc element array key is used as (unique) primary key.
A primary key may be replaced by another (unique) key.

>Class **Asmit**
* extends Asit 
* accepts multiple (_assoc_) (unique) primary keys for (each) collection element

Collection elements are searchable using
* Iterator (et al.) methods
* primary key(s)

For non-assoc arrays,
* primary key is the (numeric) array index


Asit example:

```
use Kigkonsult\Asit\Asit;
class MyClass extend Asit {}
$myClass = MyClass::factory();
...
$myClass->append( $element, $pKey );
...
```


The Asit class is extended by :
* [Asittag], secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* [AsitList], assert collection elements of expected valueType

The Asmit class is extended by :
* [Asmittag], secondary keys, additional (non-unique) tags (aka attributes?) may be set for each element
* [AsmitList], assert collection elements of expected valueType

#### Inherited methods

Inherit methods from [It]

#### Primary key methods

```Asit::assertPkey( pKey )```
* Assert primary key, int and string allowed
* ```pKey``` _mixed_
* Return void
* Throws PkeyException
* Static

```Asit::pKeyExists( pKey )```
* ```pKey``` _int_|_string_
* Return _bool_ true if primary key is set

```Asmit::countPkeys( pKey )```
* Return count of primary keys for collection element
* ```Asmit``` only
* ```pKey``` _int_|_string_
* Throws PkeyException
* Return _int_

```Asit::getPkeys( [ sortFlag ] )```
* ```sortFlag``` _int_ default _SORT_REGULAR_
* Return _array_  all primary keys

```Asit::getCurrentPkey()```
* Return primary key for ```current```
* Return _int_|_string_
* Throws PositionException

```Asmit::removePkey( pKey )```
* ```Asmit``` only
* Remove primary key for collection element but not last
* ```pKey``` _int_|_string_
* Throws PkeyException
* Return _int_

#### Get methods

```Asit::pKeyFetch( pKey )```
* Return element for a primary key
* ```pKey``` _int_|_string_
* Return _mixed_
* Throws PkeyException

```Asit::get( [ pKeys [, sortParam ]] )```
* Return (non-assoc) array of element(s) in collection, using the opt. primary keys for selection.
* ```pKeys``` _int_|_string_|_array_
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable, null=>ksort
* Return _array_ 
* Throws SortException

```Asit::pKeyYield()```
* Implements Generators yield functionality, Return collection [ pKey => ] element
* A memory minimizer for use in foreach-loops, replaces get(), getIterator() etc
* Return _mixed_

```Asit::pKeyGet( pKeys [, sortParam ] )```
* Return (non-assoc array) sub-set of element(s) in collection using primary keys
* Convenient ```get``` method alias
* ```pKeys``` _int_|_string_|_array_
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable, null=>ksort
* Return _array_
* Throws SortException

#### Set methods

```Asit::append( element [, pKey ] )```
* Append element to (array) collection, opt with primary key (pKey)
* Note, last appended element is always ```current```
* ```element``` _mixed_
* ```pKey``` _int_|_string_  MUST be unique
* Return _static_
* Throws PkeyException
    
```Asit::setCollection( collection )```
* Set collection using array key as primary key
* Multiple setCollections allowed, i.e. batch appends
  * note, unique primary keys
* ```collection``` _array_ / _Traversable_
* Return _static_
* Throws CollectionException, PkeyException

```Asit::replacePkey( oldPkey, newPkey )```
* Replace (set) primary key for collection element
* ```oldPkey``` _int_|_string_
* ```newPkey``` _int_|_string_
* Return _static_
* Throws PkeyException

```Asit::setCurrentPkey( pKey )```
* ```Asit``` : alter primary key for ```current``` element
* ```Asmit``` : add primary key for ```current``` element
* To be used in parallel with the Iterator ```current``` method, below
* ```pKey``` _int_|_string_
* Return _static_
* Throws PositionException, PkeyException

```Asmit::addCurrentPkey( pKey )```
* ```Asmit``` : add primary key for ```current``` element
* To be used in parallel with the Iterator ```current``` method, below
* ```pKey``` _int_|_string_
* Return _static_
* Throws PositionException, PkeyException

#### Current element primary key methods summary

```Asit::getCurrentPkey()```
* Return primary key for ```current```
* Throws PositionException

```Asmit::getCurrentPkey()```
* Return first found primary key for ```current```
* Throws PositionException

```Asmit::getCurrentPkeys()```
* Return primary keys for ```current```
* Throws PositionException

```Asit::setCurrentPkey( pKey )```
* ```Asit``` : alter primary key for ```current``` element
* Return _static_
* Throws PkeyException, PositionException

```Asmit::addCurrentPkey( pKey )```
* ```Asmit``` : add primary key for ```current``` element
* Return _static_
* Throws PkeyException, PositionException

```Asit::append( element, pKey )```
* Append element to (array) collection, opt with primary key (pKey)
* Note, last appended element is always ```current```
* Return _static_
* Throws PkeyException

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key
* ```pKey``` _int_|_string_
* Return _static_
* Throws PkeyException

#### Iterator et al. related methods

```Asit::getPkeyIterator()```
* Return an external iterator ( pKey => element )
* For ```Asmit``` and in case of multiple primary keys for element, first found is used
* Return _Traversable_

---
Go to [README] - [It] summary - [Asittag]/[Asmittag] summary - [AsitList]/[AsmitList] summary

[It]:ItSummary.md
[AsitList]:ListSummary.md
[AsmitList]:ListSummary.md
[Asittag]:AsittagSummary.md
[Asmittag]:AsittagSummary.md
[README]:../README.md
