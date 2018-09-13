<?php

/************************************************************************
 *
 * Copyright 2018 Center for Research in Perspective Technologies, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 ************************************************************************/

namespace NationalCatalogApi\Responses;

/**
 * Class contains base methods for response
 * from National Catalogue Api.
 */
class ApiResponse implements IApiResponse
{
    const RESPONSE_ATTRIBUTE = 'NationalCatalogApi\Responses\AttributeResponse';
    const RESPONSE_BRANDS = 'NationalCatalogApi\Responses\BrandsResponse';
    const RESPONSE_CATEGORIES = 'NationalCatalogApi\Responses\CategoriesResponse';
    const RESPONSE_LOCATIONS = 'NationalCatalogApi\Responses\LocationsResponse';
    const RESPONSE_PARTIES = 'NationalCatalogApi\Responses\PartiesResponse';
    const RESPONSE_PRODUCTS = 'NationalCatalogApi\Responses\ProductResponse';
    const RESPONSE_ETAGS_LIST = 'NationalCatalogApi\Responses\EtagslistResponse';
    const RESPONSE_SUGGESTIONS = 'NationalCatalogApi\Responses\SuggestionsResponse';
    const RESPONSE_ADD_REVIEW = 'NationalCatalogApi\Responses\AddreviewResponse';
    const RESPONSE_FEED = 'NationalCatalogApi\Responses\FeedResponse';
    const RESPONSE_FEED_STATUS = 'NationalCatalogApi\Responses\FeedStatusResponse';

    /**
     * Result from API response
     * @var array|null
     */
    private $result = null;

    /**
     * Version of API
     * @var int|null
     */
    private $apiVersion = null;

    /**
     * {@inheritDoc}
     */
    public function getResult(): ?array
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function setResult(array $result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion(): ?int
    {
        return $this->apiVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function setApiVersion(int $apiVersion): self
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }
}