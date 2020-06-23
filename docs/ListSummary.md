[comment]: # (This file is part of Asit, manages array collections. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence LGPL 3.0)
## List Summary

> Class **ItList** extends [It]

> Class **AsitList** extends [Asit]

> Class **AsittagList** extends [Asittag]

> Class **AsmitList** extends [Asit]

> Class **AsmittagList** extends [Asmittag]

* assure collection elements of expected valueType 
  * one of TypeInterface constants or FQCN (for class or interface)


#### Shared methods

```*List::__construct( [ collection, [ valueType ]] )```
* ```collection``` _array_ / _Traversable_
* ```valueType``` _string_

```*List::__construct( valueType )```
* ```valueType``` _string_

```*List::factory( [ collection, [ valueType ]] )```
* ```collection``` _array_ / _Traversable_
* ```valueType``` _string_
* Return _static_
* Static

```*List::factory( valueType )```
* ```valueType``` _string_
* Return _static_
* Static

----

```*List::assertElementType( element )```
* Assert collection element value type
* ```element``` _mixed_
* Throws InvalidArgumentException

---

```*List::assertValueType( valueType )```
* Assert value type
  * one of TypeInterface constants or FQCN (for class or interface)
* ```valueType``` _string_
* Throws InvalidArgumentException
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
  * one of TypeInterface constants or FQCN (for class or interface)
* Return _static_
* Throws InvalidArgumentException

---
Go to [README] - [It] summary - [Asit]/[Asmit] summary - [Asittag]/[Asmittag] summary 

[Asit]:AsitSummary.md
[Asmit]:AsitSummary.md
[Asittag]:AsittagSummary.md
[Asmittag]:AsittagSummary.md
[It]:ItSummary.md
[README]:../README.md
