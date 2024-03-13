[comment]: # (This file is part of Asit, manages array collections. Copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## List Summary

* implements ListTypeInterface

> Class **ItList** extends [It]

> Class **AsitList** extends [Asit]

> Class **AsittagList** extends [Asittag] implements TagInterface, PkeyTagInterface

> Class **AsmitList** extends [Asmit]

> Class **AsmittagList** extends [Asmittag] implements TagInterface, PkeyTagInterface

* assert collection elements of expected valueType 
  * one of ListTypeInterface constants or FQCN (for class or interface)

ItList example:

```
use Kigkonsult\Asit\ItList;
class MyClass extend ItList {}
$myClass = MyClass::factory( ElementValueClass::class );
...
$myClass->append( $element );
...
```


#### Shared methods

```*List::__construct( [ collection, [ valueType ]] )```
* ```collection``` _array_ / _Traversable_
* ```valueType``` _string_ required
* Throws CollectionException, TypeException

```*List::__construct( valueType )```
* ```valueType``` _string_ required
* Throws CollectionException, TypeException

```*List::factory( [ collection, [ valueType ]] )```
* ```collection``` _array_ / _Traversable_
* ```valueType``` _string_  required
* Return _static_
* Throws CollectionException, TypeException
* Static

```*List::factory( valueType )```
* ```valueType``` _string_  required
* Return _static_
* Throws CollectionException, TypeException
* Static

```*List::singleton( [ collection, [ valueType ]] )```
* ```collection``` _array_ / _Traversable_
* ```valueType``` _string_  required
* Return _static_
* Throws CollectionException, TypeException
* Static

```*List::getInstance( [ collection, [ valueType ]] )```
* List::singleton() alias

```*List::singleton( valueType )```
* ```valueType``` _string_
* Return _static_
* Throws CollectionException, TypeException
* Static

```*List::getInstance( valueType )```
* List::singleton() alias

#### Inherited methods

Inherit methods from [It] - [Asit] - [Asmit] - [Asittag] - [Asmittag]

A strong recommendation is to override the [It]::current(), [It]::get() and [It]::getIterator() methods
to specify the return types.

```
    /**
     * @override
     * @return MyClass
     */
    #[\ReturnTypeWillChange]
    public function current() : MyClass
    {
        return parent::current();
    }

    /**
     * @override
     * @param callable|int|null $sortParam
     * @return MyClass[]
     */
    #[\ReturnTypeWillChange]
    public function get( null|callable|int $sortParam = null ) : array
    {
        return parent::get( $sortParam );
    }

    /**
     * @override
     * @return MyClass[]|Traversable
     */
    #[\ReturnTypeWillChange]
    public function getIterator() : array|Traversable
    {
        return parent::getIterator();
    }

```

#### Element value type methods

```*List::assertElementType( element )```
* Assert collection element value type
* ```element``` _mixed_
* Throws TypeException

---

```*List::assertValueType( valueType )```
* Assert value type
  * one of ListTypeInterface constants or FQCN (for class or interface)
* ```valueType``` _string_
* Throws TypeException
* Static

---

```*List::getValueType()```
* Return _string_

---

```*List::isValueTypeSet()```
* Return _bool_

---

```*List::setValueType( valueType )```
* ```valueType``` _string_
  * one of ListTypeInterface constants or FQCN (for class or interface)
* Return _static_
* Throws TypeException

---
Go to [README] - [It] summary - [Asit]/[Asmit] summary - [Asittag]/[Asmittag] summary 

[Asit]:AsitSummary.md
[Asmit]:AsitSummary.md
[Asittag]:AsittagSummary.md
[Asmittag]:AsittagSummary.md
[It]:ItSummary.md
[README]:../README.md
