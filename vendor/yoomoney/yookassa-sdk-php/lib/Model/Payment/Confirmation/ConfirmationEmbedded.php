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

namespace YooKassa\Model\Payment\Confirmation;

use YooKassa\Model\Payment\ConfirmationType;
use YooKassa\Validator\Constraints as Assert;

/**
 * Класс, представляющий модель ConfirmationEmbedded.
 *
 * Сценарий, при котором действия, необходимые для подтверждения платежа, будут зависеть от способа оплаты,
 * который пользователь выберет в виджете ЮKassa.
 * Подтверждение от пользователя получит ЮKassa — вам необходимо только встроить виджет к себе на страницу.
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 *
 * @property string $confirmationToken Токен для платежного виджета
 * @property string $confirmation_token Токен для платежного виджета
 */
class ConfirmationEmbedded extends AbstractConfirmation
{
    /**
     * Токен для инициализации платежного [виджета ЮKassa](/developers/payment-acceptance/integration-scenarios/widget/basics).
     *
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $_confirmation_token = null;

    public function __construct(?array $data = [])
    {
        parent::__construct($data);
        $this->setType(ConfirmationType::EMBEDDED);
    }

    /**
     * Возвращает токен для инициализации платежного виджета.
     *
     * @return string|null Токен для инициализации платежного виджета
     */
    public function getConfirmationToken(): ?string
    {
        return $this->_confirmation_token;
    }

    /**
     * Устанавливает токен для инициализации платежного виджета.
     *
     * @param string|null $confirmation_token Токен для инициализации платежного виджета
     *
     * @return self
     */
    public function setConfirmationToken(?string $confirmation_token = null): self
    {
        $this->_confirmation_token = $this->validatePropertyValue('_confirmation_token', $confirmation_token);
        return $this;
    }
}
