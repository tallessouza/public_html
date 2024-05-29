<?php

namespace Tests\YooKassa\Request\Refunds;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use YooKassa\Helpers\Random;
use YooKassa\Model\AmountInterface;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\Deal\SettlementPayoutPaymentType;
use YooKassa\Model\MonetaryAmount;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;
use YooKassa\Model\Receipt\ReceiptItem;
use YooKassa\Model\Receipt\ReceiptItemAmount;
use YooKassa\Model\Refund\Source;
use YooKassa\Request\Payments\CreatePaymentRequestBuilder;
use YooKassa\Request\Refunds\CreateRefundRequestBuilder;

/**
 * @internal
 */
class CreateRefundRequestBuilderTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     * @throws Exception
     */
    public function testSetPaymentId($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        try {
            $builder->build(['amountValue' => Random::int(1, 100)]);
        } catch (RuntimeException $e) {
            $builder->setPaymentId($options['paymentId']);
            $instance = $builder->build(['amount' => Random::int(1, 100)]);
            self::assertEquals($options['paymentId'], $instance->getPaymentId());

            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetAmountValue($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        try {
            $builder->build(['paymentId' => Random::str(36)]);
        } catch (RuntimeException $e) {
            $builder->setAmount($options['amount']);
            $instance = $builder->build(['paymentId' => Random::str(36)]);
            if ($options['amount'] instanceof AmountInterface) {
                self::assertEquals($options['amount']->getValue(), $instance->getAmount()->getValue());
            } else {
                self::assertEquals($options['amount']['value'], $instance->getAmount()->getValue());
            }

            if ($options['amount'] instanceof AmountInterface) {
                $builder->setAmount([
                    'value' => $options['amount']->getValue(),
                    'currency' => 'USD',
                ]);
                $instance = $builder->build(['paymentId' => Random::str(36)]);
                self::assertEquals($options['amount']->getValue(), $instance->getAmount()->getValue());
            } else {
                $builder->setAmount($options['amount']);
                $instance = $builder->build(['paymentId' => Random::str(36)]);
                self::assertEquals($options['amount']['value'], $instance->getAmount()->getValue());
            }

            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetAmountCurrency($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        $builder->setCurrency($options['amount']['currency']);
        $instance = $builder->build($options);
        self::assertEquals($options['amount']['currency'], $instance->getAmount()->getCurrency());
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetComment($options): void
    {
        $builder = new CreateRefundRequestBuilder();
        $instance = $builder->build([
            'paymentId' => Random::str(36),
            'amount' => Random::int(1, 100),
        ]);
        self::assertNull($instance->getDescription());

        $builder->setDescription($options['description']);
        $instance = $builder->build([
            'paymentId' => Random::str(36),
            'amount' => Random::int(1, 100),
        ]);
        if (empty($options['description'])) {
            self::assertNull($instance->getDescription());
        } else {
            self::assertEquals($options['description'], $instance->getDescription());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testBuild($options): void
    {
        $builder = new CreateRefundRequestBuilder();
        $instance = $builder->build($options);

        self::assertEquals($options['paymentId'], $instance->getPaymentId());
        if ($options['amount'] instanceof AmountInterface) {
            self::assertEquals($options['amount']->getValue(), $instance->getAmount()->getValue());
        } else {
            self::assertEquals($options['amount']['value'], $instance->getAmount()->getValue());
        }
        self::assertEquals($options['amount']['currency'], $instance->getAmount()->getCurrency());
        if (empty($options['description'])) {
            self::assertNull($instance->getDescription());
        } else {
            self::assertEquals($options['description'], $instance->getDescription());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetReceiptItems($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        $builder->setReceiptItems($options['receiptItems']);
        $builder->setReceiptEmail($options['receiptEmail']);
        $instance = $builder->build($this->getRequiredData());

        if (empty($options['receiptItems'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals(count($options['receiptItems']), count($instance->getReceipt()->getItems()));
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testAddReceiptItems($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        foreach ($options['receiptItems'] as $item) {
            if ($item instanceof ReceiptItem) {
                $builder->addReceiptItem(
                    $item->getDescription(),
                    $item->getPrice()->getValue(),
                    $item->getQuantity(),
                    $item->getVatCode()
                );
            } else {
                $builder->addReceiptItem($item['description'], $item['price']['value'], $item['quantity'], $item['vatCode']);
            }
        }
        $builder->setReceiptEmail($options['receiptEmail']);
        $instance = $builder->build($this->getRequiredData());

        if (empty($options['receiptItems'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals(count($options['receiptItems']), count($instance->getReceipt()->getItems()));
            foreach ($instance->getReceipt()->getItems() as $item) {
                self::assertFalse($item->isShipping());
            }
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testAddReceiptShipping($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        foreach ($options['receiptItems'] as $item) {
            if ($item instanceof ReceiptItem) {
                $builder->addReceiptShipping(
                    $item->getDescription(),
                    $item->getPrice()->getValue(),
                    $item->getVatCode()
                );
            } else {
                $builder->addReceiptShipping($item['description'], $item['price']['value'], $item['vatCode']);
            }
        }
        $builder->setReceiptEmail($options['receiptEmail']);
        $instance = $builder->build($this->getRequiredData());

        if (empty($options['receiptItems'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals(count($options['receiptItems']), count($instance->getReceipt()->getItems()));
            foreach ($instance->getReceipt()->getItems() as $item) {
                self::assertTrue($item->isShipping());
            }
        }
    }

    /**
     * @dataProvider invalidItemsDataProvider
     *
     * @param mixed $items
     */
    public function testSetInvalidReceiptItems($items): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreateRefundRequestBuilder();
        $builder->setReceiptItems($items);
    }

    /**
     * @throws Exception
     */
    public function testSetReceipt(): void
    {
        $receipt = [
            'tax_system_code' => Random::int(1, 6),
            'customer' => [
                'email' => 'johndoe@yoomoney.ru',
                'phone' => Random::str(4, 15, '0123456789'),
            ],
            'items' => [
                [
                    'description' => 'test',
                    'quantity' => 123,
                    'amount' => [
                        'value' => 321,
                        'currency' => 'USD',
                    ],
                    'vat_code' => Random::int(1, 6),
                    'payment_subject' => PaymentSubject::COMMODITY,
                    'payment_mode' => PaymentMode::PARTIAL_PREPAYMENT,
                ],
            ],
        ];

        $builder = new CreateRefundRequestBuilder();
        $builder->setReceipt($receipt);
        $instance = $builder->build($this->getRequiredData());

        self::assertEquals($receipt['tax_system_code'], $instance->getReceipt()->getTaxSystemCode());
        self::assertEquals($receipt['customer']['email'], $instance->getReceipt()->getCustomer()->getEmail());
        self::assertEquals($receipt['customer']['phone'], $instance->getReceipt()->getCustomer()->getPhone());
        self::assertCount(1, $instance->getReceipt()->getItems());

        $receipt = $instance->getReceipt();

        $builder = new CreateRefundRequestBuilder();
        $builder->setReceipt($instance->getReceipt());
        $instance = $builder->build($this->getRequiredData());

        self::assertEquals($receipt['tax_system_code'], $instance->getReceipt()->getTaxSystemCode());
        self::assertEquals($receipt['customer']['email'], $instance->getReceipt()->getCustomer()->getEmail());
        self::assertEquals($receipt['customer']['phone'], $instance->getReceipt()->getCustomer()->getPhone());
        self::assertCount(1, $instance->getReceipt()->getItems());
    }

    /**
     * @dataProvider invalidReceiptDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidReceipt($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreateRefundRequestBuilder();
        $builder->setReceipt($value);
    }

    public static function invalidReceiptDataProvider(): array
    {
        return [
            [null],
            [true],
            [false],
            [1],
            [1.1],
            ['test'],
            [new stdClass()],
        ];
    }

    public static function invalidItemsDataProvider(): array
    {
        return [
            [
                [
                    [
                        'price' => [1],
                        'quantity' => -1.4,
                        'vatCode' => 10,
                    ],
                ],
            ],
            [
                [
                    [
                        'title' => 'test',
                        'quantity' => -1.4,
                        'vatCode' => 3,
                    ],
                ],
            ],
            [
                [
                    [
                        'description' => 'test',
                        'quantity' => 1.4,
                        'vatCode' => -3,
                    ],
                ],
            ],
            [
                [
                    [
                        'title' => 'test',
                        'price' => [123],
                        'quantity' => 1.4,
                        'vatCode' => 7,
                    ],
                ],
            ],
            [
                [
                    [
                        'description' => 'test',
                        'price' => [123],
                        'quantity' => -1.4,
                    ],
                ],
            ],
            [
                [
                    [
                        'title' => 'test',
                        'price' => [1],
                        'vatCode' => 7,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetReceiptEmail($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        $builder->setReceiptItems($options['receiptItems']);
        $builder->setReceiptEmail($options['receiptEmail']);
        $instance = $builder->build($this->getRequiredData());

        if (empty($options['receiptItems'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals($options['receiptEmail'], $instance->getReceipt()->getCustomer()->getEmail());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetReceiptPhone($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        $builder->setReceiptItems($options['receiptItems']);
        $builder->setReceiptEmail($options['receiptEmail']);
        $builder->setReceiptPhone($options['receiptPhone']);
        $instance = $builder->build($this->getRequiredData());

        if (empty($options['receiptItems'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals($options['receiptPhone'], $instance->getReceipt()->getCustomer()->getPhone());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetReceiptTaxSystemCode($options): void
    {
        $builder = new CreateRefundRequestBuilder();

        $builder->setReceiptItems($options['receiptItems']);
        $builder->setReceiptEmail($options['receiptEmail']);
        $builder->setTaxSystemCode($options['taxSystemCode']);
        $instance = $builder->build($this->getRequiredData());

        if (empty($options['receiptItems'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals($options['taxSystemCode'], $instance->getReceipt()->getTaxSystemCode());
        }
    }

    /**
     * @dataProvider invalidVatIdDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidTaxSystemId($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreateRefundRequestBuilder();
        $builder->setTaxSystemCode($value);
    }

    /**
     * @throws Exception
     */
    public static function validDataProvider(): array
    {
        $result = [
            [
                [
                    'paymentId' => Random::str(36),
                    'amount' => [
                        'value' => Random::int(1, 999999),
                        'currency' => Random::value(CurrencyCode::getValidValues()),
                    ],
                    'description' => null,
                    'receiptItems' => [],
                    'receiptEmail' => null,
                    'receiptPhone' => null,
                    'taxSystemCode' => Random::int(1, 6),
                    'sources' => [
                        new Source([
                            'account_id' => Random::str(36),
                            'amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                            'platform_fee_amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                        ]),
                    ],
                    'deal' => [
                        'id' => Random::str(36, 50),
                        'refund_settlements' => [
                            [
                                'type' => SettlementPayoutPaymentType::PAYOUT,
                                'amount' => [
                                    'value' => round(Random::float(10.00, 100.00), 2),
                                    'currency' => Random::value(CurrencyCode::getValidValues()),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'paymentId' => Random::str(36),
                    'amount' => new MonetaryAmount(
                        Random::int(1, 999999),
                        Random::value(CurrencyCode::getValidValues())
                    ),
                    'description' => '',
                    'receiptItems' => [],
                    'receiptEmail' => '',
                    'receiptPhone' => '',
                    'taxSystemCode' => Random::int(1, 6),
                    'sources' => [
                        new Source([
                            'account_id' => Random::str(36),
                            'amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                            'platform_fee_amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                        ]),
                    ],
                    'deal' => [
                        'id' => Random::str(36, 50),
                        'refund_settlements' => [
                            [
                                'type' => Random::value(SettlementPayoutPaymentType::getValidValues()),
                                'amount' => [
                                    'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                                    'currency' => Random::value(CurrencyCode::getValidValues()),
                                ],
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $items = [
            new ReceiptItem(),
            [
                'description' => 'test',
                'price' => [
                    'value' => Random::int(1, 999999),
                    'currency' => Random::value(CurrencyCode::getValidValues()),
                ],
                'quantity' => Random::int(1, 9999),
                'vatCode' => Random::int(1, 6),
            ],
        ];
        $items[0]->setDescription('test1');
        $items[0]->setQuantity(Random::int(1, 9999));
        $items[0]->setPrice(new ReceiptItemAmount(Random::int(1, 999999)));
        $items[0]->setVatCode(Random::int(1, 6));
        for ($i = 0; $i < 10; $i++) {
            $request = [
                'paymentId' => Random::str(36),
                'amount' => [
                    'value' => Random::int(1, 999999),
                    'currency' => Random::value(CurrencyCode::getValidValues()),
                ],
                'description' => uniqid('', true),
                'receiptItems' => $items,
                'receiptEmail' => 'johndoe@yoomoney.ru',
                'receiptPhone' => Random::str(4, 15, '0123456789'),
                'taxSystemCode' => Random::int(1, 6),
                'sources' => [
                    new Source([
                        'account_id' => Random::str(36),
                        'amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                        'platform_fee_amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                    ]),
                ],
                'deal' => [
                    'id' => Random::str(36, 50),
                    'refund_settlements' => [
                        [
                            'type' => SettlementPayoutPaymentType::PAYOUT,
                            'amount' => [
                                'value' => round(Random::float(10.00, 100.00), 2),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                        ],
                    ],
                ],
            ];
            $result[] = [$request];
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public static function invalidVatIdDataProvider(): array
    {
        return [
            [false],
            [0],
            [7],
            [Random::int(-100, -1)],
            [Random::int(7, 100)],
        ];
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetDeal($options): void
    {
        $builder = new CreateRefundRequestBuilder();
        $builder->setPaymentId($options['paymentId']);
        $builder->setAmount($options['amount']);
        $builder->setDeal($options['deal']);
        $instance = $builder->build();

        if (empty($options['deal'])) {
            self::assertNull($instance->getDeal());
        } else {
            self::assertNotNull($instance->getDeal());
            self::assertEquals($options['deal'], $instance->getDeal()->toArray());
        }
    }

    /**
     * @throws Exception
     */
    public static function invalidDealDataProvider(): array
    {
        return [
            [true],
            [false],
            [new stdClass()],
            [0],
            [7],
            [Random::int(-100, -1)],
            [Random::int(7, 100)],
        ];
    }

    /**
     * @dataProvider invalidDealDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidDeal($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePaymentRequestBuilder();
        $builder->setDeal($value);
    }

    /**
     * @throws Exception
     */
    private function getRequiredData(): array
    {
        return [
            'paymentId' => Random::str(36),
            'amount' => Random::int(1, 100),
        ];
    }
}
