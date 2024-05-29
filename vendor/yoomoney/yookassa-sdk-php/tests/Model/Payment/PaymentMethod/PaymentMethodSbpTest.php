<?php

namespace Tests\YooKassa\Model\Payment\PaymentMethod;

use TypeError;
use YooKassa\Helpers\Random;
use YooKassa\Model\Payment\PaymentMethod\PaymentMethodSbp;
use YooKassa\Model\Payment\PaymentMethodType;

/**
 * @internal
 */
class PaymentMethodSbpTest extends AbstractTestPaymentMethod
{
    protected function getTestInstance(): PaymentMethodSbp
    {
        return new PaymentMethodSbp();
    }

    protected function getExpectedType(): string
    {
        return PaymentMethodType::SBP;
    }

    /**
     * @dataProvider validSbpOperationIdDataProvider
     *
     * @param mixed $value
     */
    public function testGetSetSbpOperationId(mixed $value): void
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
     * @dataProvider invalidSbpOperationIdDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidSbpOperationId(mixed $value): void
    {
        $this->expectException(TypeError::class);

        $instance = $this->getTestInstance();
        $instance->setSbpOperationId($value);
    }

    /**
     * @dataProvider invalidSbpOperationIdDataProvider
     *
     * @param mixed $value
     */
    public function testSetterInvalidSbpOperationId(mixed $value): void
    {
        $this->expectException(TypeError::class);

        $instance = $this->getTestInstance();
        $instance->sbpOperationId = $value;
    }

    public function validSbpOperationIdDataProvider(): array
    {
        return [
            [null],
            [''],
            [Random::str(5)],
            [Random::str(10)],
            [Random::str(20)],
        ];
    }

    public function invalidSbpOperationIdDataProvider(): array
    {
        return [
            [[]],
            [new \ArrayObject()],
            [new \stdClass()],
        ];
    }

}
