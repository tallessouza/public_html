<?php

namespace Tests\YooKassa\Model\Settings;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\Settings\FiscalizationProvider;
use YooKassa\Model\Settings\Me;
use YooKassa\Validator\Exceptions\EmptyPropertyValueException;
use YooKassa\Helpers\Random;
use YooKassa\Model\Deal\DealBalanceAmount;
use YooKassa\Model\Metadata;
use YooKassa\Validator\Exceptions\InvalidPropertyValueTypeException;

/**
 * @internal
 */
class MeTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetAccountId(array $options): void
    {
        $instance = new Me();

        $instance->setAccountId($options['account_id']);
        self::assertEquals($options['account_id'], $instance->getAccountId());
        self::assertEquals($options['account_id'], $instance->accountId);
        self::assertEquals($options['account_id'], $instance->account_id);

        $instance = new Me();
        $instance->account_id = $options['account_id'];
        self::assertEquals($options['account_id'], $instance->getAccountId());
        self::assertEquals($options['account_id'], $instance->accountId);
        self::assertEquals($options['account_id'], $instance->account_id);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetStatus(array $options): void
    {
        $instance = new Me();

        $instance->setStatus($options['status']);
        self::assertEquals($options['status'], $instance->getStatus());
        self::assertEquals($options['status'], $instance->status);

        $instance = new Me();
        $instance->status = $options['status'];
        self::assertEquals($options['status'], $instance->getStatus());
        self::assertEquals($options['status'], $instance->status);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetFiscalization(array $options): void
    {
        $instance = new Me();

        $instance->setFiscalization($options['fiscalization']);
        if (is_array($options['fiscalization'])) {
            self::assertEquals($options['fiscalization'], $instance->getFiscalization()->toArray());
            self::assertEquals($options['fiscalization'], $instance->fiscalization->toArray());
        }

        $instance = new Me();
        $instance->fiscalization = $options['fiscalization'];
        if (is_array($options['fiscalization'])) {
            self::assertEquals($options['fiscalization'], $instance->getFiscalization()->toArray());
            self::assertEquals($options['fiscalization'], $instance->fiscalization->toArray());
        }
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetFiscalizationEnabled(array $options): void
    {
        $instance = new Me();

        $instance->setFiscalizationEnabled($options['fiscalization_enabled']);
        self::assertEquals($options['fiscalization_enabled'], $instance->getFiscalizationEnabled());
        self::assertEquals($options['fiscalization_enabled'], $instance->fiscalization_enabled);
        self::assertEquals($options['fiscalization_enabled'], $instance->fiscalizationEnabled);

        $instance = new Me();
        $instance->fiscalization_enabled = $options['fiscalization_enabled'];
        self::assertEquals($options['fiscalization_enabled'], $instance->getFiscalizationEnabled());
        self::assertEquals($options['fiscalization_enabled'], $instance->fiscalization_enabled);
        self::assertEquals($options['fiscalization_enabled'], $instance->fiscalizationEnabled);

        $instance = new Me();
        $instance->fiscalizationEnabled = $options['fiscalization_enabled'];
        self::assertEquals($options['fiscalization_enabled'], $instance->getFiscalizationEnabled());
        self::assertEquals($options['fiscalization_enabled'], $instance->fiscalization_enabled);
        self::assertEquals($options['fiscalization_enabled'], $instance->fiscalizationEnabled);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetPaymentMethods(array $options): void
    {
        $instance = new Me();

        $instance->setPaymentMethods($options['payment_methods']);
        self::assertEquals($options['payment_methods'], $instance->getPaymentMethods());
        self::assertEquals($options['payment_methods'], $instance->payment_methods);
        self::assertEquals($options['payment_methods'], $instance->paymentMethods);

        $instance = new Me();
        $instance->payment_methods = $options['payment_methods'];
        self::assertEquals($options['payment_methods'], $instance->getPaymentMethods());
        self::assertEquals($options['payment_methods'], $instance->payment_methods);
        self::assertEquals($options['payment_methods'], $instance->paymentMethods);

        $instance = new Me();
        $instance->paymentMethods = $options['payment_methods'];
        self::assertEquals($options['payment_methods'], $instance->getPaymentMethods());
        self::assertEquals($options['payment_methods'], $instance->payment_methods);
        self::assertEquals($options['payment_methods'], $instance->paymentMethods);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetPayoutMethods(array $options): void
    {
        $instance = new Me();

        $instance->setPayoutMethods($options['payout_methods']);
        self::assertEquals($options['payout_methods'], $instance->getPayoutMethods());
        self::assertEquals($options['payout_methods'], $instance->payout_methods);
        self::assertEquals($options['payout_methods'], $instance->payoutMethods);

        $instance = new Me();
        $instance->payout_methods = $options['payout_methods'];
        self::assertEquals($options['payout_methods'], $instance->getPayoutMethods());
        self::assertEquals($options['payout_methods'], $instance->payout_methods);
        self::assertEquals($options['payout_methods'], $instance->payoutMethods);

        $instance = new Me();
        $instance->payoutMethods = $options['payout_methods'];
        self::assertEquals($options['payout_methods'], $instance->getPayoutMethods());
        self::assertEquals($options['payout_methods'], $instance->payout_methods);
        self::assertEquals($options['payout_methods'], $instance->payoutMethods);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetPayoutBalance(array $options): void
    {
        $instance = new Me();

        $instance->setPayoutBalance($options['payout_balance']);
        if (is_array($options['payout_balance'])) {
            self::assertEquals($options['payout_balance'], $instance->getPayoutBalance()->toArray());
            self::assertEquals($options['payout_balance'], $instance->payout_balance->toArray());
            self::assertEquals($options['payout_balance'], $instance->payoutBalance->toArray());
        }

        $instance = new Me();
        $instance->payout_balance = $options['payout_balance'];
        if (is_array($options['payout_balance'])) {
            self::assertEquals($options['payout_balance'], $instance->getPayoutBalance()->toArray());
            self::assertEquals($options['payout_balance'], $instance->payout_balance->toArray());
            self::assertEquals($options['payout_balance'], $instance->payoutBalance->toArray());
        }

        $instance = new Me();
        $instance->payoutBalance = $options['payout_balance'];
        if (is_array($options['payout_balance'])) {
            self::assertEquals($options['payout_balance'], $instance->getPayoutBalance()->toArray());
            self::assertEquals($options['payout_balance'], $instance->payout_balance->toArray());
            self::assertEquals($options['payout_balance'], $instance->payoutBalance->toArray());
        }
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidPayoutBalance($value): void
    {
        if (empty($value['payout_balance'])) {
            $this->expectException(EmptyPropertyValueException::class);
            $instance = new Me();
            $instance->setPayoutBalance($value['payout_balance']);
        } elseif (!is_array($value['payout_balance']) && !($value['payout_balance'] instanceof DealBalanceAmount)) {
            $this->expectException(InvalidPropertyValueTypeException::class);
            $instance = new Me();
            $instance->setPayoutBalance($value['payout_balance']);
        }
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param mixed $value
     */
    public function testSetterInvalidPayoutBalance($value): void
    {
        if (empty($value['payout_balance'])) {
            $this->expectException(EmptyPropertyValueException::class);
            $instance = new Me();
            $instance->payout_balance = $value['payout_balance'];
        } elseif (!is_array($value['payout_balance']) && !($value['payout_balance'] instanceof DealBalanceAmount)) {
            $this->expectException(InvalidPropertyValueTypeException::class);
            $instance = new Me();
            $instance->payout_balance = $value['payout_balance'];
        }
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetTest(array $options): void
    {
        $instance = new Me();

        $instance->setTest($options['test']);
        self::assertSame($options['test'], $instance->getTest());
        self::assertSame($options['test'], $instance->test);

        $instance = new Me();
        $instance->test = $options['test'];
        self::assertSame($options['test'], $instance->getTest());
        self::assertSame($options['test'], $instance->test);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetName(array $options): void
    {
        $instance = new Me();

        $instance->setName($options['name']);
        self::assertSame($options['name'], $instance->getName());
        self::assertSame($options['name'], $instance->name);

        $instance = new Me();
        $instance->name = $options['name'];
        self::assertSame($options['name'], $instance->getName());
        self::assertSame($options['name'], $instance->name);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetItn(array $options): void
    {
        $instance = new Me();

        $instance->setItn($options['itn']);
        self::assertSame($options['itn'], $instance->getItn());
        self::assertSame($options['itn'], $instance->itn);

        $instance = new Me();
        $instance->itn = $options['itn'];
        self::assertSame($options['itn'], $instance->getItn());
        self::assertSame($options['itn'], $instance->itn);
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function validDataProvider(): array
    {
        $result = [];
        for ($i = 0; $i < 10; $i++) {
            $item = [
                'account_id' => Random::str(5, 6, '1234567890'),
                'test' => Random::bool(),
                'fiscalization' => [
                    'provider' => Random::value(FiscalizationProvider::getValidValues()),
                    'enabled' => Random::bool(),
                ],
                'fiscalization_enabled' => Random::bool(),
                'payment_methods' => [
                    'bank_card',
                    'yoo_money',
                    'sbp',
                ],
                'payout_methods' => [
                    'bank_card',
                    'yoo_money',
                    'sbp',
                ],
                'payout_balance' => ['value' => number_format(Random::float(0, 999.99), 2, '.', ''), 'currency' => CurrencyCode::RUB],
                'itn' => Random::str(10, 12, '1234567890'),
                'name' => Random::str(2, 50),
                'status' => Random::value([Me::STATUS_ENABLED, Me::STATUS_DISABLED]),
            ];
            $result[] = [$item];
        }

        return $result;
    }

    /**
     * @return \array[][]
     * @throws Exception
     */
    public static function invalidDataProvider(): array
    {
        $result = [

        ];

        $invalidData = [
            new stdClass(),
            'invalid_value',
            new Metadata(),
            Random::str(5, 10),
        ];
        $invalidObjectData = [
            new stdClass(),
            new Metadata(),
            new DateTime(),
        ];
        for ($i = 0; $i < 3; $i++) {
            $item = [
                'status' => $invalidData[$i],
                'payout_balance' => $invalidObjectData[$i],
                'test' => $invalidData[$i],
            ];
            $result[] = [$item];
        }

        return $result;
    }

}
