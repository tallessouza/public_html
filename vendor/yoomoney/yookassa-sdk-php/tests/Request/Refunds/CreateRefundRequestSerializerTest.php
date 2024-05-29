<?php

namespace Tests\YooKassa\Request\Refunds;

use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\Deal\SettlementPayoutPaymentType;
use YooKassa\Model\MonetaryAmount;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;
use YooKassa\Model\Refund\Source;
use YooKassa\Request\Refunds\CreateRefundRequest;
use YooKassa\Request\Refunds\CreateRefundRequestSerializer;

/**
 * @internal
 */
class CreateRefundRequestSerializerTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     *
     * @param mixed $options
     */
    public function testSerialize($options): void
    {
        $serializer = new CreateRefundRequestSerializer();
        $data = $serializer->serialize(CreateRefundRequest::builder()->build($options));

        $expected = [
            'payment_id' => $options['paymentId'],
            'amount' => $options['amount'],
        ];
        if (!empty($options['description'])) {
            $expected['description'] = $options['description'];
        }
        if (!empty($options['deal'])) {
            $expected['deal'] = $options['deal'];
        }

        if (!empty($options['receiptItems'])) {
            foreach ($options['receiptItems'] as $item) {
                $itemData = [
                    'description' => $item['description'],
                    'quantity' => empty($item['quantity']) ? 1 : $item['quantity'],
                    'amount' => $options['amount'],
                    'vat_code' => $item['vatCode'],
                ];

                if (!empty($item['payment_mode'])) {
                    $itemData['payment_mode'] = $item['payment_mode'];
                }

                if (!empty($item['payment_subject'])) {
                    $itemData['payment_subject'] = $item['payment_subject'];
                }

                $expected['receipt']['items'][] = $itemData;
            }
        }

        if (!empty($options['sources'])) {
            foreach ($options['sources'] as $item) {
                $expected['sources'][] = [
                    'account_id' => $item['account_id'],
                    'amount' => [
                        'value' => $item['amount']['value'],
                        'currency' => $item['amount']['currency'] ?? CurrencyCode::RUB,
                    ],
                ];
            }
        }

        if (!empty($options['receipt'])) {
            $expected['receipt'] = $options['receipt'];
        }

        self::assertEquals($expected, $data);
    }

    public function validDataProvider()
    {
        $result = [
            [
                [
                    'paymentId' => $this->randomString(36),
                    'amount' => [
                        'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                        'currency' => Random::value(CurrencyCode::getValidValues()),
                    ],
                    'description' => null,
                    'deal' => null,
                    'receipt' => [
                        'items' => [
                            [
                                'description' => 'test',
                                'quantity' => Random::int(1, 100),
                                'amount' => [
                                    'value' => Random::int(1, 1000000),
                                    'currency' => Random::value(CurrencyCode::getValidValues()),
                                ],
                                'vat_code' => Random::int(1, 6),
                            ],
                        ],
                        'customer' => [
                            'phone' => Random::str(10, '0123456789'),
                        ],
                        'tax_system_code' => Random::int(1, 6),
                    ],
                    'sources' => [
                        new Source([
                            'account_id' => Random::str(36),
                            'amount' => [
                                'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                        ]),
                    ],
                ],
            ],
            [
                [
                    'paymentId' => $this->randomString(36),
                    'amount' => [
                        'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                        'currency' => Random::value(CurrencyCode::getValidValues()),
                    ],
                    'description' => '',
                    'deal' => [
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
                    'receipt' => [
                        'items' => [
                            [
                                'description' => 'test',
                                'quantity' => Random::int(1, 100),
                                'amount' => [
                                    'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                                    'currency' => Random::value(CurrencyCode::getValidValues()),
                                ],
                                'vat_code' => Random::int(1, 6),
                            ],
                        ],
                        'customer' => [
                            'email' => 'johndoe@yoomoney.ru',
                        ],
                        'tax_system_code' => Random::int(1, 6),
                    ],
                    'sources' => [
                        new Source([
                            'account_id' => Random::str(36),
                            'amount' => [
                                'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                        ]),
                    ],
                ],
            ],
        ];
        for ($i = 0; $i < 10; $i++) {
            $request = [
                'paymentId' => $this->randomString(36),
                'amount' => [
                    'value' => Random::int(1, 1000000),
                    'currency' => Random::value(CurrencyCode::getValidValues()),
                ],
                'description' => uniqid('', true),
                'deal' => [
                    'refund_settlements' => [
                        [
                            'type' => SettlementPayoutPaymentType::PAYOUT,
                            'amount' => [
                                'value' => 123.00,
                                'currency' => 'RUB',
                            ],
                        ],
                    ],
                ],
                'receipt' => [
                    'items' => [
                        [
                            'description' => 'test',
                            'quantity' => Random::int(1, 100),
                            'amount' => [
                                'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                                'currency' => Random::value(CurrencyCode::getValidValues()),
                            ],
                            'vat_code' => Random::int(1, 6),
                            'payment_mode' => Random::value(PaymentMode::getValidValues()),
                            'payment_subject' => Random::value(PaymentSubject::getValidValues()),
                        ],
                    ],
                    'customer' => [
                        'phone' => Random::str(10, '0123456789'),
                    ],
                    'tax_system_code' => Random::int(1, 6),
                ],
                'sources' => [
                    new Source([
                        'account_id' => Random::str(36),
                        'amount' => new MonetaryAmount(Random::int(1, 1000), 'RUB'),
                    ]),
                ],
            ];
            $result[] = [$request];
        }

        return $result;
    }

    private function randomString($length, $any = true): string
    {
        static $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-+_.';

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            if ($any) {
                $char = chr(Random::int(32, 126));
            } else {
                $rnd = Random::int(0, strlen($chars) - 1);
                $char = $chars[$rnd];
            }
            $result .= $char;
        }

        return $result;
    }

    private function getReceipt($count): array
    {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = [
                'description' => Random::str(10),
                'quantity' => Random::float(1, 100),
                'amount' => [
                    'value' => sprintf('%.2f', round(Random::float(0.1, 99.99), 2)),
                    'currency' => Random::value(CurrencyCode::getValidValues()),
                ],
                'vatCode' => Random::int(1, 6),
                'paymentMode' => Random::value(PaymentMode::getValidValues()),
                'paymentSubject' => Random::value(PaymentSubject::getValidValues()),
            ];
        }

        return $result;
    }
}
