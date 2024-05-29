# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Request\Payouts\PayoutDestinationData\PayoutDestinationDataFactory
### Namespace: [\YooKassa\Request\Payouts\PayoutDestinationData](../namespaces/yookassa-request-payouts-payoutdestinationdata.md)
---
**Summary:**

Класс, представляющий модель PayoutDestinationDataFactory.

**Description:**

Фабрика создания объекта платежных методов из массива

---
### Constants
* No constants found

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [factory()](../classes/YooKassa-Request-Payouts-PayoutDestinationData-PayoutDestinationDataFactory.md#method_factory) |  | Фабричный метод создания объекта платежных данных по типу. |
| public | [factoryFromArray()](../classes/YooKassa-Request-Payouts-PayoutDestinationData-PayoutDestinationDataFactory.md#method_factoryFromArray) |  | Фабричный метод создания объекта платежных данных из массива. |

---
### Details
* File: [lib/Request/Payouts/PayoutDestinationData/PayoutDestinationDataFactory.php](../../lib/Request/Payouts/PayoutDestinationData/PayoutDestinationDataFactory.php)
* Package: YooKassa\Request
* Class Hierarchy:
  * \YooKassa\Request\Payouts\PayoutDestinationData\PayoutDestinationDataFactory

* See Also:
  * [](https://yookassa.ru/developers/api)

---
### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| category |  | Class |
| author |  | cms@yoomoney.ru |

---
## Methods
<a name="method_factory" class="anchor"></a>
#### public factory() : \YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData

```php
public factory(string $type) : \YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData
```

**Summary**

Фабричный метод создания объекта платежных данных по типу.

**Details:**
* Inherited From: [\YooKassa\Request\Payouts\PayoutDestinationData\PayoutDestinationDataFactory](../classes/YooKassa-Request-Payouts-PayoutDestinationData-PayoutDestinationDataFactory.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string</code> | type  | Тип платежных данных |

**Returns:** \YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData - 


<a name="method_factoryFromArray" class="anchor"></a>
#### public factoryFromArray() : \YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData

```php
public factoryFromArray(array $data, null|string $type = null) : \YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData
```

**Summary**

Фабричный метод создания объекта платежных данных из массива.

**Details:**
* Inherited From: [\YooKassa\Request\Payouts\PayoutDestinationData\PayoutDestinationDataFactory](../classes/YooKassa-Request-Payouts-PayoutDestinationData-PayoutDestinationDataFactory.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">array</code> | data  | Массив платежных данных |
| <code lang="php">null OR string</code> | type  | Тип платежных данных |

**Returns:** \YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData - 



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