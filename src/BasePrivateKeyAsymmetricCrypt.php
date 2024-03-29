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

use Whoa\Crypt\Exceptions\CryptException;

use function assert;
use function openssl_pkey_get_private;

/**
 * @package Whoa\Crypt
 */
abstract class BasePrivateKeyAsymmetricCrypt extends BaseAsymmetricCrypt
{
    /**
     * @param string $privateKeyOrPath Could be either private key or path to file prefixed with 'file://'.
     */
    public function __construct(string $privateKeyOrPath)
    {
        assert(
            $this->checkIfPathToFileCheckPrefix($privateKeyOrPath),
            'It seems you try to use path to file. If so you should prefix it with \'file://\'.'
        );

        $this->clearErrors();

        $privateKey = openssl_pkey_get_private($privateKeyOrPath);
        $privateKey !== false ?: $this->throwException(new CryptException($this->getErrorMessage()));

        $this->setKey($privateKey);
    }
}
