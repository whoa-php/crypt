<?php namespace Limoncello\OAuthServer\GrantTraits;

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

use Limoncello\OAuthServer\Contracts\ClientInterface;
use Limoncello\OAuthServer\Contracts\Integration\RefreshIntegrationInterface;
use Limoncello\OAuthServer\Exceptions\OAuthTokenBodyException;
use Psr\Http\Message\ResponseInterface;

/**
 * @package Limoncello\OAuthServer
 *
 * @link https://tools.ietf.org/html/rfc6749#section-6
 */
trait RefreshGrantTrait
{
    /**
     * @var RefreshIntegrationInterface
     */
    private $refreshIntegration;

    /**
     * @return RefreshIntegrationInterface
     */
    protected function refreshGetIntegration()
    {
        return $this->refreshIntegration;
    }

    /**
     * @param RefreshIntegrationInterface $refreshIntegration
     *
     * @return void
     */
    public function refreshSetIntegration(RefreshIntegrationInterface $refreshIntegration)
    {
        $this->refreshIntegration = $refreshIntegration;
    }

    /**
     * @param string[] $parameters
     *
     * @return string[]|null
     */
    protected function refreshGetValue(array $parameters)
    {
        return $this->refreshReadStringValue($parameters, 'refresh_token');
    }

    /**
     * @param string[] $parameters
     *
     * @return string[]|null
     */
    protected function refreshGetScope(array $parameters)
    {
        return ($scope = $this->refreshReadStringValue($parameters, 'scope')) !== null ? explode(' ', $scope) : null;
    }

    /**
     * @param string[]        $parameters
     * @param ClientInterface $determinedClient
     *
     * @return ResponseInterface
     */
    protected function refreshIssueToken(array $parameters, ClientInterface $determinedClient): ResponseInterface
    {
        if ($determinedClient->isRefreshGrantEnabled() === false) {
            throw new OAuthTokenBodyException(OAuthTokenBodyException::ERROR_UNAUTHORIZED_CLIENT);
        }

        if (($refreshValue = $this->refreshGetValue($parameters)) === null) {
            throw new OAuthTokenBodyException(OAuthTokenBodyException::ERROR_INVALID_REQUEST);
        }

        if (($currentScope = $this->refreshGetIntegration()->readScopeByRefreshValue($refreshValue)) === null) {
            throw new OAuthTokenBodyException(OAuthTokenBodyException::ERROR_INVALID_GRANT);
        }

        if (($requestedScope = $this->refreshGetScope($parameters)) !== null) {
            // check requested scope is within the current one
            if (empty(array_diff($requestedScope, $currentScope)) === false) {
                throw new OAuthTokenBodyException(OAuthTokenBodyException::ERROR_INVALID_SCOPE);
            }
            $isScopeModified = true;
            $scopeList       = $requestedScope;
        } else {
            $isScopeModified = false;
            $scopeList       = null;
        }

        $response = $this->refreshGetIntegration()->refreshCreateAccessTokenResponse(
            $determinedClient,
            $refreshValue,
            $isScopeModified,
            $scopeList,
            $parameters
        );

        return $response;
    }

    /**
     * @param array  $parameters
     * @param string $name
     *
     * @return null|string
     */
    private function refreshReadStringValue(array $parameters, string $name)
    {
        return array_key_exists($name, $parameters) === true && is_string($value = $parameters[$name]) === true ?
            $value : null;
    }
}