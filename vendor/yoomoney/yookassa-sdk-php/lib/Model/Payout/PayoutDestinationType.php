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

namespace YooKassa\Model\Payout;

use YooKassa\Common\AbstractEnum;
use YooKassa\Model\Payment\PaymentMethodType;

/**
 * Класс, представляющий модель PayoutDestinationType.
 *
 * Виды выплат. Возможные значения:
 * - `yoo_money` - Выплата в кошелек ЮMoney
 * - `bank_card` - Выплата на произвольную банковскую карту
 * - `sbp` - Выплата через СБП на счет в банке или платежном сервисе
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 */
class PayoutDestinationType extends AbstractEnum
{
    /** Выплата в кошелек ЮMoney */
    public const YOO_MONEY = 'yoo_money';

    /** Выплата на произвольную банковскую карту */
    public const BANK_CARD = 'bank_card';

    /** Выплата через СБП на счет в банке или платежном сервисе */
    public const SBP = 'sbp';

    /**
     * Возвращает список доступных значений
     * @return string[]
     */
    protected static array $validValues = [
        PaymentMethodType::YOO_MONEY => true,
        PaymentMethodType::BANK_CARD => true,
        PaymentMethodType::SBP => true,
    ];
}
