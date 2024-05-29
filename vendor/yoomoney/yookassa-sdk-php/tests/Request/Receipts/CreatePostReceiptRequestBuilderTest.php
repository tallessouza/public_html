<?php

namespace Tests\YooKassa\Request\Receipts;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use YooKassa\Helpers\Random;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\MonetaryAmount;
use YooKassa\Model\Receipt\AdditionalUserProps;
use YooKassa\Model\Receipt\IndustryDetails;
use YooKassa\Model\Receipt\OperationalDetails;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;
use YooKassa\Model\Receipt\ReceiptCustomer;
use YooKassa\Model\Receipt\ReceiptCustomerInterface;
use YooKassa\Model\Receipt\ReceiptItem;
use YooKassa\Model\Receipt\ReceiptType;
use YooKassa\Model\Receipt\SettlementType;
use YooKassa\Request\Receipts\CreatePostReceiptRequestBuilder;

/**
 * @internal
 */
class CreatePostReceiptRequestBuilderTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetItems($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['items'])) {
            self::assertNull($instance->getItems());
        } else {
            self::assertNotNull($instance->getItems());
            self::assertEquals(count($options['items']), count($instance->getItems()));
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetSettlements($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['settlements'])) {
            self::assertNull($instance->getSettlements());
        } else {
            self::assertNotNull($instance->getSettlements());
            self::assertEquals(count($options['settlements']), count($instance->getSettlements()));
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetCustomer($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['customer'])) {
            self::assertNull($instance->getCustomer());
        } else {
            self::assertNotNull($instance->getCustomer());
            if (!is_object($instance->getCustomer())) {
                self::assertEquals($options['customer'], $instance->getCustomer()->jsonSerialize());
            } else {
                self::assertTrue($instance->getCustomer() instanceof ReceiptCustomerInterface);
            }
        }
    }

    /**
     * @dataProvider invalidCustomerDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidCustomer($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setCustomer($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetType($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();

        $instance = $builder->build($options);

        if (empty($options['type'])) {
            self::assertNull($instance->getType());
        } else {
            self::assertNotNull($instance->getType());
            self::assertEquals($options['type'], $instance->getType());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetObjectId($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();

        $instance = $builder->build($options);

        if (empty($options['payment_id']) && empty($options['refund_id'])) {
            self::assertNull($instance->getObjectId());
        } else {
            self::assertNotNull($instance->getObjectId());
            if (!empty($options['payment_id'])) {
                self::assertEquals($options['payment_id'], $instance->getObjectId());
                self::assertEquals(ReceiptType::PAYMENT, $instance->getObjectType());
            }
            if (!empty($options['refund_id'])) {
                self::assertEquals($options['refund_id'], $instance->getObjectId());
                self::assertEquals(ReceiptType::REFUND, $instance->getObjectType());
            }
        }
    }

    /**
     * @dataProvider invalidTypeDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidType($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setType($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetSend($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();

        $instance = $builder->build($options);

        if (empty($options['send'])) {
            self::assertNull($instance->getSend());
        } else {
            self::assertNotNull($instance->getSend());
            self::assertEquals($options['send'], $instance->getSend());
        }
    }

    /**
     * @dataProvider invalidBooleanDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidSend($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setType($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSetTaxSystemCode($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();

        $instance = $builder->build($options);

        if (empty($options['tax_system_code'])) {
            self::assertNull($instance->getTaxSystemCode());
        } else {
            self::assertNotNull($instance->getTaxSystemCode());
            self::assertEquals($options['tax_system_code'], $instance->getTaxSystemCode());
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
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setTaxSystemCode($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetAdditionalUserProps($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['additional_user_props'])) {
            self::assertNull($instance->getAdditionalUserProps());
        } else {
            self::assertNotNull($instance->getAdditionalUserProps());
            if (!is_object($instance->getAdditionalUserProps())) {
                self::assertEquals($options['additional_user_props'], $instance->getAdditionalUserProps()->toArray());
            } else {
                self::assertTrue($instance->getAdditionalUserProps() instanceof AdditionalUserProps);
            }
        }
    }

    /**
     * @dataProvider invalidAdditionalUserPropsDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidAdditionalProps($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setAdditionalUserProps($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetReceiptIndustryDetails($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['receipt_industry_details'])) {
            self::assertEmpty($instance->getReceiptIndustryDetails());
        } else {
            self::assertNotNull($instance->getReceiptIndustryDetails());
            self::assertCount(count($options['receipt_industry_details']), $instance->getReceiptIndustryDetails());
        }
    }

    /**
     * @dataProvider invalidReceiptIndustryDetailsDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidReceiptIndustryDetails($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setReceiptIndustryDetails($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     *
     * @throws Exception
     */
    public function testSetReceiptOperationalDetails($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['receipt_operational_details'])) {
            self::assertNull($instance->getReceiptOperationalDetails());
        } else {
            self::assertNotNull($instance->getReceiptOperationalDetails());
            if (!is_object($instance->getReceiptOperationalDetails())) {
                self::assertEquals($options['receipt_operational_details'], $instance->getReceiptOperationalDetails()->toArray());
            } else {
                self::assertTrue($instance->getReceiptOperationalDetails() instanceof OperationalDetails);
            }
        }
    }

    /**
     * @dataProvider invalidReceiptOperationalDetailsDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidReceiptOperationalDetails($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setReceiptOperationalDetails($value);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param $value
     * @param mixed $options
     */
    public function testSetOnBehalfOf($options): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $instance = $builder->build($options);

        if (empty($options['on_behalf_of'])) {
            self::assertNull($instance->getOnBehalfOf());
        } else {
            self::assertNotNull($instance->getOnBehalfOf());
            self::assertEquals($options['on_behalf_of'], $instance->getOnBehalfOf());
        }
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $value
     */
    public function testSetCurrency($value): void
    {
        $builder = new CreatePostReceiptRequestBuilder();
        $builder->setItems($value['items']);
        $result = $builder->setCurrency(Random::value(CurrencyCode::getValidValues()));
        self::assertNotNull($result);
        self::assertInstanceOf(CreatePostReceiptRequestBuilder::class, $result);
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $value
     */
    public function testAddItem($value): void
    {
        $builder = new CreatePostReceiptRequestBuilder();

        foreach ($value['items'] as $item) {
            $result = $builder->addItem(new ReceiptItem($item));
            self::assertNotNull($result);
            self::assertInstanceOf(CreatePostReceiptRequestBuilder::class, $result);
        }
    }

    public static function setAmountDataProvider()
    {
        return [
            [
                new MonetaryAmount(
                    round(Random::float(0.1, 99.99), 2),
                    Random::value(CurrencyCode::getValidValues())
                ),
            ],
            [
                [
                    'value' => round(Random::float(0.1, 99.99), 2),
                    'currency' => Random::value(CurrencyCode::getValidValues()),
                ],
            ],
            ['100'],
        ];
    }

    public static function validDataProvider()
    {
        $type = Random::value(ReceiptType::getEnabledValues());
        $result = [
            [
                [
                    'customer' => new ReceiptCustomer(),
                    'items' => [
                        [
                            'description' => Random::str(128),
                            'quantity' => Random::int(1, 10),
                            'amount' => [
                                'value' => round(Random::float(0.1, 99.99), 2),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                            'vat_code' => Random::int(1, 6),
                            'payment_subject' => Random::value(PaymentSubject::getValidValues()),
                            'payment_mode' => Random::value(PaymentMode::getValidValues()),
                            'product_code' => Random::str(2, 96, '1234567890ABCDEF '),
                            'country_of_origin_code' => 'RU',
                            'customs_declaration_number' => Random::str(32),
                            'excise' => Random::float(0.0, 99.99),
                        ],
                    ],
                    'tax_system_code' => Random::int(1, 6),
                    'additional_user_props' => null,
                    'receipt_industry_details' => null,
                    'receipt_operational_details' => null,
                    'type' => 'payment',
                    'send' => true,
                    'settlements' => [
                        [
                            'type' => Random::value(SettlementType::getValidValues()),
                            'amount' => [
                                'value' => round(Random::float(0.1, 99.99), 2),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                        ],
                    ],
                    $type . '_id' => uniqid('', true),
                ],
            ],
        ];
        for ($i = 0; $i < 10; $i++) {
            $type = Random::value(ReceiptType::getEnabledValues());
            $request = [
                'customer' => [
                    'full_name' => Random::str(128),
                    'email' => 'johndoe@yoomoney.ru',
                    'phone' => Random::str(4, 12, '1234567890'),
                    'inn' => '1234567890',
                ],
                'items' => [
                    [
                        'description' => Random::str(128),
                        'quantity' => Random::int(1, 10),
                        'amount' => [
                            'value' => round(Random::float(0.1, 99.99), 2),
                            'currency' => Random::value(CurrencyCode::getValidValues()),
                        ],
                        'vat_code' => Random::int(1, 6),
                        'payment_subject' => Random::value(PaymentSubject::getValidValues()),
                        'payment_mode' => Random::value(PaymentMode::getValidValues()),
                        'product_code' => Random::str(2, 96, '0123456789ABCDEF '),
                        'country_of_origin_code' => 'RU',
                        'customs_declaration_number' => Random::str(32),
                        'excise' => round(Random::float(0.0, 99.99), 2),
                    ],
                ],
                'tax_system_code' => Random::int(1, 6),
                'additional_user_props' => [
                    'name' => Random::str(1, AdditionalUserProps::NAME_MAX_LENGTH),
                    'value' => Random::str(1, AdditionalUserProps::VALUE_MAX_LENGTH),
                ],
                'receipt_industry_details' => [
                    [
                        'federal_id' => Random::value([
                            '00' . Random::int(1, 9),
                            '0' . Random::int(1, 6) . Random::int(0, 9),
                            '07' . Random::int(0, 3)
                        ]),
                        'document_date' => date(IndustryDetails::DOCUMENT_DATE_FORMAT),
                        'document_number' => Random::str(1, IndustryDetails::DOCUMENT_NUMBER_MAX_LENGTH),
                        'value' => Random::str(1, IndustryDetails::VALUE_MAX_LENGTH),
                    ],
                ],
                'receipt_operational_details' => [
                    'operation_id' => Random::int(0, OperationalDetails::OPERATION_ID_MAX_VALUE),
                    'value' => Random::str(1, OperationalDetails::VALUE_MAX_LENGTH),
                    'created_at' => date(YOOKASSA_DATE),
                ],
                'type' => $type,
                'send' => true,
                'on_behalf_of' => Random::int(99999, 999999),
                'settlements' => [
                    [
                        'type' => Random::value(SettlementType::getValidValues()),
                        'amount' => [
                            'value' => round(Random::float(0.1, 99.99), 2),
                            'currency' => Random::value(CurrencyCode::getValidValues()),
                        ],
                    ],
                ],
                $type . '_id' => uniqid('', true),
            ];
            $result[] = [$request];
        }

        return $result;
    }

    public function invalidAmountDataProvider()
    {
        return [
            [-1],
            [true],
            [false],
            [new stdClass()],
            [0],
        ];
    }

    public static function invalidCustomerDataProvider()
    {
        return [
            [true],
            [false],
            [Random::str(1, 100)],
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

    public static function invalidTypeDataProvider()
    {
        return [
            [true],
            [false],
            [0],
            [7],
            [Random::int(-100, -1)],
            [Random::int(7, 100)],
        ];
    }

    public static function invalidBooleanDataProvider()
    {
        return [
            ['test'],
        ];
    }

    public static function invalidAdditionalUserPropsDataProvider()
    {
        return [
            [new stdClass()],
            ['test'],
            [[
                'name' => Random::str(AdditionalUserProps::NAME_MAX_LENGTH + 1),
                'value' => Random::str(1, AdditionalUserProps::VALUE_MAX_LENGTH),
            ]],
            [[
                'name' => Random::str(1, AdditionalUserProps::NAME_MAX_LENGTH),
                'value' => Random::str(AdditionalUserProps::VALUE_MAX_LENGTH + 1),
            ]],
        ];
    }

    public static function invalidReceiptOperationalDetailsDataProvider()
    {
        return [
            [new stdClass()],
            [true],
            [Random::str(1, 100)],
        ];
    }

    public static function invalidReceiptIndustryDetailsDataProvider()
    {
        return [
            [new stdClass()],
            [true],
            [Random::str(1, 100)],
        ];
    }
}
