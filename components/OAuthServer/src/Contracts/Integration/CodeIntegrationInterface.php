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

use Limoncello\OAuthServer\Contracts\AuthorizationCodeInterface;
use Limoncello\OAuthServer\Contracts\Clients\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Authorization code integration interface for server.
 *
 * @package Limoncello\OAuthServer
 */
interface CodeIntegrationInterface extends IntegrationInterface
{
    /**
     * @param ClientInterface $client
     * @param array|null      $scopes
     *
     * @return array [bool $isScopeValid, string[]|null $scopeList, bool $isScopeModified] Scope list `null` for
     *               invalid, string[] otherwise.
     */
    public function codeValidateScope(ClientInterface $client, array $scopes = null): array;

    /**
     * @param ClientInterface $client
     * @param string|null     $redirectUri
     * @param bool            $isScopeModified
     * @param string[]|null   $scopeList
     * @param string|null     $state
     *
     * @return ResponseInterface
     *
     * @link https://tools.ietf.org/html/rfc6749#section-4.1.2
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function codeCreateAskResourceOwnerForApprovalResponse(
        ClientInterface $client,
        string $redirectUri = null,
        bool $isScopeModified = false,
        array $scopeList = null,
        string $state = null
    ): ResponseInterface;

    /**
     * @param string $code
     *
     * @return AuthorizationCodeInterface|null
     */
    public function codeReadAuthenticationCode(string $code);

    /**
     * Revoke all tokens issued based on the input authorization code.
     *
     * @param AuthorizationCodeInterface $code
     *
     * @return void
     *
     * @link https://tools.ietf.org/html/rfc6749#section-10.5
     */
    public function codeRevokeTokens(AuthorizationCodeInterface $code);

    /**
     * @param AuthorizationCodeInterface $code
     *
     * @return ResponseInterface
     *
     * @link https://tools.ietf.org/html/rfc6749#section-4.1.4
     */
    public function codeCreateAccessTokenResponse(AuthorizationCodeInterface $code): ResponseInterface;

    /**
     * @param string $identifier
     *
     * @return ClientInterface|null
     */
    public function codeReadClient(string $identifier);
}
