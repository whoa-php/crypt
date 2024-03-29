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

namespace Whoa\Crypt;

use Generator;
use Whoa\Crypt\Exceptions\CryptException;

use function assert;
use function file_exists;
use function openssl_pkey_free;
use function openssl_pkey_get_details;
use function strlen;
use function substr;

/**
 * @package Whoa\Crypt
 */
abstract class BaseAsymmetricCrypt extends BaseCrypt
{
    /**
     * @var resource|null
     */
    private $key = null;

    /**
     * @var int|null
     */
    private ?int $keyBytes = null;

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->closeKey();
    }

    /**
     * @return self
     */
    public function closeKey(): self
    {
        if ($this->key !== null) {
            openssl_pkey_free($this->key);
            $this->key = null;
            $this->keyBytes = null;
        }

        return $this;
    }

    /**
     * @return resource|null
     */
    protected function getKey()
    {
        return $this->key;
    }

    /**
     * @param resource $key
     *
     * @return self
     */
    protected function setKey($key): self
    {
        assert(is_resource($key) === true);

        $this->closeKey();
        $this->key = $key;

        return $this;
    }

    /**
     * @return int|null
     */
    protected function getKeyBytes(): ?int
    {
        if ($this->keyBytes === null && $this->getKey() !== null) {
            $this->clearErrors();
            $details = openssl_pkey_get_details($this->getKey());
            $details !== false ?: $this->throwException(new CryptException($this->getErrorMessage()));
            $this->keyBytes = $details['bits'] / 8;
        }

        return $this->keyBytes;
    }

    /**
     * @return int|null
     */
    protected function getEncryptChunkSize(): ?int
    {
        $keyBytes = $this->getKeyBytes();

        // 11 is a kind of magic number related to padding.
        return $keyBytes === null ? null : $keyBytes - 11;
    }

    /**
     * @return int|null
     */
    protected function getDecryptChunkSize(): ?int
    {
        $keyBytes = $this->getKeyBytes();
        return $keyBytes === null ? null : $keyBytes;
    }

    /**
     * @param string $value
     * @param int $maxSize
     * @return Generator
     */
    protected function chunkString(string $value, int $maxSize): Generator
    {
        $isValidInput = $maxSize > 0;

        assert($isValidInput === true);

        if ($isValidInput === true) {
            $start = 0;
            $length = strlen($value);
            if ($length === 0) {
                yield $value;
            }
            while ($start < $length) {
                yield substr($value, $start, $maxSize);
                $start += $maxSize;
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function checkIfPathToFileCheckPrefix(string $path): bool
    {
        if (file_exists($path) === true) {
            return substr($path, 0, 7) === 'file://';
        }

        return true;
    }
}
