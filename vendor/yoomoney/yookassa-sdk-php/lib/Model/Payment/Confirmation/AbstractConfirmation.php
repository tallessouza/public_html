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

use YooKassa\Common\AbstractObject;
use YooKassa\Model\Payment\ConfirmationType;
use YooKassa\Validator\Constraints as Assert;

/**
 * Класс, представляющий модель AbstractConfirmation.
 *
 * Выбранный способ подтверждения платежа. Присутствует, когда платеж ожидает подтверждения от пользователя.
 * Подробнее о [сценариях подтверждения](/developers/payment-acceptance/getting-started/payment-process#user-confirmation)
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 *
 * @property string $type Тип подтверждения платежа
 *
 * @method string getConfirmationUrl() Для ConfirmationRedirect
 * @method string getConfirmationToken() Для ConfirmationEmbedded
 * @method string getConfirmationData() Для ConfirmationQr
 */
abstract class AbstractConfirmation extends AbstractObject
{
    /**
     * Тип подтверждения платежа.
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice(callback: [ConfirmationType::class, 'getValidValues'])]
    protected ?string $_type = null;

    /**
     * Возвращает тип подтверждения платежа.
     */
    public function getType(): ?string
    {
        return $this->_type;
    }

    /**
     * Устанавливает тип подтверждения платежа
     *
     * @param string|null $type Тип подтверждения платежа
     *
     * @return self
     */
    public function setType(?string $type = null): self
    {
        $this->_type = $this->validatePropertyValue('_type', $type);
        return $this;
    }
}
