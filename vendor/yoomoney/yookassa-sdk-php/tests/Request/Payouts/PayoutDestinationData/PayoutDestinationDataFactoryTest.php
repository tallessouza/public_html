<?php

namespace Tests\YooKassa\Request\Payouts\PayoutDestinationData;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Model\Payment\PaymentMethodType;
use YooKassa\Model\Payout\PayoutDestinationType;
use YooKassa\Request\Payouts\PayoutDestinationData\AbstractPayoutDestinationData;
use YooKassa\Request\Payouts\PayoutDestinationData\PayoutDestinationDataBankCardCard;
use YooKassa\Request\Payouts\PayoutDestinationData\PayoutDestinationDataFactory;

/**
 * @internal
 */
class PayoutDestinationDataFactoryTest extends TestCase
{
    /**
     * @dataProvider validTypeDataProvider
     */
    public function testFactory(string $type): void
    {
        $instance = $this->getTestInstance();
        $paymentData = $instance->factory($type);
        self::assertNotNull($paymentData);
        self::assertInstanceOf(AbstractPayoutDestinationData::class, $paymentData);
        self::assertEquals($type, $paymentData->getType());
    }

    /**
     * @dataProvider invalidTypeDataProvider
     *
     * @param mixed $type
     */
    public function testInvalidFactory($type): void
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = $this->getTestInstance();
        $instance->factory($type);
    }

    /**
     * @dataProvider validArrayDataProvider
     */
    public function testFactoryFromArray(array $options): void
    {
        $instance = $this->getTestInstance();
        $paymentData = $instance->factoryFromArray($options);
        self::assertNotNull($paymentData);
        self::assertInstanceOf(AbstractPayoutDestinationData::class, $paymentData);

        foreach ($options as $property => $value) {
            self::assertEquals($paymentData->{$property}, $value);
        }

        $type = $options['type'];
        unset($options['type']);
        $paymentData = $instance->factoryFromArray($options, $type);
        self::assertNotNull($paymentData);
        self::assertInstanceOf(AbstractPayoutDestinationData::class, $paymentData);

        self::assertEquals($type, $paymentData->getType());
        foreach ($options as $property => $value) {
            self::assertEquals($paymentData->{$property}, $value);
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

    /**
     * @return array
     */
    public static function validTypeDataProvider(): array
    {
        $result = [];
        foreach (PayoutDestinationType::getEnabledValues() as $value) {
            $result[] = [$value];
        }

        return $result;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function invalidTypeDataProvider(): array
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

    /**
     * @return \array[][]
     * @throws \Exception
     */
    public static function validArrayDataProvider(): array
    {
        $result = [
            [
                [
                    'type' => PaymentMethodType::BANK_CARD,
                    'card' => new PayoutDestinationDataBankCardCard(['number' => Random::str(16, '0123456789')]),
                ],
            ],
            [
                [
                    'type' => PaymentMethodType::YOO_MONEY,
                    'account_number' => Random::str(11, 33, '1234567890'),
                ],
            ],
        ];
        foreach (PayoutDestinationType::getEnabledValues() as $value) {
            $result[] = [['type' => $value]];
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function invalidDataArrayDataProvider(): array
    {
        return [
            [[]],
            [['type' => 'test']],
        ];
    }

    /**
     * @return PayoutDestinationDataFactory
     */
    protected function getTestInstance(): PayoutDestinationDataFactory
    {
        return new PayoutDestinationDataFactory();
    }
}
