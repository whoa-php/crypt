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

namespace Whoa\Crypt\Package;

use Whoa\Contracts\Settings\Packages\SymmetricCryptSettingsInterface;

use function assert;

/**
 * @package Whoa\Crypt
 */
class SymmetricCryptSettings implements SymmetricCryptSettingsInterface
{
    /**
     * @inheritdoc
     */
    final public function get(array $appConfig): array
    {
        $defaults = $this->getSettings();

        $password = $defaults[static::KEY_PASSWORD] ?? null;
        assert(empty($password) === false, "Password cannot be empty.");

        return $defaults;
    }

    /**
     * @return array
     */
    protected function getSettings(): array
    {
        return [
            static::KEY_METHOD => static::DEFAULT_METHOD,
            static::KEY_IV => static::DEFAULT_IV,
            static::KEY_USE_ZERO_PADDING => false,
            static::KEY_USE_AUTHENTICATION => false,
            static::KEY_TAG_LENGTH => 16,
        ];
    }
}
