<?php

namespace Tests\YooKassa\Request\Payments;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Request\Payments\FraudData;

/**
 * @internal
 */
class FraudDataTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetToppedUpPhone(array $options): void
    {
        $instance = new FraudData();

        $instance->setToppedUpPhone($options['topped_up_phone']);
        self::assertEquals($options['topped_up_phone'], $instance->getToppedUpPhone());
        self::assertEquals($options['topped_up_phone'], $instance->topped_up_phone);

        $instance = new FraudData();
        $instance->topped_up_phone = $options['topped_up_phone'];
        self::assertEquals($options['topped_up_phone'], $instance->getToppedUpPhone());
        self::assertEquals($options['topped_up_phone'], $instance->topped_up_phone);
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param mixed $value
     * @throws Exception
     */
    public function testSetInvalidToppedUpPhone($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new FraudData();
        $instance->setToppedUpPhone($value);
    }

    public static function validDataProvider()
    {
        $result = [];
        $result[] = [['topped_up_phone' => null]];
        $result[] = [['topped_up_phone' => '']];
        for ($i = 0; $i < 10; $i++) {
            $payment = [
                'topped_up_phone' => Random::str(4, 15, '0123456789'),
            ];
            $result[] = [$payment];
        }

        return $result;
    }

    public static function invalidDataProvider()
    {
        return [
            [Random::str(1, 3, '0123456789')],
            [Random::str(16, 30, '0123456789')],
            [Random::str(4, 16)],
        ];
    }

    /**
     * @dataProvider validMerchantCustomerBankAccountDataProvider
     */
    public function testGetSetMerchantCustomerBankAccount(array $options): void
    {
        if (!isset($options['merchant_customer_bank_account'])) {
            return;
        }

        $instance = new FraudData();

        $instance->setMerchantCustomerBankAccount($options['merchant_customer_bank_account']);
        if (is_array($options['merchant_customer_bank_account'])) {
            self::assertEquals($options['merchant_customer_bank_account'], $instance->getMerchantCustomerBankAccount()->toArray());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchant_customer_bank_account->toArray());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchantCustomerBankAccount->toArray());
        } else {
            self::assertEquals($options['merchant_customer_bank_account'], $instance->getMerchantCustomerBankAccount());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchant_customer_bank_account);
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchantCustomerBankAccount);
        }

        $instance = new FraudData();
        $instance->merchant_customer_bank_account = $options['merchant_customer_bank_account'];
        if (is_array($options['merchant_customer_bank_account'])) {
            self::assertEquals($options['merchant_customer_bank_account'], $instance->getMerchantCustomerBankAccount()->toArray());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchant_customer_bank_account->toArray());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchantCustomerBankAccount->toArray());
        } else {
            self::assertEquals($options['merchant_customer_bank_account'], $instance->getMerchantCustomerBankAccount());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchant_customer_bank_account);
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchantCustomerBankAccount);
        }

        $instance = new FraudData();
        $instance->merchantCustomerBankAccount = $options['merchant_customer_bank_account'];
        if (is_array($options['merchant_customer_bank_account'])) {
            self::assertEquals($options['merchant_customer_bank_account'], $instance->getMerchantCustomerBankAccount()->toArray());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchant_customer_bank_account->toArray());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchantCustomerBankAccount->toArray());
        } else {
            self::assertEquals($options['merchant_customer_bank_account'], $instance->getMerchantCustomerBankAccount());
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchant_customer_bank_account);
            self::assertEquals($options['merchant_customer_bank_account'], $instance->merchantCustomerBankAccount);
        }
    }

    /**
     * @dataProvider invalidMerchantCustomerBankAccountDataProvider
     *
     * @param mixed $value
     * @throws Exception
     */
    public function testSetInvalidMerchantCustomerBankAccount($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new FraudData();
        $instance->setMerchantCustomerBankAccount($value);
    }

    public static function validMerchantCustomerBankAccountDataProvider()
    {
        $result = [];
        $result[] = [['topped_up_phone' => null]];
        $result[] = [['topped_up_phone' => '', 'merchant_customer_bank_account' => null]];
        for ($i = 0; $i < 10; $i++) {
            $payment = [
                'topped_up_phone' => Random::str(4, 15, '0123456789'),
                'merchant_customer_bank_account' => [
                    'account_number' => Random::str(20, 20, '0123456789'),
                    'bic' => Random::str(20, 20, '0123456789'),
                ],
            ];
            $result[] = [$payment];
        }

        return $result;
    }

    public static function invalidMerchantCustomerBankAccountDataProvider()
    {
        return [
            [Random::str(1, 3, '0123456789')],
            [Random::str(16, 30, '0123456789')],
            [Random::str(4, 16)],
        ];
    }
}
