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
    const IDENTIFIER_LEVEL_INNER_PACK = "inner-pack";

    const PHOTO_TYPE_DEFAULT = "default";
    const PHOTO_TYPE_FACING = "facing";
    const PHOTO_TYPE_LOF = '7';
    const PHOTO_TYPE_BACK = '13';
    const PHOTO_TYPE_ROF = '19';
    const PHOTO_TYPE_TOP = 'si1';
    const PHOTO_TYPE_BOTTOM = 'si2';
    const PHOTO_TYPE_IN_PACKAGING = 'si3';
    const PHOTO_TYPE_OUT_OF_PACKAGING = 'si4';
    const PHOTO_TYPE_INNER_PACK = 'si5';
    const PHOTO_TYPE_TEXT = 'text';
    const PHOTO_TYPE_3DS = "3ds";
    const PHOTO_TYPE_MARKETING = "marketing";
    const PHOTO_TYPE_ECOMMERCE = "ecommerce";
    const PHOTO_TYPE_UNDEF = "undef";
    const PHOTO_TYPE_CUBI = "cubi";

    const ENTRY_OBJECTS_KEY_ATTR = "good_attrs";
    const ENTRY_OBJECTS_KEY_IMAGE = "good_images";
    const ENTRY_OBJECTS_KEY_CATEGORY = "categories";
    const ENTRY_OBJECTS_KEY_IDENTIFIER = "identified_by";

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
    public function addCategory(int $catId): void
    {
        $category = ['cat_id' => $catId];

        $this->addObjectToEntry(self::ENTRY_OBJECTS_KEY_CATEGORY, $category);
    }

    /**
     * @param int $catId
     */
    public function deleteCategory(int $catId): void
    {
        $category = ['cat_id' => $catId, 'delete' => 1];

        $this->addObjectToEntry(self::ENTRY_OBJECTS_KEY_CATEGORY, $category);
    }

    /**
     * @param string $type
     * @param string $value
     * @param int $partyId
     * @param string $level
     * @param int $multiplier
     * @param string $unit
     */
    public function addIdentifiedBy(
        string $type,
        string $value,
        int $partyId = null,
        string $level = self::IDENTIFIER_LEVEL_TRADE_UNIT,
        int $multiplier = 1,
        string $unit = null
    ): void {
        $identifiedBy = [
            'type' => $type,
            'value' => $value,
            'multiplier' => $multiplier,
            'level' => $level
        ];
        if ($partyId) {
            $identifiedBy['party_id'] = $partyId;
        }
        if ($unit) {
            $identifiedBy['unit'] = $unit;
        }

        $this->addObjectToEntry(self::ENTRY_OBJECTS_KEY_IDENTIFIER, $identifiedBy);
    }

    /**
     * @param int $attrId
     * @param mixed $attrValue
     * @param string $attrValueType
     * @param string $gtin
     */
    public function addAttr(int $attrId, $attrValue, string $attrValueType = null, string $gtin = null): void
    {
        $attr = [
            'attr_id' => $attrId,
            'attr_value' => $attrValue
        ];
        if ($attrValueType) {
            $attr['attr_value_type'] = $attrValueType;
        }
        if ($gtin) {
            $attr['gtin'] = $gtin;
        }

        $this->addObjectToEntry(self::ENTRY_OBJECTS_KEY_ATTR, $attr);
    }

    /**
     * @param int $attrValueId
     * @param int $attrId
     * @param mixed $attrValue
     * @param string $attrValueType
     * @param string $gtin
     */
    public function updateAttr(int $attrValueId, int $attrId, $attrValue, string $attrValueType = null, string $gtin = null): void
    {
        $attr = [
            'attr_value_id' => $attrValueId,
            'attr_id' => $attrId,
            'attr_value' => $attrValue
        ];
        if ($attrValueType) {
            $attr['attr_value_type'] = $attrValueType;
        }

        if ($gtin) {
            $attr['gtin'] = $gtin;
        }

        $this->addObjectToEntry(self::ENTRY_OBJECTS_KEY_ATTR, $attr);
    }

    /**
     * @param string $type
     * @param string|array $url
     * @param string $gtin
     * @param int $locationId
     */
    public function addImage(string $type, $url, string $gtin = null, int $locationId = null): void
    {
        $image = [
            'photo_type' => $type,
            'photo_url' => $url
        ];

        if ($locationId) {
            $image['location_id'] = $locationId;
        }

        if ($gtin) {
            $image['gtin'] = $gtin;
        }

        $this->addObjectToEntry(self::ENTRY_OBJECTS_KEY_IMAGE, $image);
    }

    /**
     * @param int $goodId
     */
    public function setGoodId(int $goodId): void
    {
        $this->entry['good_id'] = $goodId;
    }

    /**
     * @param string $goodName
     */
    public function setGoodName(string $goodName): void
    {
        $this->entry['good_name'] = $goodName;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->entry;
    }

    /**
     * @param string $key
     * @param array $object
     */
    private function addObjectToEntry(string $key, array $object): void
    {
        if (!isset($this->entry[$key])) {
            $this->entry[$key] = [];
        }
        $this->entry[$key][] = $object;
    }
}
