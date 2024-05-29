<?php

namespace Tests\YooKassa\Model\Refund\RefundMethod;

use TypeError;
use YooKassa\Helpers\Random;
use YooKassa\Model\Refund\RefundMethod\RefundMethodSbp;
use YooKassa\Model\Refund\RefundMethodType;

/**
 * @internal
 */
class RefundMethodSbpTest extends AbstractTestRefundMethod
{
    protected function getTestInstance(): RefundMethodSbp
    {
        return new RefundMethodSbp();
    }

    protected function getExpectedType(): string
    {
        return RefundMethodType::SBP;
    }

    /**
     * @dataProvider validLoanOptionDataProvider
     *
     * @param mixed $value
     */
    public function testGetSetLoanOption(mixed $value): void
    {
        $instance = $this->getTestInstance();

        $instance->setSbpOperationId($value);
        self::assertEquals($value, $instance->getSbpOperationId());
        self::assertEquals($value, $instance->sbp_operation_id);

        $instance = $this->getTestInstance();
        $instance->sbp_operation_id = $value;
        self::assertEquals($value, $instance->getSbpOperationId());
        self::assertEquals($value, $instance->sbp_operation_id);
    }

    /**
     * @dataProvider invalidLoanOptionDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidLoanOption(mixed $value): void
    {
        $this->expectException(TypeError::class);

        $instance = $this->getTestInstance();
        $instance->setSbpOperationId($value);
    }

    /**
     * @dataProvider invalidLoanOptionDataProvider
     *
     * @param mixed $value
     */
    public function testSetterInvalidLoanOption(mixed $value): void
    {
        $this->expectException(TypeError::class);

        $instance = $this->getTestInstance();
        $instance->sbpOperationId = $value;
    }

    public function validLoanOptionDataProvider(): array
    {
        return [
            [null],
            [''],
            [Random::str(5)],
            [Random::str(10)],
            [Random::str(20)],
        ];
    }

    public function invalidLoanOptionDataProvider(): array
    {
        return [
            [[]],
            [new \ArrayObject()],
            [new \stdClass()],
        ];
    }

}
