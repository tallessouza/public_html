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

namespace YooKassa\Model\Payment\PaymentMethod;

use YooKassa\Model\Payment\PaymentMethodType;
use YooKassa\Validator\Constraints as Assert;

/**
 * Класс, представляющий модель PaymentMethodMobileBalance.
 *
 * Оплата с баланса мобильного телефона.
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 *
 * @property string $phone Номер телефона в формате ITU-T E.164 с которого плательщик собирается произвести оплату
 */
class PaymentMethodMobileBalance extends AbstractPaymentMethod
{
    /**
     * Номер телефона в формате ITU-T E.164 с которого плательщик собирается произвести оплату.
     *
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Regex("/^[0-9]{4,15}$/")]
    private ?string $_phone = null;

    public function __construct(?array $data = [])
    {
        parent::__construct($data);
        $this->setType(PaymentMethodType::MOBILE_BALANCE);
    }

    /**
     * Возвращает номер телефона в формате ITU-T E.164.
     */
    public function getPhone(): ?string
    {
        return $this->_phone;
    }

    /**
     * Устанавливает номер телефона в формате ITU-T E.164.
     */
    public function setPhone(?string $phone): self
    {
        $this->_phone = $this->validatePropertyValue('_phone', $phone);
        return $this;
    }
}
