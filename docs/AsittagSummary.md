[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## Asittag/Asmittag Summary

> Class Asittag 
* extends [Asit] 
  * (unique) primary key for each collection element

> Class Asmittag 
* extends [Asmit]
  * accepts multiple (unique) primary keys for (each) collection element

Also secondary keys, additional (non-unique) tags (aka attributes?)
may be set for each element. Tags are of int or string valueType.

Collection elements are searchable using
* Iterator (et al.) methods
* primary key(s)
* tag(s)
* primary key(s) + tag(s)

For non-assoc arrays,
* primary key is the (numeric) array index
* may also have tags
 
Asittag example:
```
use Kigkonsult\Asit\Asittag;
class MyClass extend Asittag {}
$myClass = MyClass::factory();
...
```

The Asittag class extends :
* [AsittagList], assert collection elements of expected valueType

The Asmittag class extends :
* [AsmittagList], assert collection elements of expected valueType

#### Inherited methods

Inherited methods from [It] - [Asit]

#### Pkey - Tag methods

```Asittag::getPkeys( [ tag [, sortFlag ]] )```
* Return all primary keys, primary keys for collection elements using tag or empty array on not found
* Override parent
* ```tag``` _int_|_string_
* ```sortFlag``` _int_ default _SORT_REGULAR_
* Return _array_

```Asittag::getTags( [ pKey [, sortFlag ]] )```
* Return all tags, tags for one collection element using the primary key or empty array on not found
* ```pKey``` _int_|_string_
* ```sortFlag``` _int_ default _SORT_REGULAR_
* Return _array_

```Asittag::getPkeyTags( pKey [, sortFlag ] )```
* Return tags for one collection element using the primary key or empty array on not found
* Convenient getTags method alias
* ```pKey``` _int_|_string_
* ```sortFlag``` _int_ default _SORT_REGULAR_
* Return _array_

```Asittag::hasPkeyTag( pKey, tag )```
* Return bool true if element (identified by pKey) has tag(s), not found pKey/tag return false
* ```pKey``` _int_|_string_
* ```tag``` _int_|_string_|_array_
* Return _bool_
   

#### Tag methods


```assertTag( tag )```
* Assert tag, int and string allowed
* ```tag``` _mixed_
* Return _void_
* Throws TagException
* Static

```Asittag::tagExists( tag )```
* Return bool true if single or any tag in array are set
* ```tag``` _int_|_string_|_array_
* Return _bool_

```Asittag::getCurrentTags()```
* Return tags for ```current```
* Return _array_
* Throws RuntimeException

```Asittag::hasCurrentTag( tag )```
* Return bool true if ```current``` has tag(s)
* ```tag``` _int_|_string_|_array_
* Return _bool_
* Throws RuntimeException

```Asittag::tagCount( tag )```
* Return count of collection element using the tag, not found return 0
* ```tag``` _int_|_string_
* Return _bool_

#### Get methods

```Asittag::pKeyTagGet( [ pKeys [, tags [, union [, exclTags [, sortParam ]]]]] )```
* Return (non-assoc) array of element(s) in collection, opt using primary keys and/or tag(s)
* If primary keys are given, the return collection element includes only these matching the primary keys.
* Then, and if tags are given and if union is bool true, the result collection element hits match all tags, false match any tag.
* Hits with exclTags are excluded
* Override parent
* ```pKeys``` _int_|_string_|_array_
* ```tags``` _int_|_string_|_array_   none-used tag is skipped
* ```union``` _bool_ default true
* ```exclTags``` _int_|_string_|_array_ tags to exclude
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable, null=>ksort
* Return _array_
* Throws SortException

```Asittag::tagGet( tags [, union [, exclTags [, sortParam ]]]] )```
* Return (non-assoc array) sub-set of element(s) in collection using tags
* If union is bool true, the result collection element hits match all tags, false match any tag.
* Convenient get method alias
* ```tags``` _int_|_string_|_array_   none-used tag is skipped
* ```union``` _bool_ default true
* ```exclTags``` _int_|_string_|_array_ tags to exclude
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable, null=>ksort
* Return _array_
* Throws SortException

#### Set methods

```Asittag::append( element [, pKey [, tags ]] )```
* Append element to (array) collection, opt with primary key and/or tags (secondary keys)
* Note, last appended element is always ```current```
* Override parent
* ```element``` _mixed_ 
* ```pKey``` _int_|_string_  MUST be unique
* ```tags``` _array_
* Return _static_
* Throws PkeyException, TagException

```Asittag::addPkeyTag( pKey, tag )```
* Add tag (secondary key) for primary key element
* ```pKey``` _int_|_string_
* ```tag``` _int_|_string_
* Return _static_
* Throws PkeyException, TagException

```Asittag::addCurrentTag( tag )```
* Add tag (secondary key) for ```current```
* ```tag``` _int_|_string_
* Return _static_
* Throws RuntimeException, TagException

#### Remove methods

```Asittag::removePkeyTag( pKey, tag )```
* Remove tag (secondary key) for primary key element
* ```pKey``` _int_|_string_
* ```tag``` _int_|_string_
* Return _static_
* Throws PkeyException

```Asittag::removeCurrentTag( tag )```
* Remove tag (secondary key) for ```current```
* ```tag``` _int_|_string_
* Return _static_
* Throws RuntimeException
    

#### Current element tag methods summary

```Asittag::hasCurrentTag( tag )```
* Return bool true if ```current``` has tag(s)
* Throws RuntimeException

```Asittag::addCurrentTag( tag )```
* Add tag (secondary key) for ```current```
* Throws RuntimeException, TagException

```Asittag::removeCurrentTag( tag )```
* Remove tag (secondary key) for ```current```
* Throws RuntimeException

```Asittag::append( element, pKey, tags )```
* Append element to (array) collection, with primary key and/or tags (secondary keys)
* Note, last appended element is always ```current```
* Throws PkeyException, TagException

---
Go to [README] - [It] summary - [Asit] / [Asmit] summary - [AsittagList] / [AsmittagList] summary 

[It]:ItSummary.md
[Asit]:AsitSummary.md
[Asmit]:AsitSummary.md
[AsittagList]:ListSummary.md
[AsmittagList]:ListSummary.md
[README]:../README.md
