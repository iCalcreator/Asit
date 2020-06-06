[comment]: # (This file is part of Asit, manages array collections. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## It Summary

Class It implements 
* SeekableIterator, Countable and IteratorAggregate methods

The collection element may be of any valueType.

Collection elements are searchable using
* Iterator etc methods, below

It class extends :
* [ItList], assure collection elements of expected valueType
* [Asit], implements assoc array collection element get-/set-methods

---
 
```It::__construct( [ collection ] )```
* Class Asit construct method
* ```collection``` _array_

```It::factory( [ collection ] )```
* Class Asit factory method
* ```collection``` _array_
* Return _static_
* Static
    
#### Get method

```Asit::get( [ sortParam ] )```
* Return (non-assoc) array of element(s) in collection
* ```sortParam``` _int_|_callable_  asort sort_flags or uasort callable
* Return _array_

    
#### Set methods

```It::append( element )```
* Append element to (array) collection
* Note, last appended element is always ```current```
* ```element``` _mixed_
* Return _static_
* Throws InvalidArgumentException
    
```It::setCollection( collection )```
* Set (array) collection
* ```collection``` _array_
* Return _static_
* Throws InvalidArgumentException

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
* Move position to last element
* Return _static_

```It::next()```
* Move position forward to next element
* Required method implementing the Iterator interface
* Return _static_

```It::previous()```
* Move position backward to previous element
* Return _static_

```It::rewind()```
* Rewind the Iterator to the first element
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
