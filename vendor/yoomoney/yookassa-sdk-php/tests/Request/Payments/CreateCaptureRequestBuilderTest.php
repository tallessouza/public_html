<?php

namespace Tests\YooKassa\Request\Payments;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use YooKassa\Helpers\Random;
use YooKassa\Model\AmountInterface;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\Deal\SettlementPayoutPaymentType;
use YooKassa\Model\MonetaryAmount;
use YooKassa\Model\Payment\Payment;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;
use YooKassa\Model\Receipt\ReceiptItem;
use YooKassa\Model\Receipt\ReceiptItemAmount;
use YooKassa\Request\Payments\CreateCaptureRequestBuilder;
use YooKassa\Request\Payments\TransferData;

/**
 * @internal
 */
class CreateCaptureRequestBuilderTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetAmountValue($options): void
    {
        $builder = new CreateCaptureRequestBuilder();
        $builder->setAmount($options['amount']);
        $instance = $builder->build();

        if (empty($options['amount'])) {
            self::assertNull($instance->getAmount());
        } else {
            self::assertNotNull($instance->getAmount());
            self::assertEquals($options['amount']['value'], $instance->getAmount()->getValue());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetDeal($options): void
    {
        $builder = new CreateCaptureRequestBuilder();
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
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetAirline($options): void
    {
        $builder = new CreateCaptureRequestBuilder();
        $builder->setAirline($options['airline']);
        $instance = $builder->build();

        if (empty($options['airline'])) {
            self::assertNull($instance->getAirline());
        } else {
            self::assertNotNull($instance->getAirline());
            self::assertEquals($options['airline'], $instance->getAirline()->toArray());
        }
    }

    /**
     * @dataProvider validAmountDataProvider
     */
    public function testSetAmount(AmountInterface $amount): void
    {
        $builder = new CreateCaptureRequestBuilder();

        $builder->setAmount($amount);
        $instance = $builder->build();

        self::assertNotNull($instance->getAmount());
        self::assertEquals($amount->getValue(), $instance->getAmount()->getValue());
        self::assertEquals($amount->getCurrency(), $instance->getAmount()->getCurrency());

        $builder->setAmount([
            'value' => $amount->getValue(),
            'currency' => $amount->getCurrency(),
        ]);
        $instance = $builder->build();

        self::assertNotNull($instance->getAmount());
        self::assertEquals($amount->getValue(), $instance->getAmount()->getValue());
        self::assertEquals($amount->getCurrency(), $instance->getAmount()->getCurrency());
    }

    /**
     * @dataProvider invalidAmountDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidAmount($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreateCaptureRequestBuilder();
        $builder->setAmount($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetAmountCurrency($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        $builder->setCurrency($options['amount']['currency']);
        $instance = $builder->build($options);

        self::assertNotNull($instance->getAmount());
        self::assertEquals($options['amount']['currency'], $instance->getAmount()->getCurrency());
    }

    /**
     * @dataProvider invalidCurrencyDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidCurrency($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreateCaptureRequestBuilder();
        $builder->setCurrency($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetReceiptItems($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        $builder->setReceiptItems($options['receipt']['items']);
        $builder->setReceiptEmail($options['receipt']['customer']['email']);
        $builder->setReceiptPhone($options['receipt']['customer']['phone']);
        $instance = $builder->build();

        if (empty($options['receipt']['items'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertSameSize($options['receipt']['items'], $instance->getReceipt()->getItems());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testAddReceiptItems($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        foreach ($options['receipt']['items'] as $item) {
            if ($item instanceof ReceiptItem) {
                $builder->addReceiptItem(
                    $item->getDescription(),
                    $item->getPrice()->getValue(),
                    $item->getQuantity(),
                    $item->getVatCode()
                );
            } else {
                $builder->addReceiptItem($item['description'], $item['amount']['value'], $item['quantity'], $item['vatCode']);
            }
        }
        $builder->setReceiptEmail($options['receipt']['customer']['email']);
        $builder->setReceiptPhone($options['receipt']['customer']['phone']);
        $instance = $builder->build();

        if (empty($options['receipt']['items'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertSameSize($options['receipt']['items'], $instance->getReceipt()->getItems());
            foreach ($instance->getReceipt()->getItems() as $item) {
                self::assertFalse($item->isShipping());
            }
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testAddReceiptShipping($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        foreach ($options['receipt']['items'] as $item) {
            if ($item instanceof ReceiptItem) {
                $builder->addReceiptShipping(
                    $item->getDescription(),
                    $item->getPrice()->getValue(),
                    $item->getVatCode()
                );
            } else {
                $builder->addReceiptShipping($item['description'], $item['amount']['value'], $item['vatCode']);
            }
        }
        $builder->setReceiptEmail($options['receipt']['customer']['email']);
        $builder->setReceiptPhone($options['receipt']['customer']['phone']);
        $instance = $builder->build();

        if (empty($options['receipt']['items'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertSameSize($options['receipt']['items'], $instance->getReceipt()->getItems());
            foreach ($instance->getReceipt()->getItems() as $item) {
                self::assertTrue($item->isShipping());
            }
        }
    }

    /**
     * @dataProvider invalidItemsDataProvider
     */
    public function testSetInvalidReceiptItems(array $items): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreateCaptureRequestBuilder();
        $builder->setReceiptItems($items);
    }

    public static function invalidItemsDataProvider()
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
                        'description' => 'test',
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
                        'description' => 'test',
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
        ];
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetReceiptEmail($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        $builder->setReceiptItems($options['receipt']['items']);
        $builder->setReceiptEmail($options['receipt']['customer']['email']);
        $builder->setReceiptPhone($options['receipt']['customer']['phone']);
        $instance = $builder->build();

        if (empty($options['receipt']['items'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals($options['receipt']['customer']['email'], $instance->getReceipt()->getCustomer()->getEmail());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetReceiptPhone($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        $builder->setReceiptItems($options['receipt']['items']);
        $builder->setReceiptEmail($options['receipt']['customer']['email']);
        $builder->setReceiptPhone($options['receipt']['customer']['phone']);
        $instance = $builder->build();

        if (empty($options['receipt']['items'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals($options['receipt']['customer']['phone'], $instance->getReceipt()->getCustomer()->getPhone());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetReceiptTaxSystemCode($options): void
    {
        $builder = new CreateCaptureRequestBuilder();

        $builder->setReceiptItems($options['receipt']['items']);
        $builder->setReceiptEmail($options['receipt']['customer']['email']);
        $builder->setReceiptPhone($options['receipt']['customer']['phone']);
        $builder->setTaxSystemCode($options['receipt']['taxSystemCode']);
        $instance = $builder->build();

        if (empty($options['receipt']['items'])) {
            self::assertNull($instance->getReceipt());
        } else {
            self::assertNotNull($instance->getReceipt());
            self::assertEquals($options['receipt']['taxSystemCode'], $instance->getReceipt()->getTaxSystemCode());
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
        $builder = new CreateCaptureRequestBuilder();
        $builder->setTaxSystemCode($value);
    }

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

        $builder = new CreateCaptureRequestBuilder();
        $builder->setReceipt($receipt);
        $instance = $builder->build();

        self::assertEquals($receipt['tax_system_code'], $instance->getReceipt()->getTaxSystemCode());
        self::assertEquals($receipt['customer']['email'], $instance->getReceipt()->getCustomer()->getEmail());
        self::assertEquals($receipt['customer']['phone'], $instance->getReceipt()->getCustomer()->getPhone());
        self::assertCount(1, $instance->getReceipt()->getItems());

        $receipt = $instance->getReceipt();

        $builder = new CreateCaptureRequestBuilder();
        $builder->setReceipt($instance->getReceipt());
        $instance = $builder->build();

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
        $builder = new CreateCaptureRequestBuilder();
        $builder->setReceipt($value);
    }

    public static function invalidReceiptDataProvider()
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

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testBuild($options): void
    {
        $builder = new CreateCaptureRequestBuilder();
        $instance = $builder->build($options);
        if (!empty($options['amount'])) {
            self::assertNotNull($instance->getAmount());
            self::assertEquals($options['amount']['value'], $instance->getAmount()->getValue());
            self::assertEquals($options['amount']['currency'], $instance->getAmount()->getCurrency());
        } else {
            self::assertNull($instance->getAmount());
        }
    }

    public static function validDataProvider()
    {
        $receiptItem = new ReceiptItem();
        $receiptItem->setPrice(new ReceiptItemAmount(1, CurrencyCode::USD));
        $receiptItem->setQuantity(1);
        $receiptItem->setDescription('test');
        $receiptItem->setVatCode(3);

        $result = [
            [
                [
                    'amount' => [
                        'value' => Random::int(1, 1000000),
                        'currency' => Random::value(CurrencyCode::getValidValues()),
                    ],
                    'receipt' => [
                        'items' => [
                            [
                                'description' => 'test',
                                'quantity' => Random::int(1, 100),
                                'amount' => [
                                    'value' => Random::int(1, 1000000),
                                    'currency' => Random::value(CurrencyCode::getValidValues()),
                                ],
                                'vatCode' => Random::int(1, 6),
                            ],
                            $receiptItem,
                        ],
                        'customer' => [
                            'email' => 'johndoe@yoomoney.ru',
                            'phone' => null,
                        ],
                        'taxSystemCode' => null,
                    ],
                    'transfers' => null,
                    'deal' => null,
                    'airline' => null,
                    'merchant_customer_id' => null,
                ],
            ],
        ];
        for ($i = 0; $i < 10; $i++) {
            $request = [
                'amount' => [
                    'value' => Random::int(1, 1000000),
                    'currency' => Random::value(CurrencyCode::getValidValues()),
                ],
                'receipt' => [
                    'items' => [
                        $receiptItem,
                    ],
                    'customer' => [
                        'email' => null,
                        'phone' => Random::str(10, '0123456789'),
                    ],
                    'taxSystemCode' => Random::int(1, 6),
                ],
                'transfers' => [
                    new TransferData([
                        'account_id' => Random::str(36),
                        'amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                        'platform_fee_amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                        'description' => Random::str(1, TransferData::MAX_LENGTH_DESCRIPTION),
                    ]),
                ],
                'deal' => [
                    'id' => Random::str(36, 50),
                    'settlements' => [
                        [
                            'type' => Random::value(SettlementPayoutPaymentType::getValidValues()),
                            'amount' => [
                                'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                        ]
                    ],
                ],
                'airline' => [
                    'booking_reference' => 'IIIKRV',
                    'ticket_number' => '12342123413',
                    'passengers' => [
                        [
                            'first_name' => 'SERGEI',
                            'last_name' => 'IVANOV',
                        ],
                    ],
                    'legs' => [
                        [
                            'departure_airport' => 'LED',
                            'destination_airport' => 'AMS',
                            'departure_date' => '2018-06-20',
                        ],
                    ],
                ],
                'merchant_customer_id' => Random::str(5, Payment::MAX_LENGTH_MERCHANT_CUSTOMER_ID),
            ];
            $result[] = [$request];
        }

        return $result;
    }

    public static function validAmountDataProvider()
    {
        return [
            [new MonetaryAmount(Random::int(1, 1000000))],
            [new MonetaryAmount(Random::int(1, 1000000)), Random::value(CurrencyCode::getValidValues())],
        ];
    }

    public static function invalidAmountDataProvider()
    {
        return [
            [-1],
            [Random::str(10)],
            [new stdClass()],
            [true],
            [false],
        ];
    }

    public static function invalidCurrencyDataProvider()
    {
        return [
            [''],
            [-1],
            [Random::str(10)],
            [true],
            [false],
        ];
    }

    public static function invalidVatIdDataProvider()
    {
        return [
            [false],
            [0],
            [7],
            [Random::int(-100, -1)],
            [Random::int(7, 100)],
        ];
    }

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
        $builder = new CreateCaptureRequestBuilder();
        $builder->setDeal($value);
    }
}
