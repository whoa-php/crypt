<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Copyright 2021 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Whoa\Tests\Crypt;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Contracts\Application\ContainerConfiguratorInterface;
use Whoa\Contracts\Container\ContainerInterface;
use Whoa\Contracts\Settings\Packages\AsymmetricCryptSettingsInterface;
use Whoa\Contracts\Settings\SettingsInterface;
use Whoa\Contracts\Settings\SettingsProviderInterface;
use Whoa\Crypt\Contracts\DecryptInterface;
use Whoa\Crypt\Contracts\EncryptInterface;
use Whoa\Crypt\Contracts\HasherInterface;
use Whoa\Crypt\Exceptions\CryptConfigurationException;
use Whoa\Crypt\Package\AsymmetricCryptSettings;
use Whoa\Crypt\Package\AsymmetricPrivateEncryptPublicDecryptProvider;
use Whoa\Crypt\Package\AsymmetricPublicEncryptPrivateDecryptProvider;
use Whoa\Crypt\Package\HasherProvider;
use Whoa\Crypt\Package\HasherSettings;
use Whoa\Crypt\Package\SymmetricCryptProvider;
use Whoa\Crypt\Package\SymmetricCryptSettings;
use Whoa\Tests\Crypt\Data\TestContainer;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @package Whoa\Tests\Crypt
 */
class PackageTest extends TestCase
{
    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAsymmetricPrivateEncryptPublicDecrypt1(): void
    {
        $container = new TestContainer();

        $this->addAsymmetricCryptSettingsProvider($container);

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = AsymmetricPrivateEncryptPublicDecryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $this->assertNotNull($container->get(EncryptInterface::class));
        $this->assertNotNull($container->get(DecryptInterface::class));
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAsymmetricPrivateEncryptPublicDecrypt2(): void
    {
        $this->expectException(CryptConfigurationException::class);

        $container = new TestContainer();

        $this->addInvalidAsymmetricCryptSettingsProvider($container);

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = AsymmetricPrivateEncryptPublicDecryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $container->get(EncryptInterface::class);
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAsymmetricPrivateEncryptPublicDecrypt3(): void
    {
        $this->expectException(CryptConfigurationException::class);

        $container = new TestContainer();

        $this->addInvalidAsymmetricCryptSettingsProvider($container);

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = AsymmetricPrivateEncryptPublicDecryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $container->get(DecryptInterface::class);
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testPublicEncryptPrivateDecrypt1(): void
    {
        $container = new TestContainer();

        $this->addAsymmetricCryptSettingsProvider($container);

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = AsymmetricPublicEncryptPrivateDecryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $this->assertNotNull($container->get(EncryptInterface::class));
        $this->assertNotNull($container->get(DecryptInterface::class));
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testPublicEncryptPrivateDecrypt2(): void
    {
        $this->expectException(CryptConfigurationException::class);

        $container = new TestContainer();

        $this->addInvalidAsymmetricCryptSettingsProvider($container);

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = AsymmetricPublicEncryptPrivateDecryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $container->get(EncryptInterface::class);
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testPublicEncryptPrivateDecrypt3(): void
    {
        $this->expectException(CryptConfigurationException::class);

        $container = new TestContainer();

        $this->addInvalidAsymmetricCryptSettingsProvider($container);

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = AsymmetricPublicEncryptPrivateDecryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $container->get(DecryptInterface::class);
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSymmetricCrypt(): void
    {
        $container = new TestContainer();

        $settings = new class extends SymmetricCryptSettings {
            /**
             * @inheritdoc
             */
            protected function getSettings(): array
            {
                return [

                        static::KEY_PASSWORD => 'secret',
                        static::KEY_USE_AUTHENTICATION => true,

                    ] + parent::getSettings();
            }
        };
        $appConfig = [];
        $this->addSettings($container, SymmetricCryptSettings::class, $settings->get($appConfig));

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = SymmetricCryptProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $this->assertNotNull($container->get(EncryptInterface::class));
        $this->assertNotNull($container->get(DecryptInterface::class));
    }

    /**
     * Test provider.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testHasher(): void
    {
        $container = new TestContainer();

        /** @var HasherSettings $settings */
        [$settings] = HasherProvider::getSettings();
        $appConfig = [];
        $this->addSettings($container, HasherSettings::class, $settings->get($appConfig));

        /** @var ContainerConfiguratorInterface $configuratorClass */
        [$configuratorClass] = HasherProvider::getContainerConfigurators();
        $configuratorClass::configureContainer($container);

        $this->assertNotNull($container->get(HasherInterface::class));
    }

    /**
     * @param ContainerInterface $container
     * @return PackageTest
     */
    private function addAsymmetricCryptSettingsProvider(ContainerInterface $container): self
    {
        $settings = $this->getAsymmetricSettings($this->getPathToPublicKey(), $this->getPathToPrivateKey());
        $appConfig = [];

        return $this->addSettings($container, AsymmetricCryptSettings::class, $settings->get($appConfig));
    }

    /**
     * @param ContainerInterface $container
     * @return PackageTest
     */
    private function addInvalidAsymmetricCryptSettingsProvider(ContainerInterface $container): self
    {
        return $this->addSettings($container, AsymmetricCryptSettings::class, [
            AsymmetricCryptSettingsInterface::KEY_PUBLIC_PATH_OR_KEY_VALUE => null,
            AsymmetricCryptSettingsInterface::KEY_PRIVATE_PATH_OR_KEY_VALUE => null,
        ]);
    }

    /**
     * @param string $publicValue
     * @param string $privateValue
     * @return SettingsInterface
     */
    private function getAsymmetricSettings(string $publicValue, string $privateValue): SettingsInterface
    {
        return new class ($publicValue, $privateValue) extends AsymmetricCryptSettings {
            /**
             * @var string
             */
            private string $publicValue;

            /**
             * @var string
             */
            private string $privateValue;

            /**
             * @param string $publicValue
             * @param string $privateValue
             */
            public function __construct(string $publicValue, string $privateValue)
            {
                $this->publicValue = $publicValue;
                $this->privateValue = $privateValue;
            }

            /**
             * @inheritdoc
             */
            protected function getSettings(): array
            {
                return [

                        static::KEY_PUBLIC_PATH_OR_KEY_VALUE => $this->publicValue,
                        static::KEY_PRIVATE_PATH_OR_KEY_VALUE => $this->privateValue,

                    ] + parent::getSettings();
            }
        };
    }

    /**
     * @param ContainerInterface $container
     * @param string $settingsClass
     * @param array $settings
     * @return PackageTest
     */
    private function addSettings(ContainerInterface $container, string $settingsClass, array $settings): self
    {
        /** @var Mock $settingsMock */
        $settingsMock = Mockery::mock(SettingsProviderInterface::class);
        $settingsMock->shouldReceive('get')->once()->with($settingsClass)->andReturn($settings);

        $container->offsetSet(SettingsProviderInterface::class, $settingsMock);

        return $this;
    }

    /**
     * @return string
     */
    private function getPathToPublicKey(): string
    {
        return 'file://' . __DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'sample_public_key.pem';
    }

    /**
     * @return string
     */
    private function getPathToPrivateKey(): string
    {
        return 'file://' . __DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'sample_private_key.pem';
    }
}
