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

namespace YooKassa\Model\Payment\PaymentMethod\B2b\Sberbank;

use YooKassa\Common\AbstractEnum;

/**
 * Класс, представляющий модель CalculatedVatData.
 *
 * Налоговая ставка НДС.
 *
 * Возможные значения:
 * - 7 - 7%
 * - 10 - 10%
 * - 18 - 18%
 * - 20 - 20%
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 */
class VatDataRate extends AbstractEnum
{
    /** 7% */
    public const RATE_7 = '7';

    /** 10% */
    public const RATE_10 = '10';

    /** 18% */
    public const RATE_18 = '18';

    /** 20% */
    public const RATE_20 = '20';

    protected static array $validValues = [
        self::RATE_7 => true,
        self::RATE_10 => true,
        self::RATE_18 => true,
        self::RATE_20 => true,
    ];
}
