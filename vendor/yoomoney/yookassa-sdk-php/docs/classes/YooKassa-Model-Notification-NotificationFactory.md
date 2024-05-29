# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Model\Notification\NotificationFactory
### Namespace: [\YooKassa\Model\Notification](../namespaces/yookassa-model-notification.md)
---
**Summary:**

Фабрика для получения конкретного объекта уведомления.


---
### Examples
Пример скрипта обработки уведомления

```php
require_once '../vendor/autoload.php';

try {
    $source = file_get_contents('php://input');
    $data = json_decode($source, true);

    $factory = new \YooKassa\Model\Notification\NotificationFactory();
    $notificationObject = $factory->factory($data);
    $responseObject = $notificationObject->getObject();

    $client = new \YooKassa\Client();

    if (!$client->isNotificationIPTrusted($_SERVER['REMOTE_ADDR'])) {
        header('HTTP/1.1 400 Something went wrong');

        exit;
    }

    if (\YooKassa\Model\Notification\NotificationEventType::PAYMENT_SUCCEEDED === $notificationObject->getEvent()) {
        $someData = [
            'paymentId' => $responseObject->getId(),
            'paymentStatus' => $responseObject->getStatus(),
        ];
    // Специфичная логика
    // ...
    } elseif (\YooKassa\Model\Notification\NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE === $notificationObject->getEvent()) {
        $someData = [
            'paymentId' => $responseObject->getId(),
            'paymentStatus' => $responseObject->getStatus(),
        ];
    // Специфичная логика
    // ...
    } elseif (\YooKassa\Model\Notification\NotificationEventType::PAYMENT_CANCELED === $notificationObject->getEvent()) {
        $someData = [
            'paymentId' => $responseObject->getId(),
            'paymentStatus' => $responseObject->getStatus(),
        ];
    // Специфичная логика
    // ...
    } elseif (\YooKassa\Model\Notification\NotificationEventType::REFUND_SUCCEEDED === $notificationObject->getEvent()) {
        $someData = [
            'refundId' => $responseObject->getId(),
            'refundStatus' => $responseObject->getStatus(),
            'paymentId' => $responseObject->getPaymentId(),
        ];
    // ...
    // Специфичная логика
    } else {
        header('HTTP/1.1 400 Something went wrong');

        exit;
    }

    // Специфичная логика
    // ...

    $client->setAuth('xxxxxx', 'test_XXXXXXX');

    // Получим актуальную информацию о платеже
    if ($paymentInfo = $client->getPaymentInfo($someData['paymentId'])) {
        $paymentStatus = $paymentInfo->getStatus();
    // Специфичная логика
    // ...
    } else {
        header('HTTP/1.1 400 Something went wrong');

        exit;
    }
} catch (Exception $e) {
    header('HTTP/1.1 400 Something went wrong');

    exit;
}

```

---
### Constants
* No constants found

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [factory()](../classes/YooKassa-Model-Notification-NotificationFactory.md#method_factory) |  |  |

---
### Details
* File: [lib/Model/Notification/NotificationFactory.php](../../lib/Model/Notification/NotificationFactory.php)
* Package: Default
* Class Hierarchy:
  * \YooKassa\Model\Notification\NotificationFactory

---
## Methods
<a name="method_factory" class="anchor"></a>
#### public factory() : \YooKassa\Model\Notification\AbstractNotification

```php
public factory(array $data) : \YooKassa\Model\Notification\AbstractNotification
```

**Details:**
* Inherited From: [\YooKassa\Model\Notification\NotificationFactory](../classes/YooKassa-Model-Notification-NotificationFactory.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">array</code> | data  |  |

**Returns:** \YooKassa\Model\Notification\AbstractNotification - 



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