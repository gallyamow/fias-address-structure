<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevel;
use PHPUnit\Framework\TestCase;

class MainLevelRelationResolverTest extends TestCase
{
    private MainLevelRelationResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new MainLevelRelationResolver(new RelationLevelResolver());
    }

    /**
     * @test
     */
    public function itShouldChooseNewestRelation(): void
    {
        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_2020-05-05",
           "level":"5",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "name":"FOR_TEST_2016-09-29",
           "level":"5",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::CITY, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_2020-05-05',
            $res['data']['name']
        );

        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_2020-05-05",
           "level":"5",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":212121,
        "data":{
           "name":"FOR_TEST_2021-08-20",
           "level":"5",
           "startdate":"2021-08-20"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "name":"FOR_TEST_2016-09-29",
           "level":"5",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::CITY, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_2021-08-20',
            $res['data']['name']
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesStreetRelation(): void
    {
        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_16",
           "level":"13",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "name":"FOR_TEST_16",
           "level":"16",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
     {
        "id":14393,
        "data":{
           "name":"FOR_TEST_8",
           "level":"8",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::STREET, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_8',
            $res['data']['name']
        );

        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_13",
           "level":"13",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "name":"FOR_TEST_16",
           "level":"16",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::STREET, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_16',
            $res['data']['name']
        );

        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_13_2020-05-05",
           "level":"13",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "name":"FOR_TEST_13_2016-09-29",
           "level":"13",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::STREET, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_13_2020-05-05',
            $res['data']['name']
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesTerritoryRelation(): void
    {
        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "id":7398,
           "name":"Кировский",
           "level":"14",
           "nextid":0,
           "previd":0,
           "enddate":"2079-06-06",
           "changeid":20549,
           "isactive":1,
           "isactual":1,
           "objectid":6513,
           "typename":"р-н",
           "startdate":"1970-01-01",
           "objectguid":"65c87dd4-c269-483a-b3ce-c4d37603c4ba",
           "opertypeid":10,
           "updatedate":"2017-11-19"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "id":14393,
           "name":"ЛОК Солнечные пески в районе Мелькомбина",
           "level":"7",
           "nextid":0,
           "previd":14381,
           "enddate":"2079-06-06",
           "changeid":33984,
           "isactive":1,
           "isactual":1,
           "objectid":11976,
           "typename":"тер",
           "startdate":"2016-09-29",
           "objectguid":"c4f42ce4-0bd0-4f64-9bfe-c2ca9218efb8",
           "opertypeid":50,
           "updatedate":"2017-03-11"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::TERRITORY, json_decode($json, true));
        $this->assertEquals(
            'ЛОК Солнечные пески в районе Мелькомбина',
            $res['data']['name']
        );

        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_14",
           "level":"14",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
    {
        "id":14393,
        "data":{
           "name":"FOR_TEST_7",
           "level":"7",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
     {
        "id":14393,
        "data":{
           "name":"FOR_TEST_15",
           "level":"15",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::TERRITORY, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_7',
            $res['data']['name']
        );

        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_14",
           "level":"14",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
     {
        "id":14393,
        "data":{
           "name":"FOR_TEST_15",
           "level":"15",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::TERRITORY, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_15',
            $res['data']['name']
        );

        $json = <<<JSON
[
    {
        "id":7398,
        "data":{
           "name":"FOR_TEST_14_2020-05-05",
           "level":"14",
           "startdate":"2020-05-05"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    },
     {
        "id":14393,
        "data":{
           "name":"FOR_TEST_14_2016-09-29",
           "level":"14",
           "startdate":"2016-09-29"
        },
        "type":"addr_obj",
        "is_active":1,
        "is_actual":1
    }
]
JSON;

        /** @noinspection JsonEncodingApiUsageInspection */
        $res = $this->resolver->resolve(AddressLevel::TERRITORY, json_decode($json, true));
        $this->assertEquals(
            'FOR_TEST_14_2020-05-05',
            $res['data']['name']
        );
    }
}
