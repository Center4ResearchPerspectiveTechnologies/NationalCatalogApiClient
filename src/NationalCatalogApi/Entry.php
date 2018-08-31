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

final class Entry
{
    const IDENTIFIER_TYPE_GTIN = "gtin";
    const IDENTIFIER_TYPE_SKU = "sku";

    const IDENTIFIER_LEVEL_TRADE_UNIT = "trade-unit";
    const IDENTIFIER_LEVEL_BOX = "box";
    const IDENTIFIER_LEVEL_LAYER = "layer";
    const IDENTIFIER_LEVEL_PALLET = "pallet";
    const IDENTIFIER_LEVEL_METRO_UNIT = "metro-unit";
    const IDENTIFIER_LEVEL_SHOW_PACK = "show-pack";
    const IDENTIFIER_LEVEL_MULTI_PACK = "multi-pack";

    const PHOTO_TYPE_DEFAULT = "default";
    const PHOTO_TYPE_FACING = "facing";
    const PHOTO_TYPE_LEFT = "left";
    const PHOTO_TYPE_RIGHT = "right";
    const PHOTO_TYPE_BACK = "back";
    const PHOTO_TYPE_3DS = "3ds";
    const PHOTO_TYPE_MARKETING = "marketing";
    const PHOTO_TYPE_ECOMMERCE = "ecommerce";
    const PHOTO_TYPE_UNDEF = "undef";
    const PHOTO_TYPE_CUBI = "cubi";

    /**
     * @var array
     */
    private $entry = [];

    /**
     * @param mixed $id
     */
    public function setInternalId($id)
    {
        $this->entry['@id'] = $id;
    }

    /**
     * @param int $catId
     */
    public function addCategory(int $catId) : void
    {
        if (!isset($this->entry['categories'])) {
            $this->entry['categories'] = [];
        }
        $this->entry['categories'][] = ['cat_id' => $catId];
    }

    /**
     * @param int $catId
     */
    public function deleteCategory(int $catId) : void
    {
        if (!isset($this->entry['categories'])) {
            $this->entry['categories'] = [];
        }
        $this->entry['categories'][] = ['cat_id' => $catId, 'delete' => 1];
    }

    /**
     * @param string $type
     * @param string $value
     * @param int $partyId
     * @param string $level
     * @param int $multiplier
     */
    public function addIdentifiedBy(string $type, string $value, int $partyId = 0, string $level = self::IDENTIFIER_LEVEL_TRADE_UNIT, int $multiplier = 1) : void
    {
        $identifiedBy = [
            'type' => $type,
            'value' => $value,
            'multiplier' => $multiplier,
            'level' => $level
        ];
        if ($partyId) {
            $identifiedBy['party_id'] = $partyId;
        }
        if (!isset($this->entry['identified_by'])) {
            $this->entry['identified_by'] = [];
        }
        $this->entry['identified_by'][] = $identifiedBy;
    }

    /**
     * @param int $attrId
     * @param mixed $attrValue
     * @param string $attrValueType
     * @param int $attrValueId
     */
    public function addAttr(int $attrId, $attrValue, string $attrValueType = null, int $attrValueId = null) : void
    {
        $attr = [
            'attr_id' => $attrId,
            'attr_value' => $attrValue
        ];
        if ($attrValueType) {
            $attr['attr_value_type'] = $attrValueType;
        }
        if ($attrValueId) {
            $attr['attr_value_id'] = $attrValueId;
        }
        if (!isset($this->entry['good_attrs'])) {
            $this->entry['good_attrs'] = [];
        }
        $this->entry['good_attrs'][] = $attr;
    }

    /**
     * @param string $type
     * @param string|array $url
     * @param int $locationId
     */
    public function addImage(string $type, $url, int $locationId = null) : void
    {
        $image = [
            'photo_type' => $type,
            'photo_url' => $url
        ];

        if ($locationId) {
            $image['location_id'] = $locationId;
        }

        if (!isset($this->entry['good_images'])) {
            $this->entry['good_images'] = [];
        }
        $this->entry['good_images'][] = $image;
    }

    /**
     * @param int $goodId
     */
    public function setGoodId(int $goodId) : void
    {
        $this->entry['good_id'] = $goodId;
    }

    /**
     * @param string $goodName
     */
    public function setGoodName(string $goodName) : void
    {
        $this->entry['good_name'] = $goodName;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->entry;
    }
}
