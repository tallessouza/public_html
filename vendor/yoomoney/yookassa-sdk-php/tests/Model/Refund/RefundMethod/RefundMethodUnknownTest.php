<?php

namespace Tests\YooKassa\Model\Refund\RefundMethod;

use YooKassa\Model\Refund\RefundMethod\AbstractRefundMethod;
use YooKassa\Model\Refund\RefundMethod\RefundMethodUnknown;
use YooKassa\Model\Refund\RefundMethodType;

/**
 * @internal
 */
class RefundMethodUnknownTest extends AbstractTestRefundMethod
{
    protected function getTestInstance(): AbstractRefundMethod
    {
        return new RefundMethodUnknown();
    }

    protected function getExpectedType(): string
    {
        return RefundMethodType::UNKNOWN;
    }
}
