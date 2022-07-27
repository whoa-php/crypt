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

use Whoa\Crypt\Contracts\DecryptInterface;
use Whoa\Crypt\Exceptions\CryptException;

use function openssl_private_decrypt;

/**
 * @package Whoa\Crypt
 */
class PrivateKeyAsymmetricDecrypt extends BasePrivateKeyAsymmetricCrypt implements DecryptInterface
{
    /**
     * @inheritdoc
     */
    public function decrypt(string $data): string
    {
        $result = null;
        $decryptChunkSize = $this->getDecryptChunkSize();
        if ($decryptChunkSize !== null) {
            $key = $this->getKey();
            $this->clearErrors();
            foreach ($this->chunkString($data, $decryptChunkSize) as $chunk) {
                $retVal = openssl_private_decrypt($chunk, $decrypted, $key);
                $retVal === true ?: $this->throwException(new CryptException($this->getErrorMessage()));
                $result .= $decrypted;
            }
        }

        return $result;
    }
}
