# NationalCatalogApiClient
Client for National Catalog Api v3

## Installation

composer require Center4ResearchPerspectiveTechnologies/NationalCatalogApiClient master@dev

## Example of usage

```
$api = new \NationalCatalogApi\Client(LABORATORY_API_KEY, SUPPLIER_API_KEY);

$api->setUrl('http://api.crpt.my');// can skip, then used default https://апи.национальный-каталог.рф

$feed = new \NationalCatalogApi\Feed();

$entry = $feed->newEntry();// returns empty object Entity, not related yet with a feed

$entry->setGoodId(123);
$entry->setGoodName("Шоколад");

$entry->addCategory(78);
$entry->deleteCategory(90);

$entry->addIdentifiedBy(\NationalCatalogApi\Entry::IDENTIFIER_TYPE_GTIN, "4602065373085");
$entry->addIdentifiedBy(\NationalCatalogApi\Entry::IDENTIFIER_TYPE_SKU, "4602065373000", 3);

$entry->addAttr(2123, "4602065373085");
$entry->addAttr(1324, "4602065373000", 3);

$entry->addImage(\NationalCatalogApi\Entry::PHOTO_TYPE_DEFAULT, "https://public_domain/your_picture1.jpg", 2);
$entry->addImage(\NationalCatalogApi\Entry::PHOTO_TYPE_DEFAULT, [
          "https://public_domain/your_picture2.jpg",
          "https://public_domain/your_picture3.jpg"
        ]);

$feed->addEntry($entry); //add created entry to feed

print_r($feed->asJson()); // if you want to check the feed

$result = $api->postFeed($feed);// we can pass $feed or $feed->asJson()
```

## Json

```
[
    {
        "good_id": 123,
        "good_name": "Шоколад",
        "categories": [
            {
                "cat_id": 78
            },
            {
                "cat_id": 90,
                "delete": 1
            }
        ],
        "identified_by": [
            {
                "type": "gtin",
                "value": "4602065373085",
                "multiplier": 1,
                "level": "trade-unit"
            },
            {
                "type": "sku",
                "value": "4602065373000",
                "multiplier": 1,
                "level": "trade-unit",
                "party_id": 3
            }
        ],
        "good_attrs": [
            {
                "attr_id": 2123,
                "attr_value": "4602065373085"
            },
            {
                "attr_id": 1324,
                "attr_value": "4602065373000",
                "attr_value_type": 3
            }
        ],
        "good_images": [
            {
                "photo_type": "default",
                "photo_url": "https://public_domain/your_picture1.jpg",
                "location_id": 2
            },
            {
                "photo_type": "3ds",
                "photo_url": [
                    "https://public_domain/your_picture2.jpg",
                    "https://public_domain/your_picture3.jpg"
                ]
            }
        ]
    }
]

```

## Result

```
Array
(
    [feed_id] => 131
)
```
