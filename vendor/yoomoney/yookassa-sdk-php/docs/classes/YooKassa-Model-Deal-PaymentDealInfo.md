# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Model\Deal\PaymentDealInfo
### Namespace: [\YooKassa\Model\Deal](../namespaces/yookassa-model-deal.md)
---
**Summary:**

Класс, представляющий модель PaymentDealInfo.


---
### Constants
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [MAX_LENGTH_ID](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#constant_MAX_LENGTH_ID) |  |  |
| public | [MIN_LENGTH_ID](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#constant_MIN_LENGTH_ID) |  |  |

---
### Properties
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [$id](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#property_id) |  | Идентификатор сделки |
| public | [$settlements](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#property_settlements) |  | Данные о распределении денег |

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [__construct()](../classes/YooKassa-Common-AbstractObject.md#method___construct) |  | AbstractObject constructor. |
| public | [__get()](../classes/YooKassa-Common-AbstractObject.md#method___get) |  | Возвращает значение свойства. |
| public | [__isset()](../classes/YooKassa-Common-AbstractObject.md#method___isset) |  | Проверяет наличие свойства. |
| public | [__set()](../classes/YooKassa-Common-AbstractObject.md#method___set) |  | Устанавливает значение свойства. |
| public | [__unset()](../classes/YooKassa-Common-AbstractObject.md#method___unset) |  | Удаляет свойство. |
| public | [fromArray()](../classes/YooKassa-Common-AbstractObject.md#method_fromArray) |  | Устанавливает значения свойств текущего объекта из массива. |
| public | [getId()](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#method_getId) |  | Возвращает Id сделки. |
| public | [getSettlements()](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#method_getSettlements) |  | Возвращает массив оплат, обеспечивающих выдачу товара. |
| public | [getValidator()](../classes/YooKassa-Common-AbstractObject.md#method_getValidator) |  |  |
| public | [jsonSerialize()](../classes/YooKassa-Common-AbstractObject.md#method_jsonSerialize) |  | Возвращает ассоциативный массив со свойствами текущего объекта для его дальнейшей JSON сериализации. |
| public | [offsetExists()](../classes/YooKassa-Common-AbstractObject.md#method_offsetExists) |  | Проверяет наличие свойства. |
| public | [offsetGet()](../classes/YooKassa-Common-AbstractObject.md#method_offsetGet) |  | Возвращает значение свойства. |
| public | [offsetSet()](../classes/YooKassa-Common-AbstractObject.md#method_offsetSet) |  | Устанавливает значение свойства. |
| public | [offsetUnset()](../classes/YooKassa-Common-AbstractObject.md#method_offsetUnset) |  | Удаляет свойство. |
| public | [setId()](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#method_setId) |  | Устанавливает Id сделки. |
| public | [setSettlements()](../classes/YooKassa-Model-Deal-PaymentDealInfo.md#method_setSettlements) |  | Устанавливает массив оплат, обеспечивающих выдачу товара. |
| public | [toArray()](../classes/YooKassa-Common-AbstractObject.md#method_toArray) |  | Возвращает ассоциативный массив со свойствами текущего объекта для его дальнейшей JSON сериализации Является алиасом метода AbstractObject::jsonSerialize(). |
| protected | [getUnknownProperties()](../classes/YooKassa-Common-AbstractObject.md#method_getUnknownProperties) |  | Возвращает массив свойств которые не существуют, но были заданы у объекта. |
| protected | [validatePropertyValue()](../classes/YooKassa-Common-AbstractObject.md#method_validatePropertyValue) |  |  |

---
### Details
* File: [lib/Model/Deal/PaymentDealInfo.php](../../lib/Model/Deal/PaymentDealInfo.php)
* Package: YooKassa\Model
* Class Hierarchy: 
  * [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)
  * \YooKassa\Model\Deal\PaymentDealInfo

* See Also:
  * [](https://yookassa.ru/developers/api)

---
### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| category |  | Class |
| author |  | cms@yoomoney.ru |

---
## Constants
<a name="constant_MAX_LENGTH_ID" class="anchor"></a>
###### MAX_LENGTH_ID
```php
MAX_LENGTH_ID = 50 : int
```


<a name="constant_MIN_LENGTH_ID" class="anchor"></a>
###### MIN_LENGTH_ID
```php
MIN_LENGTH_ID = 36 : int
```



---
## Properties
<a name="property_id"></a>
#### public $id : string
---
***Description***

Идентификатор сделки

**Type:** <a href="../string"><abbr title="string">string</abbr></a>

**Details:**


<a name="property_settlements"></a>
#### public $settlements : \YooKassa\Common\ListObjectInterface|\YooKassa\Model\Receipt\SettlementInterface[]
---
***Description***

Данные о распределении денег

**Type:** <a href="../\YooKassa\Common\ListObjectInterface|\YooKassa\Model\Receipt\SettlementInterface[]"><abbr title="\YooKassa\Common\ListObjectInterface|\YooKassa\Model\Receipt\SettlementInterface[]">SettlementInterface[]</abbr></a>

**Details:**



---
## Methods
<a name="method___construct" class="anchor"></a>
#### public __construct() : mixed

```php
public __construct(array|null $data = []) : mixed
```

**Summary**

AbstractObject constructor.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">array OR null</code> | data  |  |

**Returns:** mixed - 


<a name="method___get" class="anchor"></a>
#### public __get() : mixed

```php
public __get(string $propertyName) : mixed
```

**Summary**

Возвращает значение свойства.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | propertyName  | Имя свойства |

**Returns:** mixed - Значение свойства


<a name="method___isset" class="anchor"></a>
#### public __isset() : bool

```php
public __isset(string $propertyName) : bool
```

**Summary**

Проверяет наличие свойства.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | propertyName  | Имя проверяемого свойства |

**Returns:** bool - True если свойство имеется, false если нет


<a name="method___set" class="anchor"></a>
#### public __set() : void

```php
public __set(string $propertyName, mixed $value) : void
```

**Summary**

Устанавливает значение свойства.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | propertyName  | Имя свойства |
| <code lang="php">mixed</code> | value  | Значение свойства |

**Returns:** void - 


<a name="method___unset" class="anchor"></a>
#### public __unset() : void

```php
public __unset(string $propertyName) : void
```

**Summary**

Удаляет свойство.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | propertyName  | Имя удаляемого свойства |

**Returns:** void - 


<a name="method_fromArray" class="anchor"></a>
#### public fromArray() : void

```php
public fromArray(array|\Traversable $sourceArray) : void
```

**Summary**

Устанавливает значения свойств текущего объекта из массива.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">array OR \Traversable</code> | sourceArray  | Ассоциативный массив с настройками |

**Returns:** void - 


<a name="method_getId" class="anchor"></a>
#### public getId() : string|null

```php
public getId() : string|null
```

**Summary**

Возвращает Id сделки.

**Details:**
* Inherited From: [\YooKassa\Model\Deal\PaymentDealInfo](../classes/YooKassa-Model-Deal-PaymentDealInfo.md)

**Returns:** string|null - 


<a name="method_getSettlements" class="anchor"></a>
#### public getSettlements() : \YooKassa\Model\Receipt\SettlementInterface[]|\YooKassa\Common\ListObjectInterface

```php
public getSettlements() : \YooKassa\Model\Receipt\SettlementInterface[]|\YooKassa\Common\ListObjectInterface
```

**Summary**

Возвращает массив оплат, обеспечивающих выдачу товара.

**Details:**
* Inherited From: [\YooKassa\Model\Deal\PaymentDealInfo](../classes/YooKassa-Model-Deal-PaymentDealInfo.md)

**Returns:** \YooKassa\Model\Receipt\SettlementInterface[]|\YooKassa\Common\ListObjectInterface - Массив оплат, обеспечивающих выдачу товара


<a name="method_getValidator" class="anchor"></a>
#### public getValidator() : \YooKassa\Validator\Validator

```php
public getValidator() : \YooKassa\Validator\Validator
```

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

**Returns:** \YooKassa\Validator\Validator - 


<a name="method_jsonSerialize" class="anchor"></a>
#### public jsonSerialize() : array

```php
public jsonSerialize() : array
```

**Summary**

Возвращает ассоциативный массив со свойствами текущего объекта для его дальнейшей JSON сериализации.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

**Returns:** array - Ассоциативный массив со свойствами текущего объекта


<a name="method_offsetExists" class="anchor"></a>
#### public offsetExists() : bool

```php
public offsetExists(string $offset) : bool
```

**Summary**

Проверяет наличие свойства.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | offset  | Имя проверяемого свойства |

**Returns:** bool - True если свойство имеется, false если нет


<a name="method_offsetGet" class="anchor"></a>
#### public offsetGet() : mixed

```php
public offsetGet(string $offset) : mixed
```

**Summary**

Возвращает значение свойства.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | offset  | Имя свойства |

**Returns:** mixed - Значение свойства


<a name="method_offsetSet" class="anchor"></a>
#### public offsetSet() : void

```php
public offsetSet(string $offset, mixed $value) : void
```

**Summary**

Устанавливает значение свойства.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | offset  | Имя свойства |
| <code lang="php">mixed</code> | value  | Значение свойства |

**Returns:** void - 


<a name="method_offsetUnset" class="anchor"></a>
#### public offsetUnset() : void

```php
public offsetUnset(string $offset) : void
```

**Summary**

Удаляет свойство.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | offset  | Имя удаляемого свойства |

**Returns:** void - 


<a name="method_setId" class="anchor"></a>
#### public setId() : self

```php
public setId(string|null $id = null) : self
```

**Summary**

Устанавливает Id сделки.

**Details:**
* Inherited From: [\YooKassa\Model\Deal\PaymentDealInfo](../classes/YooKassa-Model-Deal-PaymentDealInfo.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | id  | Идентификатор сделки. |

**Returns:** self - 


<a name="method_setSettlements" class="anchor"></a>
#### public setSettlements() : self

```php
public setSettlements(\YooKassa\Common\ListObjectInterface|array|null $settlements = null) : self
```

**Summary**

Устанавливает массив оплат, обеспечивающих выдачу товара.

**Details:**
* Inherited From: [\YooKassa\Model\Deal\PaymentDealInfo](../classes/YooKassa-Model-Deal-PaymentDealInfo.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">\YooKassa\Common\ListObjectInterface OR array OR null</code> | settlements  | Данные о распределении денег. |

**Returns:** self - 


<a name="method_toArray" class="anchor"></a>
#### public toArray() : array

```php
public toArray() : array
```

**Summary**

Возвращает ассоциативный массив со свойствами текущего объекта для его дальнейшей JSON сериализации
Является алиасом метода AbstractObject::jsonSerialize().

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

**Returns:** array - Ассоциативный массив со свойствами текущего объекта


<a name="method_getUnknownProperties" class="anchor"></a>
#### protected getUnknownProperties() : array

```php
protected getUnknownProperties() : array
```

**Summary**

Возвращает массив свойств которые не существуют, но были заданы у объекта.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

**Returns:** array - Ассоциативный массив с не существующими у текущего объекта свойствами


<a name="method_validatePropertyValue" class="anchor"></a>
#### protected validatePropertyValue() : mixed

```php
protected validatePropertyValue(string $propertyName, mixed $propertyValue) : mixed
```

**Details:**
* Inherited From: [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | propertyName  |  |
| <code lang="php">mixed</code> | propertyValue  |  |

**Returns:** mixed - 



---

### Top Namespaces

* [\YooKassa](../namespaces/yookassa.md)

---

### Reports
* [Errors - 0](../reports/errors.md)
* [Markers - 0](../reports/markers.md)
* [Deprecated - 22](../reports/deprecated.md)

---

This document was automatically generated from source code comments on 2024-04-01 using [phpDocumentor](http://www.phpdoc.org/)

&copy; 2024 YooMoney