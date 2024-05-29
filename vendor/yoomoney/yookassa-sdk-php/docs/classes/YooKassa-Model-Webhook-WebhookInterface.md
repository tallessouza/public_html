# [YooKassa API SDK](../home.md)

# Interface: WebhookInterface
### Namespace: [\YooKassa\Model\Webhook](../namespaces/yookassa-model-webhook.md)
---
**Summary:**

Interface WebhookInterface.

---
### Constants
* No constants found

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [getEvent()](../classes/YooKassa-Model-Webhook-WebhookInterface.md#method_getEvent) |  | Возвращает событие, о котором уведомляет ЮKassa. |
| public | [getId()](../classes/YooKassa-Model-Webhook-WebhookInterface.md#method_getId) |  | Возвращает идентификатор webhook. |
| public | [getUrl()](../classes/YooKassa-Model-Webhook-WebhookInterface.md#method_getUrl) |  | Возвращает URL, на который ЮKassa будет отправлять уведомления. |
| public | [setEvent()](../classes/YooKassa-Model-Webhook-WebhookInterface.md#method_setEvent) |  | Устанавливает событие, о котором уведомляет ЮKassa. |
| public | [setId()](../classes/YooKassa-Model-Webhook-WebhookInterface.md#method_setId) |  | Устанавливает идентификатор webhook. |
| public | [setUrl()](../classes/YooKassa-Model-Webhook-WebhookInterface.md#method_setUrl) |  | Устанавливает URL, на который ЮKassa будет отправлять уведомления. |

---
### Details
* File: [lib/Model/Webhook/WebhookInterface.php](../../lib/Model/Webhook/WebhookInterface.php)
* Package: \YooKassa\Model
* See Also:
  * [](https://yookassa.ru/developers/api)

---
### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| category |  | Interface |
| author |  | cms@yoomoney.ru |
| property |  | Идентификатор webhook |
| property |  | Событие, о котором уведомляет ЮKassa |
| property |  | URL, на который ЮKassa будет отправлять уведомления |

---
## Methods
<a name="method_getId" class="anchor"></a>
#### public getId() : string|null

```php
public getId() : string|null
```

**Summary**

Возвращает идентификатор webhook.

**Details:**
* Inherited From: [\YooKassa\Model\Webhook\WebhookInterface](../classes/YooKassa-Model-Webhook-WebhookInterface.md)

**Returns:** string|null - Идентификатор webhook


<a name="method_setId" class="anchor"></a>
#### public setId() : self

```php
public setId(string|null $id = null) : self
```

**Summary**

Устанавливает идентификатор webhook.

**Details:**
* Inherited From: [\YooKassa\Model\Webhook\WebhookInterface](../classes/YooKassa-Model-Webhook-WebhookInterface.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | id  | Идентификатор webhook |

**Returns:** self - 


<a name="method_getEvent" class="anchor"></a>
#### public getEvent() : string

```php
public getEvent() : string
```

**Summary**

Возвращает событие, о котором уведомляет ЮKassa.

**Details:**
* Inherited From: [\YooKassa\Model\Webhook\WebhookInterface](../classes/YooKassa-Model-Webhook-WebhookInterface.md)

**Returns:** string - Событие, о котором уведомляет ЮKassa


<a name="method_setEvent" class="anchor"></a>
#### public setEvent() : self

```php
public setEvent(string|null $event = null) : self
```

**Summary**

Устанавливает событие, о котором уведомляет ЮKassa.

**Details:**
* Inherited From: [\YooKassa\Model\Webhook\WebhookInterface](../classes/YooKassa-Model-Webhook-WebhookInterface.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | event  | Событие, о котором уведомляет ЮKassa |

**Returns:** self - 


<a name="method_getUrl" class="anchor"></a>
#### public getUrl() : string

```php
public getUrl() : string
```

**Summary**

Возвращает URL, на который ЮKassa будет отправлять уведомления.

**Details:**
* Inherited From: [\YooKassa\Model\Webhook\WebhookInterface](../classes/YooKassa-Model-Webhook-WebhookInterface.md)

**Returns:** string - URL, на который ЮKassa будет отправлять уведомления


<a name="method_setUrl" class="anchor"></a>
#### public setUrl() : self

```php
public setUrl(string|null $url = null) : self
```

**Summary**

Устанавливает URL, на который ЮKassa будет отправлять уведомления.

**Details:**
* Inherited From: [\YooKassa\Model\Webhook\WebhookInterface](../classes/YooKassa-Model-Webhook-WebhookInterface.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">string OR null</code> | url  | URL, на который ЮKassa будет отправлять уведомления |

**Returns:** self - 




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