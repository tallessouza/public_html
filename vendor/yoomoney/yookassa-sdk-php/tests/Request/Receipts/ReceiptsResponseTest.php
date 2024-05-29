<?php

namespace Tests\YooKassa\Request\Receipts;

use Exception;
use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Helpers\UUID;
use YooKassa\Model\Receipt\ReceiptType;
use YooKassa\Model\Receipt\SettlementType;
use YooKassa\Request\Receipts\ReceiptResponseInterface;
use YooKassa\Request\Receipts\ReceiptsResponse;

/**
 * @internal
 */
class ReceiptsResponseTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     *
     * @throws Exception
     */
    public function testGetType(array $options): void
    {
        $instance = new ReceiptsResponse($options);

        self::assertEquals($options['type'], $instance->getType());
        self::assertEquals($options['next_cursor'], $instance->getNextCursor());
    }

    /**
     * @dataProvider validDataProvider
     *
     * @throws Exception
     */
    public function testGetItems(array $options): void
    {
        $instance = new ReceiptsResponse($options);

        self::assertEquals(count($options['items']), count($instance->getItems()));
        self::assertTrue($instance->hasNextCursor());

        foreach ($instance->getItems() as $index => $item) {
            self::assertInstanceOf(ReceiptResponseInterface::class, $item);
            self::assertArrayHasKey($index, $options['items']);
            self::assertEquals($options['items'][$index]['id'], $item->getId());
            self::assertEquals($options['items'][$index]['type'], $item->getType());
            self::assertEquals($options['items'][$index]['tax_system_code'], $item->getTaxSystemCode());
            self::assertEquals($options['items'][$index]['status'], $item->getStatus());

            self::assertEquals(count($options['items'][$index]['items']), count($item->getItems()));
        }
    }

    public function validDataProvider(): array
    {
        return [
            [
                [
                    'type' => 'list',
                    'items' => $this->generateReceipts(),
                    'next_cursor' => Random::str(36),
                ],
            ],
        ];
    }

    private function generateReceipts(): array
    {
        $return = [];
        $count = Random::int(1, 10);

        for ($i = 0; $i < $count; $i++) {
            $return[] = $this->generateReceipt();
        }

        return $return;
    }

    private function generateReceipt(): array
    {
        $type = Random::value(ReceiptType::getEnabledValues());

        return [
            'id' => Random::str(39),
            'type' => $type,
            'status' => Random::value(['pending', 'succeeded', 'canceled']),
            'items' => $this->generateItems(),
            'settlements' => $this->generateSettlements(),
            'tax_system_code' => Random::int(1, 6),
            $type . '_id' => UUID::v4(),
        ];
    }

    private function generateItems(): array
    {
        $return = [];
        $count = Random::int(1, 10);

        for ($i = 0; $i < $count; $i++) {
            $return[] = $this->generateItem();
        }

        return $return;
    }

    private function generateItem(): array
    {
        return [
            'description' => Random::str(1, 128),
            'amount' => [
                'value' => round(Random::float(1.00, 100.00), 2),
                'currency' => 'RUB',
            ],
            'quantity' => round(Random::float(0.001, 99.999), 3),
            'vat_code' => Random::int(1, 6),
        ];
    }

    private function generateSettlements(): array
    {
        $return = [];
        $count = Random::int(1, 10);

        for ($i = 0; $i < $count; $i++) {
            $return[] = $this->generateSettlement();
        }

        return $return;
    }

    private function generateSettlement(): array
    {
        return [
            'type' => Random::value(SettlementType::getValidValues()),
            'amount' => [
                'value' => round(Random::float(1.00, 100.00), 2),
                'currency' => 'RUB',
            ],
        ];
    }
}
