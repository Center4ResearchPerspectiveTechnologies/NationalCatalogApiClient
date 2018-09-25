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
 * Interface for annotation response from API.
 */
interface IApiResponse
{
    /**
     * Method for reseive result from API response
     * Contains all information exept API Version
     *
     * @return array|null
     */
    public function getResult();

    /**
     * Method for set result reseived from API response
     *
     * @param array $result
     * @return ApiResponse
     */
    public function setResult(array $result);

    /**
     * Method for reseive API Version from response
     * Contains only information about version
     *
     * @return int|null
     */
    public function getApiVersion();

    /**
     * Method for set API Version reseived from response
     *
     * @param int $apiVersion
     * @return ApiResponse
     */
    public function setApiVersion(int $apiVersion);
}
