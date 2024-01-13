[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## It Summary

Class It implements 
* SeekableIterator, Countable and IteratorAggregate methods

The collection element may be of any valueType.

Collection elements are searchable using
* Iterator etc methods, below

It class is extended by :
* [ItList], assert collection elements of expected valueType
* [Asit], implements assoc array collection element get-/set-methods

---
 
```It::__construct( [ collection ] )```
* Class It construct method
* ```collection``` _array_ / _Traversable_
* Throws CollectionException

```It::factory( [ collection ] )```
* Class It factory method
* ```collection``` _array_ / _Traversable_
* Return _static_
* Throws CollectionException
* Static
    
```It::singleton( [ collection ] )```
* Class It singleton method
* ```collection``` _array_ / _Traversable_
* Return _static_
* Throws CollectionException
* Static

```*It::getInstance( [ collection ] )```
* It::singleton() alias


#### Get method

```It::get( [ sortParam ] )```
* Return (non-assoc) array of element(s) in collection
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable, null=>ksort
* Return _array_
* Throws SortException

    
#### Set methods

```It::append( element )```
* Append element to (array) collection
* Note, last appended element is always ```current```
* ```element``` _mixed_
* Return _static_
    
```It::setCollection( collection )```
* Set collection
* Multiple setCollections allowed, i.e. batch appends
* ```collection``` _array_ / _Traversable_
* Return _static_
* Throws CollectionException
    
```It::init()```
* Clear (remove) collection

#### Iterator etc methods

```It::count()```
* Return count of collection elements
* Required method implementing the Countable interface
* Return int

```It::current()```
* Return the current element
* Required method implementing the Iterator interface
* Return mixed

```It::exists( position )```
* Checks if position is set
* ```position``` _int_
* Return bool

```It::getIterator()```
* Return an external iterator
* Required method implementing the IteratorAggregate interface, i.e. makes the class traversable using foreach.
* Usage : ```foreach( $class as $value ) { .... }```
* Return Traversable

```It::isCollectionSet()```
* Return _bool_ - true if collection is not empty

```It::key()```
* Return the (numeric) key of the current element
* Required method implementing the Iterator interface
* Return int

```It::last()```
* Move position to last element, if empty 0
* Return _static_

```It::next()```
* Move position forward to next element
* Required method implementing the Iterator interface
* Return _static_

```It::previous()```
* Move position backward to previous element
* Return _static_
 
```It::remove()```
* Remove the current element. 
* The current element is hereafter null (i.e. NOT valid)
* Return _static_

```It::rewind()```
* Rewind the Iterator to the first element, if empty 0
* Required method implementing the Iterator interface
* Return _static_

```It::seek( position )```
* Seeks to a given position in the iterator
* Required method implementing the SeekableIterator interface
* ```position``` _int_
* Return void
* Throws OutOfBoundsException

```It::valid()```
* Checks if current position is valid
* Required method implementing the Iterator interface
* Return bool

---
Go to [README] - [Asit] summary - [Asittag] summary - [ItList] Summary 

[Asit]:AsitSummary.md
[Asittag]:AsittagSummary.md
[ItList]:ListSummary.md
[README]:../README.md
