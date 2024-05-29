<?php

/*
 * The MIT License
 *
 * Copyright (c) 2024 "YooMoney", NBСO LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace YooKassa\Model\Payment;

use YooKassa\Common\AbstractEnum;

/**
 * Класс, представляющий модель PaymentStatus.
 *
 * Статус платежа.
 *
 * Возможные значения:
 * - `pending` - Ожидает оплаты покупателем
 * - `waiting_for_capture` - Успешно оплачен покупателем, ожидает подтверждения магазином (capture или aviso)
 * - `succeeded` - Успешно оплачен и подтвержден магазином
 * - `canceled` - Неуспех оплаты или отменен магазином (cancel)
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 */
class PaymentStatus extends AbstractEnum
{
    /** Ожидает оплаты покупателем */
    public const PENDING = 'pending';

    /** Ожидает подтверждения магазином */
    public const WAITING_FOR_CAPTURE = 'waiting_for_capture';

    /** Успешно оплачен и подтвержден магазином */
    public const SUCCEEDED = 'succeeded';

    /** Неуспех оплаты или отменен магазином */
    public const CANCELED = 'canceled';

    protected static array $validValues = [
        self::PENDING => true,
        self::WAITING_FOR_CAPTURE => true,
        self::SUCCEEDED => true,
        self::CANCELED => true,
    ];
}
