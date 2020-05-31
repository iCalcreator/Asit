## Asit Summary

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

--- 
```Asit::__construct( [ collection ] )```
* Class Asit construct method
* ```collection``` _array_

```Asit::factory( [ collection ] )```
* Class Asit factory method
* ```collection``` _array_
* Return _static_
* static
    
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
    
```Asit::setCollection( collection )```
* Set (array) collection
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

```Asit::append( element [, pKey ] )```
* Note, last appended element is always ```current```

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key

#### SeekableIterator, Countable, IteratorAggregate et al. methods

```Asit::count()```
* Return count of collection elements
* Required method implementing the Countable interface
* Return int

```Asit::current()```
* Return the current element
* Required method implementing the Iterator interface
* Return mixed

```Asit::exists( position )```
* Checks if position is set
* ```position``` _int_
* Return bool

```Asit::GetIterator()```
* Return an external iterator
* Required method implementing the IteratorAggregate interface, i.e. makes the class traversable using foreach.
* Usage : ```foreach( $class as $value ) { .... }```
* Return Traversable

```Asit::GetPkeyIterator()```
* Return an external iterator ( pKey => element )
* Return Traversable

```Asit::key()```
* Return the (numeric) key of the current element
* Required method implementing the Iterator interface
* Return int

```Asit::last()```
* Move position to last element
* Return _static_

```Asit::next()```
* Move position forward to next element
* Required method implementing the Iterator interface
* Return _static_

```Asit::previous()```
* Move position backward to previous element
* Return _static_

```Asit::rewind()```
* Rewind the Iterator to the first element
* Required method implementing the Iterator interface
* Return _static_

```Asit::seek( position )```
* Seeks to a given position in the iterator
* Required method implementing the SeekableIterator interface
* ```position``` _int_
* Return void
* Throws OutOfBoundsException

```Asit::pKeySeek( pKey )```
* Seeks to a given position in the iterator using primary key
* ```pKey``` _int_|_string_
* Return _static_
* Throws InvalidArgumentException

```Asit::valid()```
* Checks if current position is valid
* Required method implementing the Iterator interface
* Return bool

Go to [README] - [Asittag] summary 

[Asittag]:AsittagSummary.md
[README]:../README.md
