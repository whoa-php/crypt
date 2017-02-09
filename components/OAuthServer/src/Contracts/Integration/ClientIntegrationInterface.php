<?php namespace Limoncello\OAuthServer\Contracts\Integration;

/**
 * Copyright 2015-2017 info@neomerx.com
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

use Limoncello\OAuthServer\Contracts\Clients\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Client credentials integration interface for server.
 *
 * @package Limoncello\OAuthServer
 */
interface ClientIntegrationInterface extends IntegrationInterface
{
    /**
     * @param ClientInterface $client
     * @param array|null      $scopes
     *
     * @return array [bool $isScopeValid, string[]|null $scopeList, bool $isScopeModified] Scope list `null` for
     *               invalid, string[] otherwise.
     */
    public function clientValidateScope(ClientInterface $client, array $scopes = null): array;

    /**
     * Create access token response.
     *
     * @param ClientInterface $client
     * @param bool            $isScopeModified
     * @param array|null      $scope
     *
     * @return ResponseInterface
     *
     * @link https://tools.ietf.org/html/rfc6749#section-4.4.3
     */
    public function clientCreateAccessTokenResponse(
        ClientInterface $client,
        bool $isScopeModified,
        array $scope = null
    ): ResponseInterface;
}
