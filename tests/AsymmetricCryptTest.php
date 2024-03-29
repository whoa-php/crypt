<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
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
use Whoa\Crypt\PrivateKeyAsymmetricDecrypt;
use Whoa\Crypt\PrivateKeyAsymmetricEncrypt;
use Whoa\Crypt\PublicKeyAsymmetricDecrypt;
use Whoa\Crypt\PublicKeyAsymmetricEncrypt;
use PHPUnit\Framework\TestCase;

/**
 * @package Whoa\Tests\Crypt
 */
class AsymmetricCryptTest extends TestCase
{
    /** Path to key file */
    public const PRIVATE_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'sample_private_key.pem';

    /** Path to key file */
    public const PUBLIC_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'sample_public_key.pem';

    /**
     * Test encrypt & decrypt.
     * @throws Exception
     */
    public function testEncryptDecrypt1(): void
    {
        $encrypt = new PublicKeyAsymmetricEncrypt(file_get_contents(self::PUBLIC_FILE));
        $decrypt = new PrivateKeyAsymmetricDecrypt('file://' . self::PRIVATE_FILE);

        $input = str_repeat('Hello world' . PHP_EOL, 1000);

        $encrypted = $encrypt->encrypt($input);
        $this->assertNotEmpty($encrypted);
        $decrypted = $decrypt->decrypt($encrypted);
        $this->assertEquals($input, $decrypted);
    }

    /**
     * Test encrypt & decrypt.
     * @throws Exception
     */
    public function testEncryptDecrypt2(): void
    {
        $decrypt = new PublicKeyAsymmetricDecrypt(file_get_contents(self::PUBLIC_FILE));
        $encrypt = new PrivateKeyAsymmetricEncrypt('file://' . self::PRIVATE_FILE);

        $input = str_repeat('Hello world' . PHP_EOL, 1000);

        $encrypted = $encrypt->encrypt($input);
        $this->assertNotEmpty($encrypted);
        $decrypted = $decrypt->decrypt($encrypted);
        $this->assertEquals($input, $decrypted);
    }

    /**
     * Test encrypt & decrypt.
     * @throws Exception
     */
    public function testEncryptDecrypt3(): void
    {
        $encrypt = new PublicKeyAsymmetricEncrypt(file_get_contents(self::PUBLIC_FILE));
        $decrypt = new PrivateKeyAsymmetricDecrypt('file://' . self::PRIVATE_FILE);

        $input = '';

        $encrypted = $encrypt->encrypt($input);
        $this->assertNotEmpty($encrypted);
        $decrypted = $decrypt->decrypt($encrypted);
        $this->assertEquals($input, $decrypted);
    }
}
