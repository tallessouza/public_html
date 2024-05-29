# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Model\Payment\PaymentMethod\PaymentMethodUnknown
### Namespace: [\YooKassa\Model\Payment\PaymentMethod](../namespaces/yookassa-model-payment-paymentmethod.md)
---
**Summary:**

Класс, представляющий модель PaymentMethodUnknown.

**Description:**

Неизвестный платежный метод.

---
### Constants
* No constants found

---
### Properties
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [$id](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property_id) |  | Идентификатор записи о сохраненных платежных данных |
| public | [$saved](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property_saved) |  | Возможность многократного использования |
| public | [$title](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property_title) |  | Название метода оплаты |
| public | [$type](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property_type) |  | Код способа оплаты |
| protected | [$_id](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property__id) |  | Идентификатор записи о сохраненных платежных данных. |
| protected | [$_saved](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property__saved) |  | С помощью сохраненного способа оплаты можно проводить [безакцептные списания](/developers/payment-acceptance/scenario-extensions/recurring-payments). |
| protected | [$_title](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property__title) |  | Название способа оплаты. |
| protected | [$_type](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#property__type) |  | Код способа оплаты. |

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [__construct()](../classes/YooKassa-Model-Payment-PaymentMethod-PaymentMethodUnknown.md#method___construct) |  |  |
| public | [__get()](../classes/YooKassa-Common-AbstractObject.md#method___get) |  | Возвращает значение свойства. |
| public | [__isset()](../classes/YooKassa-Common-AbstractObject.md#method___isset) |  | Проверяет наличие свойства. |
| public | [__set()](../classes/YooKassa-Common-AbstractObject.md#method___set) |  | Устанавливает значение свойства. |
| public | [__unset()](../classes/YooKassa-Common-AbstractObject.md#method___unset) |  | Удаляет свойство. |
| public | [fromArray()](../classes/YooKassa-Common-AbstractObject.md#method_fromArray) |  | Устанавливает значения свойств текущего объекта из массива. |
| public | [getId()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_getId) |  | Возвращает id. |
| public | [getSaved()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_getSaved) |  | Возвращает saved. |
| public | [getTitle()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_getTitle) |  | Возвращает Название способа оплаты. |
| public | [getType()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_getType) |  | Возвращает тип платежного метода. |
| public | [getValidator()](../classes/YooKassa-Common-AbstractObject.md#method_getValidator) |  |  |
| public | [jsonSerialize()](../classes/YooKassa-Common-AbstractObject.md#method_jsonSerialize) |  | Возвращает ассоциативный массив со свойствами текущего объекта для его дальнейшей JSON сериализации. |
| public | [offsetExists()](../classes/YooKassa-Common-AbstractObject.md#method_offsetExists) |  | Проверяет наличие свойства. |
| public | [offsetGet()](../classes/YooKassa-Common-AbstractObject.md#method_offsetGet) |  | Возвращает значение свойства. |
| public | [offsetSet()](../classes/YooKassa-Common-AbstractObject.md#method_offsetSet) |  | Устанавливает значение свойства. |
| public | [offsetUnset()](../classes/YooKassa-Common-AbstractObject.md#method_offsetUnset) |  | Удаляет свойство. |
| public | [setId()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_setId) |  | Устанавливает id. |
| public | [setSaved()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_setSaved) |  | Устанавливает признак возможности многократного использования. |
| public | [setTitle()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_setTitle) |  | Устанавливает Название способа оплаты. |
| public | [setType()](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md#method_setType) |  | Устанавливает тип платежного метода. |
| public | [toArray()](../classes/YooKassa-Common-AbstractObject.md#method_toArray) |  | Возвращает ассоциативный массив со свойствами текущего объекта для его дальнейшей JSON сериализации Является алиасом метода AbstractObject::jsonSerialize(). |
| protected | [getUnknownProperties()](../classes/YooKassa-Common-AbstractObject.md#method_getUnknownProperties) |  | Возвращает массив свойств которые не существуют, но были заданы у объекта. |
| protected | [validatePropertyValue()](../classes/YooKassa-Common-AbstractObject.md#method_validatePropertyValue) |  |  |

---
### Details
* File: [lib/Model/Payment/PaymentMethod/PaymentMethodUnknown.php](../../lib/Model/Payment/PaymentMethod/PaymentMethodUnknown.php)
* Package: YooKassa\Model
* Class Hierarchy:  
  * [\YooKassa\Common\AbstractObject](../classes/YooKassa-Common-AbstractObject.md)
  * [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)
  * \YooKassa\Model\Payment\PaymentMethod\PaymentMethodUnknown

* See Also:
  * [](https://yookassa.ru/developers/api)

---
### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| category |  | Class |
| author |  | cms@yoomoney.ru |
| deprecated |  | Не используется в реальных платежах |

---
## Properties
<a name="property_id"></a>
#### public $id : string
---
***Description***

Идентификатор записи о сохраненных платежных данных

**Type:** <a href="../string"><abbr title="string">string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property_saved"></a>
#### public $saved : bool
---
***Description***

Возможность многократного использования

**Type:** <a href="../bool"><abbr title="bool">bool</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property_title"></a>
#### public $title : string
---
***Description***

Название метода оплаты

**Type:** <a href="../string"><abbr title="string">string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property_type"></a>
#### public $type : string
---
***Description***

Код способа оплаты

**Type:** <a href="../string"><abbr title="string">string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property__id"></a>
#### protected $_id : ?string
---
**Summary**

Идентификатор записи о сохраненных платежных данных.

**Type:** <a href="../?string"><abbr title="?string">?string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property__saved"></a>
#### protected $_saved : bool
---
**Summary**

С помощью сохраненного способа оплаты можно проводить [безакцептные списания](/developers/payment-acceptance/scenario-extensions/recurring-payments).

**Type:** <a href="../bool"><abbr title="bool">bool</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property__title"></a>
#### protected $_title : ?string
---
**Summary**

Название способа оплаты.

**Type:** <a href="../?string"><abbr title="?string">?string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)


<a name="property__type"></a>
#### protected $_type : ?string
---
**Summary**

Код способа оплаты.

**Type:** <a href="../?string"><abbr title="?string">?string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)



---
## Methods
<a name="method___construct" class="anchor"></a>
#### public __construct() : mixed

```php
public __construct(?array $data = []) : mixed
```

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\PaymentMethodUnknown](../classes/YooKassa-Model-Payment-PaymentMethod-PaymentMethodUnknown.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">?array</code> | data  |  |

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

Возвращает id.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

**Returns:** string|null - 


<a name="method_getSaved" class="anchor"></a>
#### public getSaved() : bool|null

```php
public getSaved() : bool|null
```

**Summary**

Возвращает saved.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

**Returns:** bool|null - 


<a name="method_getTitle" class="anchor"></a>
#### public getTitle() : string|null

```php
public getTitle() : string|null
```

**Summary**

Возвращает Название способа оплаты.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

**Returns:** string|null - Название способа оплаты


<a name="method_getType" class="anchor"></a>
#### public getType() : string|null

```php
public getType() : string|null
```

**Summary**

Возвращает тип платежного метода.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

**Returns:** string|null - Тип платежного метода


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

Устанавливает id.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | id  | Идентификатор способа оплаты. |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \Exception |  |

**Returns:** self - 


<a name="method_setSaved" class="anchor"></a>
#### public setSaved() : self

```php
public setSaved(bool|array|null $saved = null) : self
```

**Summary**

Устанавливает признак возможности многократного использования.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">bool OR array OR null</code> | saved  | С помощью сохраненного способа оплаты можно проводить [безакцептные списания](/developers/payment-acceptance/scenario-extensions/recurring-payments). |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \Exception |  |

**Returns:** self - 


<a name="method_setTitle" class="anchor"></a>
#### public setTitle() : self

```php
public setTitle(string|null $title = null) : self
```

**Summary**

Устанавливает Название способа оплаты.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | title  | Название способа оплаты. |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \Exception |  |

**Returns:** self - 


<a name="method_setType" class="anchor"></a>
#### public setType() : self

```php
public setType(string|null $type = null) : self
```

**Summary**

Устанавливает тип платежного метода.

**Details:**
* Inherited From: [\YooKassa\Model\Payment\PaymentMethod\AbstractPaymentMethod](../classes/YooKassa-Model-Payment-PaymentMethod-AbstractPaymentMethod.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | type  | Тип платежного метода |

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