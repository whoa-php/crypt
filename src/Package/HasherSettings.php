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

use Whoa\Contracts\Settings\Packages\HasherSettingsInterface;

/**
 * @package Whoa\Crypt
 */
class HasherSettings implements HasherSettingsInterface
{
    /**
     * @inheritdoc
     */
    final public function get(array $appConfig): array
    {
        return $this->getSettings();
    }

    /**
     * @return array
     */
    protected function getSettings(): array
    {
        return [
            /** @see http://php.net/manual/en/password.constants.php */
            static::KEY_ALGORITHM => PASSWORD_DEFAULT,
            /** @see http://php.net/manual/en/function.password-hash.php */
            static::KEY_COST => 10,
        ];
    }
}
