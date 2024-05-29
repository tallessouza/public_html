<?php

namespace Tests\YooKassa\Model\Refund\RefundMethod;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Model\Payment\PaymentMethodType;
use YooKassa\Model\Refund\RefundMethod\AbstractRefundMethod;
use YooKassa\Model\Refund\RefundMethod\RefundMethodFactory;
use YooKassa\Model\Refund\RefundMethodType;

/**
 * @internal
 */
class RefundMethodFactoryTest extends TestCase
{
    /**
     * @dataProvider validTypeDataProvider
     */
    public function testFactory(string $type): void
    {
        $instance = $this->getTestInstance();
        $paymentData = $instance->factory($type);
        self::assertNotNull($paymentData);
        self::assertInstanceOf(AbstractRefundMethod::class, $paymentData);
        self::assertEquals($type, $paymentData->getType());
    }

    /**
     * @dataProvider validArrayDataProvider
     */
    public function testFactoryFromArray(array $options, string $actualType): void
    {
        $instance = $this->getTestInstance();

        $paymentData = $instance->factoryFromArray($options);
        self::assertNotNull($paymentData);
        self::assertInstanceOf(AbstractRefundMethod::class, $paymentData);

        foreach ($options as $property => $value) {
            if ($property === 'type') {
                $value = $actualType;
            }
            if (is_object($paymentData->{$property})) {
                self::assertEquals($paymentData->{$property}->toArray(), $value);
            } else {
                self::assertEquals($paymentData->{$property}, $value);
            }
        }

        $type = $actualType;
        unset($options['type']);
        $paymentData = $instance->factoryFromArray($options, $type);
        self::assertNotNull($paymentData);
        self::assertInstanceOf(AbstractRefundMethod::class, $paymentData);

        self::assertEquals($type, $paymentData->getType());
        foreach ($options as $property => $value) {
            if ($property === 'type') {
                $value = $actualType;
            }
            if (is_object($paymentData->{$property})) {
                self::assertEquals($paymentData->{$property}->toArray(), $value);
            } else {
                self::assertEquals($paymentData->{$property}, $value);
            }
        }
    }

    /**
     * @dataProvider invalidDataArrayDataProvider
     *
     * @param mixed $options
     */
    public function testInvalidFactoryFromArray($options): void
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = $this->getTestInstance();
        $instance->factoryFromArray($options);
    }

    public static function validTypeDataProvider()
    {
        $result = [];
        foreach (RefundMethodType::getValidValues() as $value) {
            $result[] = [$value];
        }

        return $result;
    }

    public static function invalidTypeDataProvider()
    {
        return [
            [''],
            [0],
            [1],
            [-1],
            ['5'],
            [Random::str(10)],
        ];
    }

    public static function validArrayDataProvider()
    {
        $result = [
            [
                [
                    'type' => RefundMethodType::SBP,
                    'sbp_operation_id' => Random::str(26, 36),
                ],
                RefundMethodType::SBP,
            ],
            [
                [
                    'type' => PaymentMethodType::CASH,
                ],
                RefundMethodType::UNKNOWN,
            ],
        ];
        foreach (RefundMethodType::getValidValues() as $value) {
            $result[] = [['type' => $value], $value];
        }

        return $result;
    }

    public static function invalidDataArrayDataProvider()
    {
        return [
            [[]],
        ];
    }

    protected function getTestInstance(): RefundMethodFactory
    {
        return new RefundMethodFactory();
    }
}
