<?php

namespace Tests\YooKassa\Request\Payments;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Request\Payments\MerchantCustomerBankAccount;

/**
 * @internal
 */
class MerchantCustomerBankAccountTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetAccountNumber(array $options): void
    {
        $instance = new MerchantCustomerBankAccount();

        $instance->setAccountNumber($options['account_number']);
        self::assertEquals($options['account_number'], $instance->getAccountNumber());
        self::assertEquals($options['account_number'], $instance->accountNumber);
        self::assertEquals($options['account_number'], $instance->account_number);

        $instance = new MerchantCustomerBankAccount();
        $instance->accountNumber = $options['account_number'];
        self::assertEquals($options['account_number'], $instance->getAccountNumber());
        self::assertEquals($options['account_number'], $instance->accountNumber);
        self::assertEquals($options['account_number'], $instance->account_number);

        $instance = new MerchantCustomerBankAccount();
        $instance->account_number = $options['account_number'];
        self::assertEquals($options['account_number'], $instance->getAccountNumber());
        self::assertEquals($options['account_number'], $instance->accountNumber);
        self::assertEquals($options['account_number'], $instance->account_number);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetBic(array $options): void
    {
        $instance = new MerchantCustomerBankAccount();

        $instance->setBic($options['bic']);
        self::assertEquals($options['bic'], $instance->getBic());
        self::assertEquals($options['bic'], $instance->bic);

        $instance = new MerchantCustomerBankAccount();
        $instance->bic = $options['bic'];
        self::assertEquals($options['bic'], $instance->getBic());
        self::assertEquals($options['bic'], $instance->bic);
    }

    public static function validDataProvider()
    {
        $result = [];
        $result[] = [['account_number' => null, 'bic' => '']];
        $result[] = [['account_number' => '', 'bic' => null]];
        for ($i = 0; $i < 10; $i++) {
            $acc = [
                'account_number' => Random::str(20, 20, '0123456789'),
                'bic' => Random::str(20, 20, '0123456789'),
            ];
            $result[] = [$acc];
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
}
