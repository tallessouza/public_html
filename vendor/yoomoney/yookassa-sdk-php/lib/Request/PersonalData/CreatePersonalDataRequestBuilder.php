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

namespace YooKassa\Request\PersonalData;

use YooKassa\Common\AbstractRequestBuilder;
use YooKassa\Common\AbstractRequestInterface;
use YooKassa\Common\Exceptions\InvalidPropertyValueTypeException;
use YooKassa\Common\Exceptions\InvalidRequestException;
use YooKassa\Model\Metadata;

/**
 * Класс, представляющий модель CreatePersonalDataRequestBuilder.
 *
 * Класс билдера объектов запросов к API на создание платежа.
 *
 * @example 02-builder.php 210 20 Пример использования билдера
 *
 * @category Class
 * @package  YooKassa\Request
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 */
class CreatePersonalDataRequestBuilder extends AbstractRequestBuilder
{
    /**
     * Собираемый объект запроса.
     *
     * @var CreatePersonalDataRequest|null
     */
    protected ?AbstractRequestInterface $currentObject = null;

    /**
     * Устанавливает тип персональных данных.
     *
     * @param string $value Тип персональных данных
     *
     * @return self Инстанс билдера запросов
     */
    public function setType(string $value): self
    {
        $this->currentObject->setType($value);

        return $this;
    }

    /**
     * Устанавливает фамилию пользователя.
     *
     * @param string $value фамилия пользователя
     */
    public function setLastName(string $value): self
    {
        $this->currentObject->setLastName($value);

        return $this;
    }

    /**
     * Устанавливает имя пользователя.
     *
     * @param string $value имя пользователя
     */
    public function setFirstName(string $value): self
    {
        $this->currentObject->setFirstName($value);

        return $this;
    }

    /**
     * Устанавливает отчество пользователя.
     *
     * @param null|string $value Отчество пользователя
     */
    public function setMiddleName(?string $value): self
    {
        $this->currentObject->setMiddleName($value);

        return $this;
    }

    /**
     * Устанавливает метаданные, привязанные к платежу.
     *
     * @param null|array|Metadata $value Метаданные платежа, устанавливаемые мерчантом
     *
     * @return CreatePersonalDataRequestBuilder Инстанс текущего билдера
     */
    public function setMetadata(mixed $value): CreatePersonalDataRequestBuilder
    {
        $this->currentObject->setMetadata($value);

        return $this;
    }

    /**
     * Строит и возвращает объект запроса для отправки в API ЮKassa.
     *
     * @param null|array $options Массив параметров для установки в объект запроса
     *
     * @return CreatePersonalDataRequestInterface|AbstractRequestInterface Инстанс объекта запроса
     */
    public function build(array $options = null): AbstractRequestInterface
    {
        return parent::build($options);
    }

    /**
     * Инициализирует объект запроса, который в дальнейшем будет собираться билдером
     *
     * @return CreatePersonalDataRequest Инстанс собираемого объекта запроса к API
     */
    protected function initCurrentObject(): CreatePersonalDataRequest
    {
        return new CreatePersonalDataRequest();
    }
}
