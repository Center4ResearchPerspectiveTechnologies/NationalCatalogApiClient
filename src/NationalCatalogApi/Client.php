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

namespace NationalCatalogApi;

use NationalCatalogApi\Responses\{ApiResponse};

/**
 * Class Client
 *
 * @property string HeaderResponseCode
 * @property string HeaderETag
 * @property string HeaderAPIUsageLimit
 * @property string HeaderRetryAfter
 *
 * @package NationalCatalogApi
 */
final class Client
{
    const API_URL = 'https://апи.национальный-каталог.рф';
    const VERSION = 'v3';

    const RESPONSE_FORMAT_JSON = 'json';
    const RESPONSE_FORMAT_XML = 'xml';

    const REQUEST_ENTITY_ATTRIBUTES = 'attributes';
    const REQUEST_ENTITY_BRANDS = 'brands';
    const REQUEST_ENTITY_CATEGORIES = 'categories';
    const REQUEST_ENTITY_LOCATIONS = 'locations';
    const REQUEST_ENTITY_PARTIES = 'parties';
    const REQUEST_ENTITY_PRODUCTS = 'product';
    const REQUEST_ENTITY_ETAGS_LIST = 'etagslist';
    const REQUEST_ENTITY_SUGGESTIONS = 'suggestions';
    const REQUEST_ENTITY_ADD_REVIEW = 'addreview';
    const REQUEST_ENTITY_FEED = 'feed';
    const REQUEST_ENTITY_FEED_STATUS = 'feed-status';

    const CODE_STATUS_OK = 200;
    const CODE_STATUS_NOT_MODIFIED = 304;
    const CODE_STATUS_REQUEST_ERROR = 400;
    const CODE_STATUS_NOT_AUTHORIZED = 401;
    const CODE_STATUS_NO_ACCESS = 403;
    const CODE_STATUS_NO_DATA_FOUND = 404;
    const CODE_STATUS_REQUEST_ENTITY_TO_LARGE = 413;
    const CODE_STATUS_REQUEST_LIMIT_REACHED = 429;
    const CODE_STATUS_INTERNAL_SERVER_ERROR = 500;
    const CODE_STATUS_METHOD_NOT_FOUND = 501;
    const CODE_STATUS_SERVICE_NOT_AVAILABLE = 503;

    const ATTRIBUTE_TYPE_ALL = 'a';
    const ATTRIBUTE_TYPE_MANDATORY = 'm';
    const ATTRIBUTE_TYPE_RECOMMEND = 'r';
    const ATTRIBUTE_TYPE_OPTIONAL = 'o';

    const SOCIAL_TYPE_GOOGLE_PLUS = 'gp';
    const SOCIAL_TYPE_FACEBOOK = 'fb';
    const SOCIAL_TYPE_TWITTER = 'tw';
    const SOCIAL_TYPE_VK = 'vk';

    protected $apiKey;
    protected $supplierKey;
    protected $apiUrl;
    protected $format;
    /** @var string */
    private $_error;
    /** @var array */
    private $_headers;

    /**
     * @param string $apiKey
     * @param string|null $supplierKey
     */
    public function __construct(string $apiKey, ?string $supplierKey = null)
    {
        $this->apiUrl = self::API_URL;
        $this->auth($apiKey, $supplierKey);
        $this->format = self::RESPONSE_FORMAT_JSON;
        $this->_error = null;
        $this->_headers = null;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        if (0 === strpos($property, 'Header') && array_key_exists($header = str_replace('Header', '', $property),
                $this->_headers)) {
            return $this->_headers[$header];
        }
        return null;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->apiUrl = $url;
    }

    /**
     * @param string $apiKey
     * @param string|null $supplierKey
     */
    public function auth(string $apiKey, ?string $supplierKey = null): void
    {
        $this->apiKey = $apiKey;
        $this->supplierKey = $supplierKey;
    }

    /**
     * @param string $format
     * @throws \Throwable
     */
    public function setFormat($format): void
    {
        if (in_array($format, [self::RESPONSE_FORMAT_JSON, self::RESPONSE_FORMAT_XML])) {
            $this->format = $format;
        } else {
            throw new \Exception("Format is not supported");
        }
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return null !== $this->_error;
    }

    /**
     * Return last error
     * @return null|string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Return last HTTP Code
     * @return null|string
     */
    public function getHttpCode()
    {
        return $this->HeaderResponseCode;
    }

    /**
     * Return last ETag
     * @return null|string
     */
    public function getLastETag()
    {
        return $this->HeaderETag;
    }

    /**
     * Return current usage count requests
     * @return null|string
     */
    public function getCurrentUsageCount()
    {
        if (null !== $this->HeaderAPIUsageLimit && false !== strpos($this->HeaderAPIUsageLimit, '/')) {
            return explode('/', $this->HeaderAPIUsageLimit)[0];
        }
        return null;
    }

    /**
     * Return requests limit
     * @return null|string
     */
    public function getUsageLimit()
    {
        if (null !== $this->HeaderAPIUsageLimit && false !== strpos($this->HeaderAPIUsageLimit, '/')) {
            return explode('/', $this->HeaderAPIUsageLimit)[1];
        }
        return $this->HeaderAPIUsageLimit;
    }

    /**
     * @return null|string
     */
    public function getRetryAfter()
    {
        return $this->HeaderRetryAfter;
    }

    /**
     * Send request
     *
     * @param string $url
     * @param mixed $params
     * @param array $headers
     * @return bool|string Return the result on success, FALSE on failure
     */
    private function sendRequest(string $url, $params = [], array $headers = [])
    {

        $this->_headers = null;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (count($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($curl);
        if (false === $response) {
            $this->_error = 'Error (' . curl_errno($curl) . '): ' . curl_error($curl);
        } else {
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            if (strlen($response) === $header_size) {
                $body = '';
            } else {
                $body = substr($response, $header_size);
            }
            $response = $body;
            $this->_headers = array_reduce(explode("\r\n", $header), function ($result, $header) {
                if (false === strpos($header, ':')) {
                    return $result;
                }
                $key = explode(':', $header)[0];
                $value = trim(str_replace($key . ':', '', $header), " \t\"'");
                $result[str_replace('-', '', $key)] = $value;
                return $result;
            }, []);
        }

        $this->_headers['ResponseCode'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }

    /**
     * Send request and return pure response
     *
     * @param string $requestEntity
     * @param array $params
     * @param string|bool $ETag ETag
     * @return bool|string Return the result on success, FALSE on failure
     */
    public function getPureResponse($requestEntity, array $params = [], ?string $ETag = null)
    {
        $params['format'] = $this->format;
        $params['apikey'] = $this->apiKey;
        if ($this->supplierKey) {
            $params['supplier_key'] = $this->supplierKey;
        }
        $headers = [];
        if (null !== $ETag) {
            $headers[] = 'If-None-Match: "' . $ETag . '"';
        }
        $url = $this->getUrl($requestEntity);
        return $this->sendRequest($url, $params, $headers);
    }

    /**
     * Parse response
     *
     * @param mixed $result
     * @param string $responseObj
     * @return bool|array
     * @throws \Exception
     */
    public function parseResponse($result, $responseObj)
    {
        if ($this->format == self::RESPONSE_FORMAT_JSON) {
            $response = false !== $result ? json_decode($result, true) : false;
            if ($response && isset($response['result'])) {
                return $this->prepareResponseObj($response, $responseObj);
            }
        } else {
            if ($this->format == self::RESPONSE_FORMAT_XML) {
                $response = \simplexml_load_string($result);
                if ($response) {
                    return $this->prepareResponseObj($response, $responseObj);
                }
            }
        }
        $this->_error = null;
        switch ($this->getHttpCode()) {
            case self::CODE_STATUS_OK:
                break;
            case self::CODE_STATUS_REQUEST_ERROR:
                $this->_error = 'Error (' . $this->getHttpCode() . '): request error';
                break;
            case self::CODE_STATUS_NOT_MODIFIED:
                $this->_error = 'Error (' . $this->getHttpCode() . '): not modified';
                break;
            case self::CODE_STATUS_NOT_AUTHORIZED:
                $this->_error = 'Error (' . $this->getHttpCode() . '): not authorized';
                break;
            case self::CODE_STATUS_NO_ACCESS:
                $this->_error = 'Error (' . $this->getHttpCode() . '): no access';
                break;
            case self::CODE_STATUS_NO_DATA_FOUND:
                $this->_error = 'Error (' . $this->getHttpCode() . '): data not found';
                $this->prepareResponseObj([], $responseObj);
                break;
            case self::CODE_STATUS_REQUEST_ENTITY_TO_LARGE:
                $this->_error = 'Error (' . $this->getHttpCode() . '): request entity to large';
                break;
            case self::CODE_STATUS_REQUEST_LIMIT_REACHED:
                $this->_error = 'Error (' . $this->getHttpCode() . '): request limit reached';
                break;
            case self::CODE_STATUS_INTERNAL_SERVER_ERROR:
                $this->_error = 'Error (' . $this->getHttpCode() . '): internal server error';
                break;
            case self::CODE_STATUS_METHOD_NOT_FOUND:
                $this->_error = 'Error (' . $this->getHttpCode() . '): method not found';
                break;
            case self::CODE_STATUS_SERVICE_NOT_AVAILABLE:
                $this->_error = 'Error (' . $this->getHttpCode() . '): service not available';
                break;
            default:
                $this->_error = 'Error (' . $this->getHttpCode() . ')';
                break;
        }

        if ($this->_error) {
            throw new \Exception($this->_error, $this->getHttpCode());
        }
        return false;
    }

    /*** Get response
     *
     * @param string $requestEntity
     * @param string $responseObj
     * @param array $params
     * @param string|null $ETag
     * @return array|bool
     * @throws \Exception
     */
    public function request(string $requestEntity, string $responseObj, array $params = [], ?string $ETag = null)
    {
        $result = $this->getPureResponse($requestEntity, $params, $ETag);
        return $this->parseResponse($result, $responseObj);
    }

    /**
     * Return the user agent string
     * @return string
     */
    protected function getUserAgent()
    {
        return 'NationalCatalog PHP API client ' . self::VERSION;
    }

    /**
     * Return Url string
     * @param string $requestEntity
     * @return string
     */
    protected function getUrl(string $requestEntity)
    {
        return $this->apiUrl . '/' . self::VERSION . '/' . $requestEntity;
    }

    /**
     * Return list of brands
     *
     * @param string $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getBrands(?string $ETag = null)
    {
        return $this->request(self::REQUEST_ENTITY_BRANDS, ApiResponse::RESPONSE_BRANDS, [], $ETag);
    }

    /**
     * Return list of brands
     *
     * @param int|null $partyId
     * @param string|null $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getLocations(?int $partyId = null, ?string $ETag = null)
    {
        $params = [];
        if (isset($partyId)) {
            $params = [
                'party_id' => $partyId
            ];
        }
        return $this->request(self::REQUEST_ENTITY_LOCATIONS, ApiResponse::RESPONSE_LOCATIONS, $params, $ETag);
    }

    /**
     * Return list of categories
     *
     * @param string|null $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getCategories(?string $ETag = null)
    {
        return $this->request(self::REQUEST_ENTITY_CATEGORIES, ApiResponse::RESPONSE_CATEGORIES, [], $ETag);
    }

    /**
     * Return party list by role
     *
     * @param string|null $role
     * @return bool|array
     * @throws \Exception
     */
    public function getParties(?string $role = null)
    {
        return $this->request(self::REQUEST_ENTITY_PARTIES, ApiResponse::RESPONSE_PARTIES, ['role' => $role]);
    }

    /**
     * Return list of products
     *
     * @param string $query
     * @return bool|array
     * @throws \Exception
     */
    public function getSuggestions(string $query)
    {
        $params = [
            'q' => $query
        ];
        return $this->request(self::REQUEST_ENTITY_SUGGESTIONS, ApiResponse::RESPONSE_SUGGESTIONS, $params);
    }

    /**
     * Return list of attributes
     *
     * @param int|null $catId category id
     * @param  $attrType attribute type (const)
     * @return bool|array
     * @throws \Exception
     */
    public function getAttributes(?int $catId = null, $attrType = null)
    {
        $params = [];
        if (isset($catId)) {
            $params['cat_id'] = $catId;
        }
        if (isset($attrType)) {
            $params['attr_type'] = $attrType;
        }
        return $this->request(self::REQUEST_ENTITY_ATTRIBUTES, ApiResponse::RESPONSE_ATTRIBUTE, $params);
    }

    /**
     * Add reply to review
     *
     * @param int $reviewParentId parent review id
     * @param string $reviewText message
     * @param string $socialType social network type (const)
     * @param string $socialId social network id
     * @param string $reviewAuthor author name
     * @param float $reviewRating rating
     * @return bool|array
     * @throws \Exception
     */
    public function addReplyToReview(
        int $reviewParentId,
        string $reviewText,
        string $socialType,
        string $socialId,
        string $reviewAuthor,
        float $reviewRating
    )
    {
        $params = [
            'review_parent_id' => $reviewParentId,
            'review_text' => $reviewText,
            'social_type' => $socialType,
            'social_id' => $socialId,
            'review_author' => $reviewAuthor,
            'review_rating' => $reviewRating
        ];
        return $this->request(self::REQUEST_ENTITY_ADD_REVIEW, ApiResponse::RESPONSE_ADD_REVIEW, $params);
    }

    /**
     * Add review to party
     *
     * @param int $partyId party id
     * @param string $reviewText message
     * @param string $socialType social network type (const)
     * @param string $socialId social network id
     * @param string $reviewAuthor author name
     * @param float $reviewRating rating
     * @return bool|array
     * @throws \Exception
     */
    public function addReviewToParty(
        int $partyId,
        string $reviewText,
        string $socialType,
        string $socialId,
        string $reviewAuthor,
        float $reviewRating
    )
    {
        $params = [
            'party_id' => $partyId,
            'review_text' => $reviewText,
            'social_type' => $socialType,
            'social_id' => $socialId,
            'review_author' => $reviewAuthor,
            'review_rating' => $reviewRating
        ];
        return $this->request(self::REQUEST_ENTITY_ADD_REVIEW, ApiResponse::RESPONSE_ADD_REVIEW, $params);
    }

    /**
     * Add review to brand
     *
     * @param int $brandId brand id
     * @param string $reviewText message
     * @param string $socialType social network type (const)
     * @param string $socialId social network id
     * @param string $reviewAuthor author name
     * @param float $reviewRating rating
     * @return bool|array
     * @throws \Exception
     */
    public function addReviewToBrand(
        int $brandId,
        string $reviewText,
        string $socialType,
        string $socialId,
        string $reviewAuthor,
        float $reviewRating
    )
    {
        $params = [
            'brand_id' => $brandId,
            'review_text' => $reviewText,
            'social_type' => $socialType,
            'social_id' => $socialId,
            'review_author' => $reviewAuthor,
            'review_rating' => $reviewRating
        ];
        return $this->request(self::REQUEST_ENTITY_ADD_REVIEW, ApiResponse::RESPONSE_ADD_REVIEW, $params);
    }

    /**
     * Add review to good
     *
     * @param int $goodId good id
     * @param string $reviewText message
     * @param string $socialType social network type (const)
     * @param string $socialId social network id
     * @param string $reviewAuthor author name
     * @param float $reviewRating rating
     * @return bool|array
     * @throws \Exception
     */
    public function addReviewToGood(
        int $goodId,
        string $reviewText,
        string $socialType,
        string $socialId,
        string $reviewAuthor,
        float $reviewRating
    )
    {
        $params = [
            'good_id' => $goodId,
            'review_text' => $reviewText,
            'social_type' => $socialType,
            'social_id' => $socialId,
            'review_author' => $reviewAuthor,
            'review_rating' => $reviewRating
        ];
        return $this->request(self::REQUEST_ENTITY_ADD_REVIEW, ApiResponse::RESPONSE_ADD_REVIEW, $params);
    }

    /**
     * Return information about product by id
     *
     * @param int $goodId
     * @param string|bool $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getProductById(int $goodId, ?string $ETag = null)
    {
        $params = [
            'good_id' => $goodId
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, ApiResponse::RESPONSE_PRODUCTS, $params, $ETag);
    }

    /**
     * Return information about products by GTIN
     *
     * @param string $gtin
     * @param string|null $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getProductsByGtin(string $gtin, ?string $ETag = null)
    {
        $params = [
            'gtin' => $gtin
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, ApiResponse::RESPONSE_PRODUCTS, $params, $ETag);
    }

    /**
     * Return information about products by LTIN
     *
     * @param string $ltin
     * @param int $partyId
     * @param string|null $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getProductsByLtin(string $ltin, int $partyId, ?string $ETag = null)
    {
        $params = [
            'ltin' => $ltin,
            'party_id' => $partyId
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, ApiResponse::RESPONSE_PRODUCTS, $params, $ETag);
    }

    /**
     * Return information about products by SKU
     *
     * @param string $sku
     * @param int $partyId
     * @param string $ETag ETag
     * @return bool|array
     * @throws \Exception
     */
    public function getProductsBySku(string $sku, int $partyId, string $ETag = null)
    {
        $params = [
            'sku' => $sku,
            'party_id' => $partyId
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, ApiResponse::RESPONSE_PRODUCTS, $params, $ETag);
    }

    /**
     * Return array [ GoodId, ETag, Attributes ] for party
     *
     * @param int $partyId
     * @return bool|array
     * @throws \Exception
     */
    public function getETagsList(int $partyId)
    {
        $params = [
            'party_id' => $partyId
        ];
        return $this->request(self::REQUEST_ENTITY_ETAGS_LIST, ApiResponse::RESPONSE_ETAGS_LIST, $params);
    }

    /**
     * Get status of created feed
     *
     * @param int $feedId feed id
     * @return bool|array
     * @throws \Exception
     */
    public function getFeedStatus(int $feedId)
    {
        $params = [
            'feed_id' => $feedId
        ];
        return $this->request(self::REQUEST_ENTITY_FEED_STATUS, ApiResponse::RESPONSE_FEED_STATUS, $params);
    }

    /**
     * Post feed
     *
     * @param mixed $content
     * @return bool|array
     * @throws \Exception
     */
    public function postFeed($content)
    {
        $params['apikey'] = $this->apiKey;
        $params['supplier_key'] = $this->supplierKey;
        $params['format'] = $this->format;
        $url = $this->getUrl(self::REQUEST_ENTITY_FEED) . '?' . http_build_query($params);
        $body = ($content instanceof Feed) ? $content->asJson() : $content;

        $contentTypeHeader = 'Content-Type: application/';

        $isXml = @substr($body, 0, 1) == '<';
        $contentTypeHeader .= ($isXml) ? "xml" : "json";

        $headers = [$contentTypeHeader];

        $result = $this->sendRequest($url, $body, $headers);
        return $this->parseResponse($result, ApiResponse::RESPONSE_FEED);
    }

    /**
     * * Method for prepare response object
     * @param $response - recieved response
     * @param string $requestedApi - class name with namespace
     * @return mixed
     */
    private function prepareResponseObj($response, $requestedApi)
    {
        $originalResponse = $response;
        if ($this->format == self::RESPONSE_FORMAT_XML) {
            $response = json_decode(json_encode((array)$response), true);
        }
        /**
         * @var ApiResponse $responseObj
         */
        $responseObj = new $requestedApi($response['result']);
        $responseObj->setApiVersion($response['apiversion']);

        $result = $response['result'];

        /*if ($this->format == self::RESPONSE_FORMAT_XML) {
            $result = $originalResponse->xpath('result');
        }
        */
        $responseObj->setResult($result);
        return $responseObj;
    }
}
