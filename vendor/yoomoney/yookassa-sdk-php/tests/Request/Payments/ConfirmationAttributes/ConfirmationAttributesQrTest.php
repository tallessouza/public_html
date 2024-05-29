<?php

namespace Tests\YooKassa\Request\Payments\ConfirmationAttributes;

use YooKassa\Model\Payment\ConfirmationType;
use YooKassa\Request\Payments\ConfirmationAttributes\ConfirmationAttributesQr;

/**
 * @internal
 */
class ConfirmationAttributesQrTest extends AbstractTestConfirmationAttributes
{
    /**
     * @dataProvider validUrlDataProvider
     *
     * @param mixed $value
     */
    public function testGetSetReturnUrl($value): void
    {
        $instance = $this->getTestInstance();

        $instance->setReturnUrl($value);
        if (null === $value || '' === $value) {
            self::assertNull($instance->getReturnUrl());
            self::assertNull($instance->returnUrl);
            self::assertNull($instance->return_url);
        } else {
            self::assertEquals($value, $instance->getReturnUrl());
            self::assertEquals($value, $instance->returnUrl);
            self::assertEquals($value, $instance->return_url);
        }

        $instance->returnUrl = $value;
        if (null === $value || '' === $value) {
            self::assertNull($instance->getReturnUrl());
            self::assertNull($instance->returnUrl);
            self::assertNull($instance->return_url);
        } else {
            self::assertEquals($value, $instance->getReturnUrl());
            self::assertEquals($value, $instance->returnUrl);
            self::assertEquals($value, $instance->return_url);
        }

        $instance->return_url = $value;
        if (null === $value || '' === $value) {
            self::assertNull($instance->getReturnUrl());
            self::assertNull($instance->returnUrl);
            self::assertNull($instance->return_url);
        } else {
            self::assertEquals($value, $instance->getReturnUrl());
            self::assertEquals($value, $instance->returnUrl);
            self::assertEquals($value, $instance->return_url);
        }
    }

    public function validEnforceDataProvider()
    {
        return [
            [true],
            [false],
            [null],
            [''],
            [0],
            [1],
            [100],
        ];
    }

    public function invalidEnforceDataProvider()
    {
        return [
            ['true'],
            ['false'],
            [[]],
            [new stdClass()],
        ];
    }

    public static function validUrlDataProvider()
    {
        return [
            ['https://test.ru'],
        ];
    }

    protected function getTestInstance(): ConfirmationAttributesQr
    {
        return new ConfirmationAttributesQr();
    }

    protected function getExpectedType(): string
    {
        return ConfirmationType::QR;
    }
}
