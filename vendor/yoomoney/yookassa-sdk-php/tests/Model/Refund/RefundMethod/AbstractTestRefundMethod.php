<?php

namespace Tests\YooKassa\Model\Refund\RefundMethod;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use YooKassa\Helpers\Random;
use YooKassa\Model\Refund\RefundMethod\AbstractRefundMethod;

abstract class AbstractTestRefundMethod extends TestCase
{
    public function testGetType(): void
    {
        $instance = $this->getTestInstance();
        self::assertEquals($this->getExpectedType(), $instance->getType());
    }

    /**
     * @dataProvider invalidTypeDataProvider
     */
    public function testInvalidType(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TestRefundData($value);
    }

    public static function invalidTypeDataProvider()
    {
        return [
            [''],
            [null],
            [Random::str(40)],
            [0],
        ];
    }

    abstract protected function getTestInstance(): AbstractRefundMethod;

    abstract protected function getExpectedType(): string;
}

class TestRefundData extends AbstractRefundMethod
{
    public function __construct($type)
    {
        parent::__construct([]);
        $this->setType($type);
    }
}
