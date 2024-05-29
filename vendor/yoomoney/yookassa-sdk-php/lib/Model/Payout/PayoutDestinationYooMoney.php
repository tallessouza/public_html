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

use YooKassa\Model\Payment\PaymentMethodType;
use YooKassa\Validator\Constraints as Assert;

/**
 * Класс, представляющий модель PayoutToYooMoneyDestination.
 *
 * Выплаты на кошелек ЮMoney.
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 * @property string $accountNumber Номер кошелька в ЮMoney, с которого была произведена оплата
 * @property string $account_number Номер кошелька в ЮMoney, с которого была произведена оплата
 */
class PayoutDestinationYooMoney extends AbstractPayoutDestination
{
    /** @var int Максимальная длина номера кошелька. */
    public const MAX_LENGTH_ACCOUNT_NUMBER = 33;

    /** @var int Минимальная длина номера кошелька. */
    public const MIN_LENGTH_ACCOUNT_NUMBER = 11;

    /**
     * Номер кошелька ЮMoney, например ~`41001614575714`. Длина — от 11 до 33 цифр.
     *
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Regex("/^[0-9]{11,33}$/")]
    private ?string $_account_number = null;

    /**
     * Конструктор PayoutDestinationYooMoney.
     *
     * @param array|null $data
     */
    public function __construct(?array $data = [])
    {
        parent::__construct($data);
        $this->setType(PaymentMethodType::YOO_MONEY);
    }

    /**
     * Возвращает номер кошелька в ЮMoney, с которого была произведена оплата.
     *
     * @return string|null Номер кошелька в ЮMoney
     */
    public function getAccountNumber(): ?string
    {
        return $this->_account_number;
    }

    /**
     * Устанавливает номер кошелька в ЮMoney, с которого была произведена оплата.
     *
     * @param string|null $account_number Номер кошелька ЮMoney
     *
     * @return self
     */
    public function setAccountNumber(?string $account_number = null): self
    {
        $this->_account_number = $this->validatePropertyValue('_account_number', $account_number);
        return $this;
    }
}
