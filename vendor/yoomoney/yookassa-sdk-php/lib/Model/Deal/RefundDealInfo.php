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

namespace YooKassa\Model\Deal;

use YooKassa\Common\AbstractObject;
use YooKassa\Common\ListObject;
use YooKassa\Common\ListObjectInterface;
use YooKassa\Model\Receipt\SettlementInterface;
use YooKassa\Validator\Constraints as Assert;

/**
 * Класс, представляющий модель RefundDealInfo.
 *
 * Данные о сделке, в составе которой проходит возврат.
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 * @property string $id Идентификатор сделки
 * @property ListObjectInterface|SettlementInterface[] $refund_settlements Данные о распределении денег
 * @property ListObjectInterface|SettlementInterface[] $refundSettlements Данные о распределении денег
 */
class RefundDealInfo extends AbstractObject
{
    /**
     * Идентификатор сделки
     *
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $_id = null;

    /**
     * Данные о распределении денег.
     *
     * @var SettlementInterface[]|null
     */
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[Assert\AllType(SettlementPayoutRefund::class)]
    #[Assert\Type(ListObject::class)]
    private ?ListObject $_refund_settlements = null;

    /**
     * Возвращает Id сделки.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->_id;
    }

    /**
     * Устанавливает Id сделки.
     *
     * @param string|null $id
     *
     * @return self
     */
    public function setId(?string $id = null): self
    {
        $this->_id = $this->validatePropertyValue('_id', $id);
        return $this;
    }

    /**
     * Возвращает массив оплат, обеспечивающих выдачу товара.
     *
     * @return SettlementInterface[]|ListObjectInterface Массив оплат, обеспечивающих выдачу товара
     */
    public function getRefundSettlements(): ListObjectInterface
    {
        if ($this->_refund_settlements === null) {
            $this->_refund_settlements = new ListObject(SettlementPayoutRefund::class);
        }
        return $this->_refund_settlements;
    }

    /**
     * Устанавливает массив оплат, обеспечивающих выдачу товара.
     *
     * @param ListObjectInterface|array|null $refund_settlements Данные о распределении денег.
     *
     * @return self
     */
    public function setRefundSettlements(mixed $refund_settlements = null): self
    {
        $this->_refund_settlements = $this->validatePropertyValue('_refund_settlements', $refund_settlements);
        return $this;
    }
}
