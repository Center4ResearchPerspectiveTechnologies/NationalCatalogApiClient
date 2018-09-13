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
 * Class contains response from Feed Api.
 */
class FeedResponse extends ApiResponse
{
    /**
     * Identifier of saved feed
     * @var int|null
     */
    private $feed_id = null;


    public function __construct(array $data)
    {
        foreach($data as $key => $val) {
            $this->{$key} = $val;
        }
    }
    /**
     * Method for reseive feed identifier
     * Contains only feed ID integer
     *
     * @return int|null
     */
    public function getFeedId(): ?int
    {
        return $this->feed_id;
    }
}