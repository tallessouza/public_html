# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Common\Exceptions\InternalServerError
### Namespace: [\YooKassa\Common\Exceptions](../namespaces/yookassa-common-exceptions.md)
---
**Summary:**

Технические неполадки на стороне ЮKassa. Результат обработки запроса неизвестен. Повторите запрос позднее с тем же ключом идемпотентности.

**Description:**

Рекомендуется повторять запрос с периодичностью один раз в минуту до тех пор, пока ЮKassa не сообщит результат обработки операции.

---
### Constants
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [HTTP_CODE](../classes/YooKassa-Common-Exceptions-InternalServerError.md#constant_HTTP_CODE) |  |  |

---
### Properties
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [$retryAfter](../classes/YooKassa-Common-Exceptions-InternalServerError.md#property_retryAfter) |  |  |
| public | [$type](../classes/YooKassa-Common-Exceptions-InternalServerError.md#property_type) |  |  |
| protected | [$responseBody](../classes/YooKassa-Common-Exceptions-ApiException.md#property_responseBody) |  |  |
| protected | [$responseHeaders](../classes/YooKassa-Common-Exceptions-ApiException.md#property_responseHeaders) |  |  |

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [__construct()](../classes/YooKassa-Common-Exceptions-InternalServerError.md#method___construct) |  | Constructor. |
| public | [getResponseBody()](../classes/YooKassa-Common-Exceptions-ApiException.md#method_getResponseBody) |  |  |
| public | [getResponseHeaders()](../classes/YooKassa-Common-Exceptions-ApiException.md#method_getResponseHeaders) |  |  |

---
### Details
* File: [lib/Common/Exceptions/InternalServerError.php](../../lib/Common/Exceptions/InternalServerError.php)
* Package: Default
* Class Hierarchy:  
  * [\Exception](\Exception)
  * [\YooKassa\Common\Exceptions\ApiException](../classes/YooKassa-Common-Exceptions-ApiException.md)
  * \YooKassa\Common\Exceptions\InternalServerError

---
## Constants
<a name="constant_HTTP_CODE" class="anchor"></a>
###### HTTP_CODE
```php
HTTP_CODE = 500
```



---
## Properties
<a name="property_retryAfter"></a>
#### public $retryAfter : mixed
---
**Type:** <a href="../mixed"><abbr title="mixed">mixed</abbr></a>

**Details:**


<a name="property_type"></a>
#### public $type : mixed
---
**Type:** <a href="../mixed"><abbr title="mixed">mixed</abbr></a>

**Details:**


<a name="property_responseBody"></a>
#### protected $responseBody : ?string
---
**Type:** <a href="../?string"><abbr title="?string">?string</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Common\Exceptions\ApiException](../classes/YooKassa-Common-Exceptions-ApiException.md)


<a name="property_responseHeaders"></a>
#### protected $responseHeaders : array
---
**Type:** <a href="../array"><abbr title="array">array</abbr></a>

**Details:**
* Inherited From: [\YooKassa\Common\Exceptions\ApiException](../classes/YooKassa-Common-Exceptions-ApiException.md)



---
## Methods
<a name="method___construct" class="anchor"></a>
#### public __construct() : mixed

```php
public __construct(mixed $responseHeaders = [], mixed $responseBody = &#039;&#039;) : mixed
```

**Summary**

Constructor.

**Details:**
* Inherited From: [\YooKassa\Common\Exceptions\InternalServerError](../classes/YooKassa-Common-Exceptions-InternalServerError.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">mixed</code> | responseHeaders  | HTTP header |
| <code lang="php">mixed</code> | responseBody  | HTTP body |

**Returns:** mixed - 


<a name="method_getResponseBody" class="anchor"></a>
#### public getResponseBody() : ?string

```php
public getResponseBody() : ?string
```

**Details:**
* Inherited From: [\YooKassa\Common\Exceptions\ApiException](../classes/YooKassa-Common-Exceptions-ApiException.md)

**Returns:** ?string - 


<a name="method_getResponseHeaders" class="anchor"></a>
#### public getResponseHeaders() : string[]

```php
public getResponseHeaders() : string[]
```

**Details:**
* Inherited From: [\YooKassa\Common\Exceptions\ApiException](../classes/YooKassa-Common-Exceptions-ApiException.md)

**Returns:** string[] - 



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