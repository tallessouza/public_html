<?php

namespace Tests\YooKassa\Model\Settings;

use Exception;
use PHPUnit\Framework\TestCase;
use YooKassa\Model\Settings\FiscalizationProvider;
use YooKassa\Model\Settings\FiscalizationData;
use YooKassa\Helpers\Random;

/**
 * @internal
 */
class FiscalizationDataTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetProvider(array $options): void
    {
        $instance = new FiscalizationData();

        $instance->setProvider($options['provider']);
        self::assertEquals($options['provider'], $instance->getProvider());
        self::assertEquals($options['provider'], $instance->provider);

        $instance = new FiscalizationData();
        $instance->provider = $options['provider'];
        self::assertEquals($options['provider'], $instance->getProvider());
        self::assertEquals($options['provider'], $instance->provider);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetEnabled(array $options): void
    {
        $instance = new FiscalizationData();

        $instance->setEnabled($options['enabled']);
        self::assertEquals($options['enabled'], $instance->getEnabled());
        self::assertEquals($options['enabled'], $instance->enabled);

        $instance = new FiscalizationData();
        $instance->enabled = $options['enabled'];
        self::assertEquals($options['enabled'], $instance->getEnabled());
        self::assertEquals($options['enabled'], $instance->enabled);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetSetFiscalization(array $options): void
    {
        $instance = new FiscalizationData($options);

        self::assertEquals($options, $instance->toArray());
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
                'provider' => Random::value(FiscalizationProvider::getValidValues()),
                'enabled' => Random::bool(),
            ];
            $result[] = [$item];
        }

        return $result;
    }

}
