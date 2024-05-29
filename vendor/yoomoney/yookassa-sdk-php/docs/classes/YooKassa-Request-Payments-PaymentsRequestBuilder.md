# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Request\Payments\PaymentsRequestBuilder
### Namespace: [\YooKassa\Request\Payments](../namespaces/yookassa-request-payments.md)
---
**Summary:**

Класс, представляющий модель PaymentsRequestBuilder.

**Description:**

Класс билдера объекта запроса для получения списка платежей магазина, передаваемого в методы клиента API.

---
### Constants
* No constants found

---
### Properties
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| protected | [$currentObject](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#property_currentObject) |  | Инстанс собираемого запроса. |

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [__construct()](../classes/YooKassa-Common-AbstractRequestBuilder.md#method___construct) |  | Конструктор, инициализирует пустой запрос, который в будущем начнём собирать. |
| public | [build()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_build) |  | Собирает и возвращает объект запроса списка платежей магазина. |
| public | [setCapturedAtGt()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCapturedAtGt) |  | Устанавливает дату подтверждения от которой выбираются платежи. |
| public | [setCapturedAtGte()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCapturedAtGte) |  | Устанавливает дату подтверждения от которой выбираются платежи. |
| public | [setCapturedAtLt()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCapturedAtLt) |  | Устанавливает дату подтверждения до которой выбираются платежи. |
| public | [setCapturedAtLte()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCapturedAtLte) |  | Устанавливает дату подтверждения до которой выбираются платежи. |
| public | [setCreatedAtGt()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCreatedAtGt) |  | Устанавливает дату создания от которой выбираются платежи. |
| public | [setCreatedAtGte()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCreatedAtGte) |  | Устанавливает дату создания от которой выбираются платежи. |
| public | [setCreatedAtLt()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCreatedAtLt) |  | Устанавливает дату создания до которой выбираются платежи. |
| public | [setCreatedAtLte()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCreatedAtLte) |  | Устанавливает дату создания до которой выбираются платежи. |
| public | [setCursor()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setCursor) |  | Устанавливает страница выдачи результатов. |
| public | [setLimit()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setLimit) |  | Устанавливает ограничение количества объектов платежа. |
| public | [setOptions()](../classes/YooKassa-Common-AbstractRequestBuilder.md#method_setOptions) |  | Устанавливает свойства запроса из массива. |
| public | [setPaymentMethod()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setPaymentMethod) |  | Устанавливает платежный метод выбираемых платежей. |
| public | [setStatus()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_setStatus) |  | Устанавливает статус выбираемых платежей. |
| protected | [initCurrentObject()](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md#method_initCurrentObject) |  | Возвращает новый объект запроса для получения списка платежей, который в дальнейшем будет собираться в билдере. |

---
### Details
* File: [lib/Request/Payments/PaymentsRequestBuilder.php](../../lib/Request/Payments/PaymentsRequestBuilder.php)
* Package: YooKassa\Request
* Class Hierarchy: 
  * [\YooKassa\Common\AbstractRequestBuilder](../classes/YooKassa-Common-AbstractRequestBuilder.md)
  * \YooKassa\Request\Payments\PaymentsRequestBuilder

* See Also:
  * [](https://yookassa.ru/developers/api)

---
### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| category |  | Class |
| author |  | cms@yoomoney.ru |

---
## Properties
<a name="property_currentObject"></a>
#### protected $currentObject : ?\YooKassa\Common\AbstractRequestInterface
---
**Summary**

Инстанс собираемого запроса.

**Type:** <a href="../?\YooKassa\Common\AbstractRequestInterface"><abbr title="?\YooKassa\Common\AbstractRequestInterface">AbstractRequestInterface</abbr></a>
Собираемый объект запроса списка платежей магазина
**Details:**



---
## Methods
<a name="method___construct" class="anchor"></a>
#### public __construct() : mixed

```php
public __construct() : mixed
```

**Summary**

Конструктор, инициализирует пустой запрос, который в будущем начнём собирать.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractRequestBuilder](../classes/YooKassa-Common-AbstractRequestBuilder.md)

**Returns:** mixed - 


<a name="method_build" class="anchor"></a>
#### public build() : \YooKassa\Request\Payments\PaymentsRequest

```php
public build(null|array $options = null) : \YooKassa\Request\Payments\PaymentsRequest
```

**Summary**

Собирает и возвращает объект запроса списка платежей магазина.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR array</code> | options  | Массив с настройками запроса |

**Returns:** \YooKassa\Request\Payments\PaymentsRequest - Инстанс объекта запроса к API для получения списка платежей магазина


<a name="method_setCapturedAtGt" class="anchor"></a>
#### public setCapturedAtGt() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCapturedAtGt(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату подтверждения от которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время создания, до (включительно) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCapturedAtGte" class="anchor"></a>
#### public setCapturedAtGte() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCapturedAtGte(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату подтверждения от которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время подтверждения, от (включительно) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCapturedAtLt" class="anchor"></a>
#### public setCapturedAtLt() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCapturedAtLt(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату подтверждения до которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время подтверждения, до (включительно) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCapturedAtLte" class="anchor"></a>
#### public setCapturedAtLte() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCapturedAtLte(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату подтверждения до которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время подтверждения, до (включительно) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCreatedAtGt" class="anchor"></a>
#### public setCreatedAtGt() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCreatedAtGt(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату создания от которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время создания, от (не включая) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCreatedAtGte" class="anchor"></a>
#### public setCreatedAtGte() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCreatedAtGte(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату создания от которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время создания, от (включительно) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCreatedAtLt" class="anchor"></a>
#### public setCreatedAtLt() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCreatedAtLt(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату создания до которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время создания, до (не включая) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCreatedAtLte" class="anchor"></a>
#### public setCreatedAtLte() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCreatedAtLte(null|\DateTime|int|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает дату создания до которой выбираются платежи.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR \DateTime OR int OR string</code> | value  | Время создания, до (включительно) или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Генерируется если была передана дата в невалидном формате (была передана строка или число, которые не удалось преобразовать в валидную дату) |
| \Exception|\YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Генерируется если была передана дата с не тем типом (передана не строка, не число и не значение типа DateTime) |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setCursor" class="anchor"></a>
#### public setCursor() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setCursor(null|string $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает страница выдачи результатов.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">null OR string</code> | value  | Страница выдачи результатов или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Выбрасывается если в метод была передана не строка |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setLimit" class="anchor"></a>
#### public setLimit() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setLimit(string|int|null $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает ограничение количества объектов платежа.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR int OR null</code> | value  | Ограничение количества объектов платежа или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Выбрасывается если в метод было передана не целое число |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setOptions" class="anchor"></a>
#### public setOptions() : \YooKassa\Common\AbstractRequestBuilder

```php
public setOptions(iterable|null $options) : \YooKassa\Common\AbstractRequestBuilder
```

**Summary**

Устанавливает свойства запроса из массива.

**Details:**
* Inherited From: [\YooKassa\Common\AbstractRequestBuilder](../classes/YooKassa-Common-AbstractRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">iterable OR null</code> | options  | Массив свойств запроса |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \InvalidArgumentException | Выбрасывается если аргумент не массив и не итерируемый объект |
| \YooKassa\Common\Exceptions\InvalidPropertyException | Выбрасывается если не удалось установить один из параметров, переданных в массиве настроек |

**Returns:** \YooKassa\Common\AbstractRequestBuilder - Инстанс текущего билдера запросов


<a name="method_setPaymentMethod" class="anchor"></a>
#### public setPaymentMethod() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setPaymentMethod(string|null $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает платежный метод выбираемых платежей.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | value  | Платежный метод выбираемых платежей или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Выбрасывается если переданное значение не является валидным статусом |
| \YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Выбрасывается если в метод была передана не строка |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_setStatus" class="anchor"></a>
#### public setStatus() : \YooKassa\Request\Payments\PaymentsRequestBuilder

```php
public setStatus(string|null $value) : \YooKassa\Request\Payments\PaymentsRequestBuilder
```

**Summary**

Устанавливает статус выбираемых платежей.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | value  | Статус выбираемых платежей или null, чтобы удалить значение |

##### Throws:
| Type | Description |
| ---- | ----------- |
| \YooKassa\Common\Exceptions\InvalidPropertyValueException | Выбрасывается если переданное значение не является валидным статусом |
| \YooKassa\Common\Exceptions\InvalidPropertyValueTypeException | Выбрасывается если в метод была передана не строка |

**Returns:** \YooKassa\Request\Payments\PaymentsRequestBuilder - Инстанс текущего билдера


<a name="method_initCurrentObject" class="anchor"></a>
#### protected initCurrentObject() : \YooKassa\Request\Payments\PaymentsRequest

```php
protected initCurrentObject() : \YooKassa\Request\Payments\PaymentsRequest
```

**Summary**

Возвращает новый объект запроса для получения списка платежей, который в дальнейшем будет собираться в билдере.

**Details:**
* Inherited From: [\YooKassa\Request\Payments\PaymentsRequestBuilder](../classes/YooKassa-Request-Payments-PaymentsRequestBuilder.md)

**Returns:** \YooKassa\Request\Payments\PaymentsRequest - Объект запроса списка платежей магазина



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