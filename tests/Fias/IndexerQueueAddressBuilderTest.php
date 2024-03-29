<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Tests\Fias;

use Addresser\FiasAddressStructure\ActualityComparator;
use Addresser\FiasAddressStructure\AddressBuilderInterface;
use Addresser\FiasAddressStructure\AddressLevel;
use Addresser\FiasAddressStructure\AddressSynonymizer;
use Addresser\FiasAddressStructure\Exceptions\InvalidAddressLevelException;
use Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers\AddHouseAddressLevelSpecResolver;
use Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers\ApartmentAddressLevelSpecResolver;
use Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers\HouseAddressLevelSpecResolver;
use Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers\ObjectAddressLevelSpecResolver;
use Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers\RoomAddressLevelSpecResolver;
use Addresser\FiasAddressStructure\Fias\BaseNameNormalizer;
use Addresser\FiasAddressStructure\Fias\IndexerQueueAddressBuilder;
use Addresser\FiasAddressStructure\Fias\FiasLevel;
use Addresser\FiasAddressStructure\Fias\RelationLevelResolver;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class IndexerQueueAddressBuilderTest extends TestCase
{
    private AddressBuilderInterface $builder;

    protected function setUp(): void
    {
        $relationLevelResolved = new RelationLevelResolver();

        $this->builder = new IndexerQueueAddressBuilder(
            new ObjectAddressLevelSpecResolver(),
            new HouseAddressLevelSpecResolver(),
            new AddHouseAddressLevelSpecResolver(),
            new ApartmentAddressLevelSpecResolver(),
            new RoomAddressLevelSpecResolver(),
            new ActualityComparator(),
            new AddressSynonymizer(),
            $relationLevelResolved,
            new BaseNameNormalizer()
        );
    }

    /**
     * CAR_PLACE - пока не обрабатываем
     *
     * @test
     */
    public function itShouldThrowExceptionWhenBuildsCarPlace(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(sprintf('Unsupported address level "%d".', AddressLevel::CAR_PLACE));

        $this->builder->build(
            [
                'object_id' => 95392599,
                'path_ltree' => '5705.6326.8931.70915638.95392599',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6326,"types":["addr_obj"],"relations":[{"id": 7148, "data": {"id": 7148, "name": "Уфа", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19976, "isactive": 1, "isactual": 1, "objectid": 6326, "typename": "г", "startdate": "1900-01-01", "objectguid": "7339e834-2cb4-4734-a4c7-1fca2c66e562", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":8931,"types":["addr_obj"],"relations":[{"id": 10636, "data": {"id": 10636, "name": "Максима Горького", "level": "8", "nextid": 0, "previd": 10603, "enddate": "2079-06-06", "changeid": 28103, "isactive": 1, "isactual": 1, "objectid": 8931, "typename": "ул", "startdate": "1900-01-01", "objectguid": "6697bd2e-7a91-4524-ba4e-d543c2324da4", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 10603, "data": {"id": 10603, "name": "Горького", "level": "8", "nextid": 10636, "previd": 0, "enddate": "1900-01-01", "changeid": 28056, "isactive": 0, "isactual": 0, "objectid": 8931, "typename": "ул", "startdate": "1900-01-01", "objectguid": "6697bd2e-7a91-4524-ba4e-d543c2324da4", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":70915638,"types":["house"],"relations":[{"id": 42771639, "data": {"id": 42771639, "nextid": 42771811, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2017-01-20", "addtype1": null, "addtype2": null, "changeid": 105648294, "housenum": "51", "isactive": 0, "isactual": 0, "objectid": 70915638, "housetype": 2, "startdate": "2016-12-08", "objectguid": "ec8a5184-f20d-4e07-8ef4-32d3ab3d0464", "opertypeid": 10, "updatedate": "2018-04-12"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 42771811, "data": {"id": 42771811, "nextid": 68135167, "previd": 42771639, "addnum1": null, "addnum2": null, "enddate": "2018-04-09", "addtype1": null, "addtype2": null, "changeid": 105648702, "housenum": "51", "isactive": 0, "isactual": 0, "objectid": 70915638, "housetype": 2, "startdate": "2017-01-20", "objectguid": "ec8a5184-f20d-4e07-8ef4-32d3ab3d0464", "opertypeid": 20, "updatedate": "2018-04-12"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 68135167, "data": {"id": 68135167, "nextid": 0, "previd": 42771811, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 105648732, "housenum": "51", "isactive": 1, "isactual": 1, "objectid": 70915638, "housetype": 2, "startdate": "2018-04-09", "objectguid": "ec8a5184-f20d-4e07-8ef4-32d3ab3d0464", "opertypeid": 20, "updatedate": "2018-04-12"}, "type": "house", "is_active": 1, "is_actual": 1}]},{"object_id":95392599,"types":["carplace"],"relations":[{"id": 1530, "data": {"id": 1530, "nextid": 0, "number": "2108", "previd": 0, "enddate": "2079-06-06", "changeid": 138548530, "isactive": 1, "isactual": 1, "objectid": 95392599, "startdate": "2020-02-02", "objectguid": "4dba70c7-937e-42d2-a2a5-5ecbbfac7c1f", "opertypeid": 10, "updatedate": "2020-02-02"}, "type": "carplace", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6326,"values":[{"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200100100051", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000000", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"}]},{"object_id":8931,"values":[{"value": "0277", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0277", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000022101", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000022151", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020010010000221", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000022100", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0221", "type_id": 15, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0903", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":70915638,"values":[{"value": "02:55:030317:18", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-01-20"},{"value": "450105", "type_id": 5, "end_date": "2018-04-09", "is_actual": false, "start_date": "2017-01-20"},{"value": "450112", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-04-09"},{"value": "807010000010000090320055000000005", "type_id": 13, "end_date": "2018-04-09", "is_actual": false, "start_date": "2016-12-08"},{"value": "807010000010000090320055000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-04-09"},{"value": "0277", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-12-08"},{"value": "0277", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-12-08"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-12-08"},{"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-12-08"},{"value": "2", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-12-08"},{"value": "55", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-12-08"}]},{"object_id":95392599,"values":[{"value": "02:55:030317:433", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-02"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-02"},{"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-02"},{"value": "450112", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-02"},{"value": "0277", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-02"},{"value": "0277", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-02"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );
    }

    /**
     * STEAD - пока не обрабатываем
     *
     * @test
     */
    public function itShouldThrowExceptionWhenBuildsStead(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(sprintf('Unsupported address level "%d".', AddressLevel::STEAD));

        $this->builder->build(
            [
                'object_id' => 96170133,
                'path_ltree' => '5705.11745.13232.15675.96170133',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":11745,"types":["addr_obj"],"relations":[{"id": 14070, "data": {"id": 14070, "name": "Уфимский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 33545, "isactive": 1, "isactual": 1, "objectid": 11745, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c7a81174-8d01-4ae6-83e6-386ae23ee629", "opertypeid": 1, "updatedate": "2017-04-20"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":13232,"types":["addr_obj"],"relations":[{"id": 16061, "data": {"id": 16061, "name": "Суровка", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 36668, "isactive": 1, "isactual": 1, "objectid": 13232, "typename": "д", "startdate": "1900-01-01", "objectguid": "fda8f95f-78f8-4030-b39a-35efee12782f", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":15675,"types":["addr_obj"],"relations":[{"id": 19083, "data": {"id": 19083, "name": "Янтарная", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 42474, "isactive": 1, "isactual": 1, "objectid": 15675, "typename": "ул", "startdate": "2019-10-21", "objectguid": "754edbc7-9db7-44b2-9547-9dd884c0aa7f", "opertypeid": 10, "updatedate": "2019-10-21"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":96170133,"types":["stead"],"relations":[{"id": 11255306, "data": {"id": 11255306, "nextid": 0, "number": "16", "previd": 0, "enddate": "2079-06-06", "changeid": 142857252, "isactive": 1, "isactual": 1, "objectid": 96170133, "startdate": "2020-03-30", "objectguid": "8a4e0bb4-9349-4ea2-9520-c0a840117af6", "opertypeid": "10", "updatedate": "2020-03-30"}, "type": "stead", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":11745,"values":[{"value": "80252000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80652000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-04-19"},{"value": "0200100000001", "type_id": 10, "end_date": "2017-04-19", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-04-19"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2017-04-19", "is_actual": false, "start_date": "1900-01-01"},{"value": "806520000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-04-19"}]},{"object_id":13232,"values":[{"value": "0245", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-04-27"},{"value": "0245", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-04-27"},{"value": "0272", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0272", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80252840005", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "450511", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001000055", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0245", "type_id": 3, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0245", "type_id": 4, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80652440", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80652440116", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200100005501", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200100005500", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806524400000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806524401160000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":15675,"values":[{"value": "0245", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-04-27"},{"value": "0245", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-04-27"},{"value": "0272", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "0272", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "80652440116", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "80252840005", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "450511", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "02001000055004000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "020010000550040", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "806524401160000004001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"},{"value": "0040", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-10-21"}]},{"object_id":96170133,"values":[{"value": "0245", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-04-27"},{"value": "0245", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-04-27"},{"value": "02:47:110901:97", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "80652440116", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "80252840005", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "450511", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "0272", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "0272", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "8", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"},{"value": "806524401160000004010008000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-30"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsChuvashia(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 259389,
                'path_ltree' => '259389',
                'objects' => '[{"object_id":259389,"types":["addr_obj"],"relations":[{"id": 318076, "data": {"id": 318076, "name": "Чувашская Республика -", "level": "1", "nextid": 0, "previd": 318070, "enddate": "2079-06-06", "changeid": 667684, "isactive": 1, "isactual": 1, "objectid": 259389, "typename": "Чувашия", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2016-02-24"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 318070, "data": {"id": 318070, "name": "Чувашская Республика -", "level": "1", "nextid": 318076, "previd": 0, "enddate": "1900-01-01", "changeid": 667675, "isactive": 0, "isactual": 0, "objectid": 259389, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":259389,"values":[{"value": "Чувашская республика - Чувашия", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "Чувашская республика", "type_id": 16, "end_date": "1900-01-01", "is_actual": true, "start_date": "1900-01-01"},{"value": "2100", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2100", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "97000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "21000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2100000000001", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "2100000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "970000000000000000000", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "970000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "97000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals('878fc621-3708-46c7-a97f-5a13a4176b3e', $address->getFiasId());
        $this->assertEquals(259389, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::REGION, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::REGION, $address->getAddressLevel());
        $this->assertEquals('2100000000000', $address->getKladrId());
        $this->assertEquals('97000000000', $address->getOkato());
        $this->assertEquals('97000000', $address->getOktmo());
        $this->assertEquals(['Чувашия'], $address->getSynonyms());

        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());

        $this->assertNull($address->getAreaFiasId());
        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getSettlementFiasId());
        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getBlock1());
        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getRoomFiasId());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsRenaming(): void
    {
        // переименование улицы
        $address = $this->builder->build(
            [
                'object_id' => 8654,
                'path_ltree' => '5705.6326.8654',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6326,"types":["addr_obj"],"relations":[{"id": 7148, "data": {"id": 7148, "name": "Уфа", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19976, "isactive": 1, "isactual": 1, "objectid": 6326, "typename": "г", "startdate": "1900-01-01", "objectguid": "7339e834-2cb4-4734-a4c7-1fca2c66e562", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":8654,"types":["addr_obj"],"relations":[{"id": 10275, "data": {"id": 10275, "name": "Мустая Карима", "level": "8", "nextid": 0, "previd": 10268, "enddate": "2079-06-06", "changeid": 27353, "isactive": 1, "isactual": 1, "objectid": 8654, "typename": "ул", "startdate": "1900-01-01", "objectguid": "76293e30-b0d7-4260-8d26-02c14a504ab7", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 10268, "data": {"id": 10268, "name": "Социалистическая", "level": "8", "nextid": 10275, "previd": 0, "enddate": "1900-01-01", "changeid": 27336, "isactive": 0, "isactual": 0, "objectid": 8654, "typename": "ул", "startdate": "1900-01-01", "objectguid": "76293e30-b0d7-4260-8d26-02c14a504ab7", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6326,"values":[{"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200100100051", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000000", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"}]},{"object_id":8654,"values":[{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000085201", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02001001000085202", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02001001000085251", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020010010000852", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000085200", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0852", "type_id": 15, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0544", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-10"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(['Социалистическая'], $address->getRenaming());

        $address = $this->builder->build(
            [
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'objects' => '[{"object_id":5512,"types":["addr_obj"],"relations":[{"id": 6118, "data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 6108, "data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5512,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452680", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300401", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200000300402", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        // есть переименования
        $this->assertEquals(['Крымсараево'], $address->getRenaming());

        $address = $this->builder->build(
            [
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'objects' => '[{"object_id":5512,"types":["addr_obj"],"relations":[{"id": 6118, "data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 6108, "data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5512,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452680", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300401", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200000300402", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, дер. Крым-Сараево (бывш. Крымсараево)',
            $address->getShortString(true)
        );

        // не выводится если не передавать параметр
        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, дер. Крым-Сараево',
            $address->getShortString()
        );
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsSynonyms(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 5705,
                'path_ltree' => '5705',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );
        $this->assertEquals(['Башкирия'], $address->getSynonyms());

        $address = $this->builder->build(
            [
                'object_id' => 211522,
                'path_ltree' => '211522',
                'objects' => '[{"object_id":211522,"types":["addr_obj"],"relations":[{"id": 254908, "data": {"id": 254908, "name": "Удмуртская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 543325, "isactive": 1, "isactual": 1, "objectid": 211522, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "52618b9c-bcbb-47e7-8957-95c63f0b17cc", "opertypeid": 1, "updatedate": "2017-12-04"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":211522,"values":[{"value": "Удмуртская Республика", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1800", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1800", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "94000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1800000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "18000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "940000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "94000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );
        $this->assertEquals(['Удмуртия'], $address->getSynonyms());

        $address = $this->builder->build(
            [
                'object_id' => 259389,
                'path_ltree' => '259389',
                'objects' => '[{"object_id":259389,"types":["addr_obj"],"relations":[{"id": 318076, "data": {"id": 318076, "name": "Чувашская Республика -", "level": "1", "nextid": 0, "previd": 318070, "enddate": "2079-06-06", "changeid": 667684, "isactive": 1, "isactual": 1, "objectid": 259389, "typename": "Чувашия", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2016-02-24"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 318070, "data": {"id": 318070, "name": "Чувашская Республика -", "level": "1", "nextid": 318076, "previd": 0, "enddate": "1900-01-01", "changeid": 667675, "isactive": 0, "isactual": 0, "objectid": 259389, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":259389,"values":[{"value": "Чувашская республика - Чувашия", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "Чувашская республика", "type_id": 16, "end_date": "1900-01-01", "is_actual": true, "start_date": "1900-01-01"},{"value": "2100", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2100", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "97000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "21000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2100000000001", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "2100000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "970000000000000000000", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "970000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "97000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );
        $this->assertEquals(['Чувашия'], $address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsRegion(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 5705,
                'path_ltree' => '5705',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals('респ. Башкортостан', $address->getShortString());

        // соответствующий уровень заполнен
        $this->assertEquals($address->getFiasId(), $address->getRegionFiasId());
        $this->assertEquals($address->getKladrId(), $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());

        // все остальные уровни пустые
        $this->assertNull($address->getAreaFiasId());
        $this->assertNull($address->getAreaKladrId());
        $this->assertNull($address->getAreaType());
        $this->assertNull($address->getAreaTypeFull());
        $this->assertNull($address->getArea());
        $this->assertNull($address->getAreaWithType());
        $this->assertNull($address->getAreaWithFullType());

        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCityWithType());
        $this->assertNull($address->getCityWithFullType());

        $this->assertNull($address->getSettlementFiasId());
        $this->assertNull($address->getSettlementKladrId());
        $this->assertNull($address->getSettlementType());
        $this->assertNull($address->getSettlementTypeFull());
        $this->assertNull($address->getSettlementWithType());
        $this->assertNull($address->getSettlementWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getStreetKladrId());
        $this->assertNull($address->getStreetType());
        $this->assertNull($address->getStreetTypeFull());
        $this->assertNull($address->getStreetWithType());
        $this->assertNull($address->getStreetWithFullType());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getFiasId());
        $this->assertEquals(5705, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::REGION, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::REGION, $address->getAddressLevel());
        $this->assertEquals('0200000000000', $address->getKladrId());
        $this->assertEquals('80000000000', $address->getOkato());
        $this->assertEquals('80000000', $address->getOktmo());
        $this->assertEquals('452000', $address->getPostalCode());
        // есть синоним
        $this->assertEquals(['Башкирия'], $address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsArea(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 36249,
                'path_ltree' => '5705.36249',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":36249,"types":["addr_obj"],"relations":[{"id": 42085, "data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":36249,"values":[{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452930", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals('респ. Башкортостан, Краснокамский р-н', $address->getShortString());

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());

        // соответствующий уровень заполнен
        $this->assertEquals($address->getFiasId(), $address->getAreaFiasId());
        $this->assertEquals($address->getKladrId(), $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());

        // все остальные уровни пустые
        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCity());
        $this->assertNull($address->getCityWithType());
        $this->assertNull($address->getCityWithFullType());

        $this->assertNull($address->getSettlementFiasId());
        $this->assertNull($address->getSettlementKladrId());
        $this->assertNull($address->getSettlementType());
        $this->assertNull($address->getSettlementTypeFull());
        $this->assertNull($address->getSettlement());
        $this->assertNull($address->getSettlementWithType());
        $this->assertNull($address->getSettlementWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getStreetKladrId());
        $this->assertNull($address->getStreetType());
        $this->assertNull($address->getStreetTypeFull());
        $this->assertNull($address->getStreet());
        $this->assertNull($address->getStreetWithType());
        $this->assertNull($address->getStreetWithFullType());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getFiasId());
        $this->assertEquals(36249, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::ADMINISTRATIVE_REGION, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::AREA, $address->getAddressLevel());
        $this->assertEquals('0203100000000', $address->getKladrId());
        $this->assertEquals('80237000000', $address->getOkato());
        $this->assertNull($address->getOktmo());
        $this->assertEquals('452930', $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsCity(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 6143,
                'path_ltree' => '5705.6143',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals('респ. Башкортостан, г. Нефтекамск', $address->getShortString());

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());

        // район для города пропущен (не для всех вроде так должно быть)
        $this->assertNull($address->getAreaFiasId());
        $this->assertNull($address->getAreaKladrId());
        $this->assertNull($address->getAreaType());
        $this->assertNull($address->getAreaTypeFull());
        $this->assertNull($address->getArea());
        $this->assertNull($address->getAreaWithType());
        $this->assertNull($address->getAreaWithFullType());

        // соответствующий уровень заполнен
        $this->assertEquals($address->getFiasId(), $address->getCityFiasId());
        $this->assertEquals($address->getKladrId(), $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());

        // все остальные уровни пустые
        $this->assertNull($address->getSettlementFiasId());
        $this->assertNull($address->getSettlementKladrId());
        $this->assertNull($address->getSettlementType());
        $this->assertNull($address->getSettlementTypeFull());
        $this->assertNull($address->getSettlement());
        $this->assertNull($address->getSettlementWithType());
        $this->assertNull($address->getSettlementWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getStreetKladrId());
        $this->assertNull($address->getStreetType());
        $this->assertNull($address->getStreetTypeFull());
        $this->assertNull($address->getStreet());
        $this->assertNull($address->getStreetWithType());
        $this->assertNull($address->getStreetWithFullType());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getFiasId());
        $this->assertEquals(6143, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::CITY, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::CITY, $address->getAddressLevel());
        $this->assertEquals('0200000300000', $address->getKladrId());
        $this->assertEquals('80427000000', $address->getOkato());
        $this->assertEquals('80727000001', $address->getOktmo());
        $this->assertEquals(null, $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsCitySettlement(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'objects' => '[{"object_id":5512,"types":["addr_obj"],"relations":[{"id": 6118, "data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 6108, "data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5512,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452680", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300401", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200000300402", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, дер. Крым-Сараево (бывш. Крымсараево)',
            $address->getShortString(true)
        );

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // для нас. пунктов внутри города - город заполнен
        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getCityFiasId());
        $this->assertEquals('0200000300000', $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());
        $this->assertEquals('г. Нефтекамск', $address->getCityWithType());
        $this->assertEquals('город Нефтекамск', $address->getCityWithFullType());

        $this->assertEquals('f5b6853e-7787-4127-b60a-a2bcc96a9b3f', $address->getSettlementFiasId());
        $this->assertEquals('0200000300400', $address->getSettlementKladrId());
        $this->assertEquals('дер.', $address->getSettlementType());
        $this->assertEquals('деревня', $address->getSettlementTypeFull());
        $this->assertEquals('Крым-Сараево', $address->getSettlement());

        // район не заполнен
        $this->assertNull($address->getAreaFiasId());
        $this->assertNull($address->getAreaKladrId());
        $this->assertNull($address->getAreaType());
        $this->assertNull($address->getAreaTypeFull());
        $this->assertNull($address->getArea());
        $this->assertNull($address->getAreaWithType());
        $this->assertNull($address->getAreaWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        // все остальные уровни пустые
        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getStreetKladrId());
        $this->assertNull($address->getStreetType());
        $this->assertNull($address->getStreetTypeFull());
        $this->assertNull($address->getStreet());
        $this->assertNull($address->getStreetWithType());
        $this->assertNull($address->getStreetWithFullType());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('f5b6853e-7787-4127-b60a-a2bcc96a9b3f', $address->getFiasId());
        $this->assertEquals(5512, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::SETTLEMENT, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::SETTLEMENT, $address->getAddressLevel());
        $this->assertEquals('0200000300400', $address->getKladrId());
        $this->assertEquals('80427807004', $address->getOkato());
        $this->assertEquals('80727000121', $address->getOktmo());
        $this->assertEquals(null, $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsCityFlat(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 69611691,
                'path_ltree' => '5705.6143.7280.69610029.69611691',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":7280,"types":["addr_obj"],"relations":[{"id": 8472, "data": {"id": 8472, "name": "Социалистическая", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 23087, "isactive": 1, "isactual": 1, "objectid": 7280, "typename": "ул", "startdate": "1900-01-01", "objectguid": "b008fb9b-72d8-4949-9eef-d1935589e84d", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":69610029,"types":["house"],"relations":[{"id": 41986684, "data": {"id": 41986684, "nextid": 67480068, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2016-08-31", "addtype1": null, "addtype2": null, "changeid": 103747162, "housenum": "18", "isactive": 0, "isactual": 0, "objectid": 69610029, "housetype": 2, "startdate": "1900-01-01", "objectguid": "e3463736-aaa5-4759-b609-a37a2696fe7f", "opertypeid": 10, "updatedate": "2019-07-10"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 67480068, "data": {"id": 67480068, "nextid": 0, "previd": 41986684, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 103747177, "housenum": "18", "isactive": 1, "isactual": 1, "objectid": 69610029, "housetype": 2, "startdate": "2016-08-31", "objectguid": "e3463736-aaa5-4759-b609-a37a2696fe7f", "opertypeid": 20, "updatedate": "2016-08-31"}, "type": "house", "is_active": 1, "is_actual": 1}]},{"object_id":69611691,"types":["apartment"],"relations":[{"id": 41515966, "data": {"id": 41515966, "nextid": 0, "number": "1", "previd": 0, "enddate": "2079-06-06", "changeid": 103749605, "isactive": 1, "isactual": 1, "objectid": 69611691, "aparttype": 2, "startdate": "2017-05-02", "objectguid": "280df371-0124-45ea-947a-b7b67052c8ee", "opertypeid": 10, "updatedate": "2019-06-20"}, "type": "apartment", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":7280,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020000030000002", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0002", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000003000000201", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000000000000200", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000003000000202", "type_id": 10, "end_date": "2018-07-10", "is_actual": false, "start_date": "2016-08-31"},{"value": "02000003000000200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-10"},{"value": "807270000010000000200", "type_id": 13, "end_date": "2018-07-10", "is_actual": false, "start_date": "2016-08-31"},{"value": "807270000010000000201", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-10"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":69610029,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452684", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "154", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000220154000000005", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000220154000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":69611691,"values":[{"value": "02:66:010105:3137", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-05-02"},{"value": "807270000010000000240154000000005", "type_id": 13, "end_date": "2017-05-02", "is_actual": false, "start_date": "2017-05-02"},{"value": "807270000010000000240154000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-05-02"},{"value": "452684", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-05-02"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, ул. Социалистическая, д. 18, кв. 1',
            $address->getShortString()
        );

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // район для города пропущен (не для всех вроде так должно быть)
        $this->assertNull($address->getAreaFiasId());
        $this->assertNull($address->getAreaKladrId());
        $this->assertNull($address->getAreaType());
        $this->assertNull($address->getAreaTypeFull());
        $this->assertNull($address->getArea());
        $this->assertNull($address->getAreaWithType());
        $this->assertNull($address->getAreaWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getCityFiasId());
        $this->assertEquals('0200000300000', $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());
        $this->assertEquals('г. Нефтекамск', $address->getCityWithType());
        $this->assertEquals('город Нефтекамск', $address->getCityWithFullType());

        $this->assertEquals('b008fb9b-72d8-4949-9eef-d1935589e84d', $address->getStreetFiasId());
        $this->assertEquals('02000003000000200', $address->getStreetKladrId());
        $this->assertEquals('ул.', $address->getStreetType());
        $this->assertEquals('улица', $address->getStreetTypeFull());
        $this->assertEquals('Социалистическая', $address->getStreet());
        $this->assertEquals('ул. Социалистическая', $address->getStreetWithType());
        $this->assertEquals('улица Социалистическая', $address->getStreetWithFullType());

        $this->assertEquals('e3463736-aaa5-4759-b609-a37a2696fe7f', $address->getHouseFiasId());
        $this->assertEquals(null, $address->getHouseKladrId());
        $this->assertEquals('д.', $address->getHouseType());
        $this->assertEquals('дом', $address->getHouseTypeFull());
        $this->assertEquals('18', $address->getHouse());

        // без строений
        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        // соответствующий уровень заполнен
        $this->assertEquals('280df371-0124-45ea-947a-b7b67052c8ee', $address->getFlatFiasId());
        $this->assertEquals('кв.', $address->getFlatType());
        $this->assertEquals('квартира', $address->getFlatTypeFull());
        $this->assertEquals('1', $address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('280df371-0124-45ea-947a-b7b67052c8ee', $address->getFiasId());
        $this->assertEquals(69611691, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::PREMISES, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::FLAT, $address->getAddressLevel());
        $this->assertEquals(null, $address->getKladrId());
        $this->assertEquals(null, $address->getOkato());
        $this->assertEquals(null, $address->getOktmo());
        $this->assertEquals('452684', $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsAreaSettlement(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 37631,
                'path_ltree' => '5705.36249.37631',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":36249,"types":["addr_obj"],"relations":[{"id": 42085, "data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":37631,"types":["addr_obj"],"relations":[{"id": 43639, "data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":36249,"values":[{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452930", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":37631,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0203100000301", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals('респ. Башкортостан, Краснокамский р-н, с. Куяново', $address->getShortString());

        // город пуст
        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCity());
        $this->assertNull($address->getCityWithType());
        $this->assertNull($address->getCityWithFullType());

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // для нас. пунктов внутри района - район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());
        $this->assertEquals('Краснокамский р-н', $address->getAreaWithType());
        $this->assertEquals('Краснокамский район', $address->getAreaWithFullType());

        // соответствующий уровень заполнен
        $this->assertEquals($address->getFiasId(), $address->getSettlementFiasId());
        $this->assertEquals($address->getKladrId(), $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());
        $this->assertEquals('с. Куяново', $address->getSettlementWithType());
        $this->assertEquals('село Куяново', $address->getSettlementWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        // все остальные уровни пустые
        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getStreetKladrId());
        $this->assertNull($address->getStreetType());
        $this->assertNull($address->getStreetTypeFull());
        $this->assertNull($address->getStreet());
        $this->assertNull($address->getStreetWithType());
        $this->assertNull($address->getStreetWithFullType());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getFiasId());
        $this->assertEquals(37631, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::SETTLEMENT, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::SETTLEMENT, $address->getAddressLevel());
        $this->assertEquals('0203100000300', $address->getKladrId());
        $this->assertEquals('80237812001', $address->getOkato());
        $this->assertEquals('80637412101', $address->getOktmo());
        $this->assertEquals('452946', $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsSettlementStreet(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 38528,
                'path_ltree' => '5705.36249.37631.38528',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":36249,"types":["addr_obj"],"relations":[{"id": 42085, "data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":37631,"types":["addr_obj"],"relations":[{"id": 43639, "data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":38528,"types":["addr_obj"],"relations":[{"id": 44686, "data": {"id": 44686, "name": "Комсомольский", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 102031, "isactive": 1, "isactual": 1, "objectid": 38528, "typename": "пр-кт", "startdate": "1900-01-01", "objectguid": "c876fdd0-5f9c-4389-9d98-f1bff7640520", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":36249,"values":[{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452930", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":37631,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0203100000301", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":38528,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020310000030019", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0019", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "02031000003001901", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "02031000003001900", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000001900", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000001901", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, Краснокамский р-н, с. Куяново, пр-кт Комсомольский',
            $address->getShortString()
        );

        // город пуст
        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCity());

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // для поселков и тд район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());
        $this->assertEquals('Краснокамский р-н', $address->getAreaWithType());
        $this->assertEquals('Краснокамский район', $address->getAreaWithFullType());

        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getSettlementFiasId());
        $this->assertEquals('0203100000300', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());
        $this->assertEquals('с. Куяново', $address->getSettlementWithType());
        $this->assertEquals('село Куяново', $address->getSettlementWithFullType());

        // соответствующий уровень заполнен
        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getStreetFiasId());
        $this->assertEquals('02031000003001900', $address->getStreetKladrId());
        $this->assertEquals('пр-кт', $address->getStreetType());
        $this->assertEquals('проспект', $address->getStreetTypeFull());
        $this->assertEquals('Комсомольский', $address->getStreet());
        $this->assertEquals('пр-кт Комсомольский', $address->getStreetWithType());
        $this->assertEquals('проспект Комсомольский', $address->getStreetWithFullType());

        // все остальные уровни пустые
        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getFiasId());
        $this->assertEquals(38528, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::ROAD_NETWORK_ELEMENT, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::STREET, $address->getAddressLevel());
        $this->assertEquals('02031000003001900', $address->getKladrId());
        $this->assertEquals('80237812001', $address->getOkato());
        $this->assertEquals('80637412101', $address->getOktmo());
        $this->assertEquals('452946', $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsSettlementHouse(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 79959421,
                'path_ltree' => '5705.36249.37631.38528.79959421',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":36249,"types":["addr_obj"],"relations":[{"id": 42085, "data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":37631,"types":["addr_obj"],"relations":[{"id": 43639, "data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":38528,"types":["addr_obj"],"relations":[{"id": 44686, "data": {"id": 44686, "name": "Комсомольский", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 102031, "isactive": 1, "isactual": 1, "objectid": 38528, "typename": "пр-кт", "startdate": "1900-01-01", "objectguid": "c876fdd0-5f9c-4389-9d98-f1bff7640520", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":79959421,"types":["house"],"relations":[{"id": 48349501, "data": {"id": 48349501, "nextid": 69241846, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2019-02-13", "addtype1": null, "addtype2": null, "changeid": 118837036, "housenum": "33", "isactive": 0, "isactual": 0, "objectid": 79959421, "housetype": 2, "startdate": "1900-01-01", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 10, "updatedate": "2019-02-16"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 69241846, "data": {"id": 69241846, "nextid": 0, "previd": 48349501, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 118837148, "housenum": "33", "isactive": 1, "isactual": 1, "objectid": 79959421, "housetype": 2, "startdate": "2019-02-13", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 20, "updatedate": "2019-02-16"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":36249,"values":[{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452930", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":37631,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0203100000301", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":38528,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020310000030019", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0019", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "02031000003001901", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "02031000003001900", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000001900", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000001901", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":79959421,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "47", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02:33:200108:122", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-02-13"},{"value": "806374121010000001920047000000005", "type_id": 13, "end_date": "2019-02-13", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000001920047000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-02-13"},{"value": "2", "type_id": 14, "end_date": "2019-02-13", "is_actual": false, "start_date": "1900-01-01"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-02-13"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, Краснокамский р-н, с. Куяново, пр-кт Комсомольский, д. 33',
            $address->getShortString()
        );

        // город пуст
        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCity());

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // для поселков и тд район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());
        $this->assertEquals('Краснокамский р-н', $address->getAreaWithType());
        $this->assertEquals('Краснокамский район', $address->getAreaWithFullType());

        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getSettlementFiasId());
        $this->assertEquals('0203100000300', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());
        $this->assertEquals('с. Куяново', $address->getSettlementWithType());
        $this->assertEquals('село Куяново', $address->getSettlementWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getStreetFiasId());
        $this->assertEquals('02031000003001900', $address->getStreetKladrId());
        $this->assertEquals('пр-кт', $address->getStreetType());
        $this->assertEquals('проспект', $address->getStreetTypeFull());
        $this->assertEquals('Комсомольский', $address->getStreet());
        $this->assertEquals('пр-кт Комсомольский', $address->getStreetWithType());
        $this->assertEquals('проспект Комсомольский', $address->getStreetWithFullType());

        // соответствующий уровень заполнен
        $this->assertEquals('fc29d0da-e0aa-43a2-bd0e-4466332633aa', $address->getHouseFiasId());
        $this->assertEquals(null, $address->getHouseKladrId());
        $this->assertEquals('д.', $address->getHouseType());
        $this->assertEquals('дом', $address->getHouseTypeFull());
        $this->assertEquals('33', $address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('fc29d0da-e0aa-43a2-bd0e-4466332633aa', $address->getFiasId());
        $this->assertEquals(79959421, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::BUILDING, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::HOUSE, $address->getAddressLevel());
        $this->assertEquals(null, $address->getKladrId());
        $this->assertEquals('80237812001', $address->getOkato());
        $this->assertEquals('80637412101', $address->getOktmo());
        $this->assertEquals('452946', $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsSettlementFlat(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 79960688,
                'path_ltree' => '5705.36249.37631.38528.79959421.79960688',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":36249,"types":["addr_obj"],"relations":[{"id": 42085, "data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":37631,"types":["addr_obj"],"relations":[{"id": 43639, "data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":38528,"types":["addr_obj"],"relations":[{"id": 44686, "data": {"id": 44686, "name": "Комсомольский", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 102031, "isactive": 1, "isactual": 1, "objectid": 38528, "typename": "пр-кт", "startdate": "1900-01-01", "objectguid": "c876fdd0-5f9c-4389-9d98-f1bff7640520", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":79959421,"types":["house"],"relations":[{"id": 48349501, "data": {"id": 48349501, "nextid": 69241846, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2019-02-13", "addtype1": null, "addtype2": null, "changeid": 118837036, "housenum": "33", "isactive": 0, "isactual": 0, "objectid": 79959421, "housetype": 2, "startdate": "1900-01-01", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 10, "updatedate": "2019-02-16"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 69241846, "data": {"id": 69241846, "nextid": 0, "previd": 48349501, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 118837148, "housenum": "33", "isactive": 1, "isactual": 1, "objectid": 79959421, "housetype": 2, "startdate": "2019-02-13", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 20, "updatedate": "2019-02-16"}, "type": "house", "is_active": 1, "is_actual": 1}]},{"object_id":79960688,"types":["apartment"],"relations":[{"id": 47823043, "data": {"id": 47823043, "nextid": 0, "number": "2", "previd": 0, "enddate": "2079-06-06", "changeid": 118838821, "isactive": 1, "isactual": 1, "objectid": 79960688, "aparttype": 2, "startdate": "2017-06-01", "objectguid": "87d9a47b-7f3b-4860-ad59-470f29ece6d6", "opertypeid": 10, "updatedate": "2019-02-13"}, "type": "apartment", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":36249,"values":[{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452930", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":37631,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0203100000301", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":38528,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020310000030019", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0019", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "02031000003001901", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "02031000003001900", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806374120000000001900", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000001901", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":79959421,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0231", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "47", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02:33:200108:122", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-02-13"},{"value": "806374121010000001920047000000005", "type_id": 13, "end_date": "2019-02-13", "is_actual": false, "start_date": "1900-01-01"},{"value": "806374121010000001920047000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-02-13"},{"value": "2", "type_id": 14, "end_date": "2019-02-13", "is_actual": false, "start_date": "1900-01-01"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-02-13"}]},{"object_id":79960688,"values":[{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-06-01"},{"value": "02:33:200108:141", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-06-01"},{"value": "806374121010000001940047000000005", "type_id": 13, "end_date": "2017-06-01", "is_actual": false, "start_date": "2017-06-01"},{"value": "806374121010000001940047000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-06-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, Краснокамский р-н, с. Куяново, пр-кт Комсомольский, д. 33, кв. 2',
            $address->getShortString()
        );

        // город пуст
        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCity());

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // для поселков и тд район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());
        $this->assertEquals('Краснокамский р-н', $address->getAreaWithType());
        $this->assertEquals('Краснокамский район', $address->getAreaWithFullType());

        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getSettlementFiasId());
        $this->assertEquals('0203100000300', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());
        $this->assertEquals('с. Куяново', $address->getSettlementWithType());
        $this->assertEquals('село Куяново', $address->getSettlementWithFullType());

        $this->assertNull($address->getTerritoryFiasId());
        $this->assertNull($address->getTerritoryKladrId());
        $this->assertNull($address->getTerritoryType());
        $this->assertNull($address->getTerritoryTypeFull());
        $this->assertNull($address->getTerritory());
        $this->assertNull($address->getTerritoryWithType());
        $this->assertNull($address->getTerritoryWithFullType());

        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getStreetFiasId());
        $this->assertEquals('02031000003001900', $address->getStreetKladrId());
        $this->assertEquals('пр-кт', $address->getStreetType());
        $this->assertEquals('проспект', $address->getStreetTypeFull());
        $this->assertEquals('Комсомольский', $address->getStreet());
        $this->assertEquals('пр-кт Комсомольский', $address->getStreetWithType());
        $this->assertEquals('проспект Комсомольский', $address->getStreetWithFullType());

        $this->assertEquals('fc29d0da-e0aa-43a2-bd0e-4466332633aa', $address->getHouseFiasId());
        $this->assertEquals(null, $address->getHouseKladrId());
        $this->assertEquals('д.', $address->getHouseType());
        $this->assertEquals('дом', $address->getHouseTypeFull());
        $this->assertEquals('33', $address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        // соответствующий уровень заполнен
        $this->assertEquals('87d9a47b-7f3b-4860-ad59-470f29ece6d6', $address->getFlatFiasId());
        $this->assertEquals('кв.', $address->getFlatType());
        $this->assertEquals('квартира', $address->getFlatTypeFull());
        $this->assertEquals('2', $address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('87d9a47b-7f3b-4860-ad59-470f29ece6d6', $address->getFiasId());
        $this->assertEquals(79960688, $address->getFiasObjectId());
        $this->assertEquals(FiasLevel::PREMISES, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::FLAT, $address->getAddressLevel());
        $this->assertEquals(null, $address->getKladrId());
        $this->assertEquals(null, $address->getOkato());
        $this->assertEquals(null, $address->getOktmo());
        $this->assertEquals('452946', $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsFlatWithAdditionalNumbers(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 70027141,
                'path_ltree' => '5705.6143.7280.70027141',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":7280,"types":["addr_obj"],"relations":[{"id": 8472, "data": {"id": 8472, "name": "Социалистическая", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 23087, "isactive": 1, "isactual": 1, "objectid": 7280, "typename": "ул", "startdate": "1900-01-01", "objectguid": "b008fb9b-72d8-4949-9eef-d1935589e84d", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":70027141,"types":["house"],"relations":[{"id": 42233509, "data": {"id": 42233509, "nextid": 0, "previd": 0, "addnum1": "4", "addnum2": null, "enddate": "2079-06-06", "addtype1": 2, "addtype2": null, "changeid": 104351076, "housenum": "10А", "isactive": 1, "isactual": 1, "objectid": 70027141, "housetype": 5, "startdate": "2019-05-30", "objectguid": "b9433c6d-574a-4224-8197-0f01a5671f68", "opertypeid": 10, "updatedate": "2019-05-30"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":7280,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020000030000002", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0002", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000003000000201", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000000000000200", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000003000000202", "type_id": 10, "end_date": "2018-07-10", "is_actual": false, "start_date": "2016-08-31"},{"value": "02000003000000200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-10"},{"value": "807270000010000000200", "type_id": 13, "end_date": "2018-07-10", "is_actual": false, "start_date": "2016-08-31"},{"value": "807270000010000000201", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-10"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":70027141,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "02:66:010101:992", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "807270000010000000220352000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "352", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-30"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2019-05-30"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, ул. Социалистическая, зд. 10А, стр. 4',
            $address->getShortString()
        );

        // соответствующий уровень заполнен
        $this->assertEquals('b9433c6d-574a-4224-8197-0f01a5671f68', $address->getHouseFiasId());
        $this->assertEquals(null, $address->getHouseKladrId());
        $this->assertEquals('зд.', $address->getHouseType());
        $this->assertEquals('здание', $address->getHouseTypeFull());
        $this->assertEquals('10А', $address->getHouse());

        // литера, корпус, сооружение
        $this->assertEquals('стр.', $address->getBlockType1());
        $this->assertEquals('строение', $address->getBlockTypeFull1());
        $this->assertEquals('4', $address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $address = $this->builder->build(
            [
                'object_id' => 36105517,
                'path_ltree' => '5705.6177.7215.36105517',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6177,"types":["addr_obj"],"relations":[{"id": 6940, "data": {"id": 6940, "name": "Кумертау", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19453, "isactive": 1, "isactual": 1, "objectid": 6177, "typename": "г", "startdate": "1900-01-01", "objectguid": "48e38991-07fd-4aaa-b240-a7280e4a823f", "opertypeid": 1, "updatedate": "2013-01-07"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":7215,"types":["addr_obj"],"relations":[{"id": 8388, "data": {"id": 8388, "name": "Брикетная", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 22885, "isactive": 1, "isactual": 1, "objectid": 7215, "typename": "ул", "startdate": "1900-01-01", "objectguid": "d4fd2f5a-8c4a-4b05-b8d1-6b0c14f9a392", "opertypeid": 1, "updatedate": "2013-01-07"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":36105517,"types":["house"],"relations":[{"id": 21463473, "data": {"id": 21463473, "nextid": 0, "previd": 0, "addnum1": "А", "addnum2": "1/6", "enddate": "2079-06-06", "addtype1": 1, "addtype2": 2, "changeid": 54819930, "housenum": "5", "isactive": 1, "isactual": 1, "objectid": 36105517, "housetype": 1, "startdate": "2015-07-09", "objectguid": "f581b200-3843-4cc6-baba-c35efe08f5a5", "opertypeid": 10, "updatedate": "2019-07-10"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республик Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6177,"values":[{"value": "80423000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000007000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0262", "type_id": 1, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0261", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "0262", "type_id": 2, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0261", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "0262", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "0200000700001", "type_id": 10, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000700000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "807230000000000000000", "type_id": 13, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "807230000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "80723000", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80723000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":7215,"values":[{"value": "80423000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "453303", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020000070000014", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0014", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0262", "type_id": 1, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0261", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "0262", "type_id": 2, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0261", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "0262", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "0262", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "80723000", "type_id": 7, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000007000001401", "type_id": 10, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000007000001400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "807230000000000001400", "type_id": 13, "end_date": "2013-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "807230000010000001401", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2013-01-01"},{"value": "80723000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2013-01-01"},{"value": "80723000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":36105517,"values":[{"value": "0261", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "0261", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "0262", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "0262", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "80423000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "807230000010000001420012000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "12", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-09"},{"value": "80723000", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2015-07-09"},{"value": "80723000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"},{"value": "453303", "type_id": 5, "end_date": "2020-08-15", "is_actual": false, "start_date": "2015-07-09"},{"value": "453310", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-08-15"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Кумертау, ул. Брикетная, влд. 5, корп. А, стр. 1/6',
            $address->getShortString()
        );

        // соответствующий уровень заполнен
        $this->assertEquals('f581b200-3843-4cc6-baba-c35efe08f5a5', $address->getHouseFiasId());
        $this->assertEquals(null, $address->getHouseKladrId());
        $this->assertEquals('влд.', $address->getHouseType());
        $this->assertEquals('владение', $address->getHouseTypeFull());
        $this->assertEquals('5', $address->getHouse());

        // литера, корпус, сооружение
        $this->assertEquals('корп.', $address->getBlockType1());
        $this->assertEquals('корпус', $address->getBlockTypeFull1());
        $this->assertEquals('А', $address->getBlock1());

        $this->assertEquals('стр.', $address->getBlockType2());
        $this->assertEquals('строение', $address->getBlockTypeFull2());
        $this->assertEquals('1/6', $address->getBlock2());

        $address = $this->builder->build(
            [
                'object_id' => 80354205,
                'path_ltree' => '1325381.1325680.1329639.80354205',
                'objects' => '[{"object_id":1325381,"types":["addr_obj"],"relations":[{"id": 1637437, "data": {"id": 1637437, "name": "Ульяновская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630187, "isactive": 1, "isactual": 1, "objectid": 1325381, "typename": "обл", "startdate": "1900-01-01", "objectguid": "fee76045-fe22-43a4-ad58-ad99e903bd58", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1325680,"types":["addr_obj"],"relations":[{"id": 1637755, "data": {"id": 1637755, "name": "Ульяновск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630950, "isactive": 1, "isactual": 1, "objectid": 1325680, "typename": "г", "startdate": "1900-01-01", "objectguid": "bebfd75d-a0da-4bf9-8307-2e2c85eac463", "opertypeid": 1, "updatedate": "2018-10-26"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1329639,"types":["addr_obj"],"relations":[{"id": 1642081, "data": {"id": 1642081, "name": "Московское", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3640299, "isactive": 1, "isactual": 1, "objectid": 1329639, "typename": "ш", "startdate": "1900-01-01", "objectguid": "5040339e-9130-490e-bd7a-f209dd36a4a4", "opertypeid": 1, "updatedate": "2015-11-29"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":80354205,"types":["house"],"relations":[{"id": 48600378, "data": {"id": 48600378, "nextid": 70331499, "previd": 0, "addnum1": "6", "addnum2": null, "enddate": "2021-02-04", "addtype1": 2, "addtype2": null, "changeid": 119417796, "housenum": "9А/2", "isactive": 0, "isactual": 0, "objectid": 80354205, "housetype": 5, "startdate": "2019-01-30", "objectguid": "fd7c161b-0765-4e54-9517-1c49f50e03ce", "opertypeid": 10, "updatedate": "2021-02-04"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 70331499, "data": {"id": 70331499, "nextid": null, "previd": 48600378, "addnum1": "2", "addnum2": "Б,б,б1,Л", "enddate": "2079-06-06", "addtype1": 1, "addtype2": 4, "changeid": 174547397, "housenum": "9-А", "isactive": 1, "isactual": 1, "objectid": 80354205, "housetype": 2, "startdate": "2021-02-04", "objectguid": "fd7c161b-0765-4e54-9517-1c49f50e03ce", "opertypeid": 20, "updatedate": "2021-02-04"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":1325381,"values":[{"value": "Ульяновская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "433000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "730000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":1325680,"values":[{"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-02-11"},{"value": "73401000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300000100000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000001000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "737010000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73701000", "type_id": 7, "end_date": "2021-02-11", "is_actual": false, "start_date": "1900-01-01"}]},{"object_id":1329639,"values":[{"value": "7327", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7327", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73701000", "type_id": 7, "end_date": "2020-07-07", "is_actual": false, "start_date": "1900-01-01"},{"value": "730000010000766", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0766", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73401373000", "type_id": 6, "end_date": "2015-11-25", "is_actual": false, "start_date": "1900-01-01"},{"value": "73000001000076601", "type_id": 10, "end_date": "2015-11-25", "is_actual": false, "start_date": "1900-01-01"},{"value": "73000001000076600", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-11-25"},{"value": "737010000000000076600", "type_id": 13, "end_date": "2015-11-25", "is_actual": false, "start_date": "1900-01-01"},{"value": "737010000000000076601", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-11-25"},{"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-07"},{"value": "73401373000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-08"}]},{"object_id":80354205,"values":[{"value": "737010000000000076620309000000010", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-02-04"},{"value": "7327", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "7327", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "73401373000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "73:24:030803:854", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "309", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-01-30"},{"value": "737010000000000076620309000000005", "type_id": 13, "end_date": "2021-02-04", "is_actual": true, "start_date": "2019-01-30"},{"value": "432010", "type_id": 5, "end_date": "2020-08-15", "is_actual": false, "start_date": "2019-01-30"},{"value": "432045", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-08-15"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Ульяновская обл., г. Ульяновск, Московское ш., д. 9-А, корп. 2, лит. Б,б,б1,Л',
            $address->getShortString()
        );

        // соответствующий уровень заполнен
        $this->assertEquals('fd7c161b-0765-4e54-9517-1c49f50e03ce', $address->getHouseFiasId());
        $this->assertEquals(null, $address->getHouseKladrId());
        $this->assertEquals('д.', $address->getHouseType());
        $this->assertEquals('дом', $address->getHouseTypeFull());
        $this->assertEquals('9-А', $address->getHouse());

        // литера, корпус, сооружение
        $this->assertEquals('корп.', $address->getBlockType1());
        $this->assertEquals('корпус', $address->getBlockTypeFull1());
        $this->assertEquals('2', $address->getBlock1());

        $this->assertEquals('лит.', $address->getBlockType2());
        $this->assertEquals('литера', $address->getBlockTypeFull2());
        $this->assertEquals('Б,б,б1,Л', $address->getBlock2());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsTerritory(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 11454,
                'path_ltree' => '5705.6143.5791.11454',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":5791,"types":["addr_obj"],"relations":[{"id": 6473, "data": {"id": 6473, "name": "Энергетик", "level": "6", "nextid": 0, "previd": 6468, "enddate": "2079-06-06", "changeid": 18238, "isactive": 1, "isactual": 1, "objectid": 5791, "typename": "с", "startdate": "2014-08-15", "objectguid": "0823f2aa-86e2-4584-8523-5f487fff95ab", "opertypeid": 20, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 6468, "data": {"id": 6468, "name": "Энергетик", "level": "6", "nextid": 6473, "previd": 6460, "enddate": "2014-08-15", "changeid": 18221, "isactive": 0, "isactual": 0, "objectid": 5791, "typename": "п", "startdate": "2013-10-30", "objectguid": "0823f2aa-86e2-4584-8523-5f487fff95ab", "opertypeid": 20, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 0, "is_actual": 0},{"id": 6460, "data": {"id": 6460, "name": "Энергетик", "level": "6", "nextid": 6468, "previd": 0, "enddate": "2013-10-30", "changeid": 18195, "isactive": 0, "isactual": 0, "objectid": 5791, "typename": "с", "startdate": "1900-01-01", "objectguid": "0823f2aa-86e2-4584-8523-5f487fff95ab", "opertypeid": 1, "updatedate": "2014-08-20"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":11454,"types":["addr_obj"],"relations":[{"id": 13707, "data": {"id": 13707, "name": "Родничок", "level": "7", "nextid": 0, "previd": 13698, "enddate": "2079-06-06", "changeid": 33103, "isactive": 1, "isactual": 1, "objectid": 11454, "typename": "тер. СНТ", "startdate": "2018-09-20", "objectguid": "a4697fc8-eced-4078-881c-2d400a12af21", "opertypeid": 20, "updatedate": "2018-12-28"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 13698, "data": {"id": 13698, "name": "СНТ Родничок", "level": "7", "nextid": 13707, "previd": 13676, "enddate": "2018-09-20", "changeid": 33093, "isactive": 0, "isactual": 0, "objectid": 11454, "typename": "снт", "startdate": "2016-09-28", "objectguid": "a4697fc8-eced-4078-881c-2d400a12af21", "opertypeid": 50, "updatedate": "2018-09-25"}, "type": "addr_obj", "is_active": 0, "is_actual": 0},{"id": 13676, "data": {"id": 13676, "name": "СНТ Родничок", "level": "15", "nextid": 13698, "previd": 0, "enddate": "2016-09-28", "changeid": 33068, "isactive": 0, "isactual": 0, "objectid": 11454, "typename": "снт", "startdate": "2016-03-18", "objectguid": "a4697fc8-eced-4078-881c-2d400a12af21", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":5791,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003006", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300601", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200000300602", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "2013-10-30"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300603", "type_id": 10, "end_date": "2014-08-15", "is_actual": false, "start_date": "2014-01-05"},{"value": "0200000300600", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-08-15"},{"value": "807270001310000000000", "type_id": 13, "end_date": "2014-08-15", "is_actual": false, "start_date": "2014-01-05"},{"value": "807270001310000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-08-15"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]},{"object_id":11454,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-03-18"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-03-18"},{"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-03-18"},{"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-03-18"},{"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-03-18"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-03-18"},{"value": "02000003006000651", "type_id": 10, "end_date": "2016-09-28", "is_actual": false, "start_date": "2016-03-18"},{"value": "807270001310000000000", "type_id": 13, "end_date": "2016-09-28", "is_actual": false, "start_date": "2016-03-18"},{"value": "0006", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-28"},{"value": "452697", "type_id": 5, "end_date": "2018-09-20", "is_actual": false, "start_date": "2016-03-18"},{"value": "02000003006000601", "type_id": 10, "end_date": "2018-09-20", "is_actual": false, "start_date": "2016-09-28"},{"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-09-20"},{"value": "807270001310006000000", "type_id": 13, "end_date": "2018-09-20", "is_actual": false, "start_date": "2016-09-28"},{"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-09-20"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, с. Энергетик, тер. СНТ Родничок',
            $address->getShortString()
        );

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());
        $this->assertEquals('респ. Башкортостан', $address->getRegionWithType());
        $this->assertEquals('республика Башкортостан', $address->getRegionWithFullType());

        // для нас. пунктов внутри города - город заполнен
        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getCityFiasId());
        $this->assertEquals('0200000300000', $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());
        $this->assertEquals('г. Нефтекамск', $address->getCityWithType());
        $this->assertEquals('город Нефтекамск', $address->getCityWithFullType());

        $this->assertEquals('0823f2aa-86e2-4584-8523-5f487fff95ab', $address->getSettlementFiasId());
        $this->assertEquals('0200000300600', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Энергетик', $address->getSettlement());
        $this->assertEquals('с. Энергетик', $address->getSettlementWithType());
        $this->assertEquals('село Энергетик', $address->getSettlementWithFullType());

        $this->assertEquals('a4697fc8-eced-4078-881c-2d400a12af21', $address->getTerritoryFiasId());
        $this->assertEquals('02000003006000600', $address->getTerritoryKladrId());
        $this->assertEquals('тер. СНТ', $address->getTerritoryType());
        $this->assertEquals('территория садоводческих некоммерческих партнерств', $address->getTerritoryTypeFull());
        $this->assertEquals('Родничок', $address->getTerritory());
        $this->assertEquals('тер. СНТ Родничок', $address->getTerritoryWithType());
        $this->assertEquals(
            'территория садоводческих некоммерческих партнерств Родничок',
            $address->getTerritoryWithFullType()
        );

        // район не заполнен
        $this->assertNull($address->getAreaFiasId());
        $this->assertNull($address->getAreaKladrId());
        $this->assertNull($address->getAreaType());
        $this->assertNull($address->getAreaTypeFull());
        $this->assertNull($address->getArea());

        // все остальные уровни пустые
        $this->assertNull($address->getStreetFiasId());
        $this->assertNull($address->getStreetKladrId());
        $this->assertNull($address->getStreetType());
        $this->assertNull($address->getStreetTypeFull());
        $this->assertNull($address->getStreet());

        $this->assertNull($address->getHouseFiasId());
        $this->assertNull($address->getHouseKladrId());
        $this->assertNull($address->getHouseType());
        $this->assertNull($address->getHouseTypeFull());
        $this->assertNull($address->getHouse());

        $this->assertNull($address->getBlockType1());
        $this->assertNull($address->getBlockTypeFull1());
        $this->assertNull($address->getBlock1());

        $this->assertNull($address->getBlockType2());
        $this->assertNull($address->getBlockTypeFull2());
        $this->assertNull($address->getBlock2());

        $this->assertNull($address->getFlatFiasId());
        $this->assertNull($address->getFlatType());
        $this->assertNull($address->getFlatTypeFull());
        $this->assertNull($address->getFlat());

        $this->assertNull($address->getRoomFiasId());
        $this->assertNull($address->getRoomType());
        $this->assertNull($address->getRoomTypeFull());
        $this->assertNull($address->getRoom());

        // текущий уровень заполнен
        $this->assertEquals('a4697fc8-eced-4078-881c-2d400a12af21', $address->getFiasId());
        $this->assertEquals(11454, $address->getFiasObjectId());
        // остались на уровне поселения
        $this->assertEquals(FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::TERRITORY, $address->getAddressLevel());
        $this->assertEquals('02000003006000600', $address->getKladrId());
        $this->assertEquals('80427000003', $address->getOkato());
        $this->assertEquals('80727000131', $address->getOktmo());
        $this->assertEquals(null, $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsTerritoryStreet(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 989833,
                'path_ltree' => '976397.986313.986685.998330.989833',
                'objects' => '[{"object_id":976397,"types":["addr_obj"],"relations":[{"id": 1211099, "data": {"id": 1211099, "name": "Оренбургская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2683816, "isactive": 1, "isactual": 1, "objectid": 976397, "typename": "обл", "startdate": "1900-01-01", "objectguid": "8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986313,"types":["addr_obj"],"relations":[{"id": 1222392, "data": {"id": 1222392, "name": "Оренбургский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2706517, "isactive": 1, "isactual": 1, "objectid": 986313, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "214f9132-f231-4f81-89c1-1241a6fb003a", "opertypeid": 1, "updatedate": "2011-09-13"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986685,"types":["addr_obj"],"relations":[{"id": 1222809, "data": {"id": 1222809, "name": "Пригородный сельсовет", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2707361, "isactive": 1, "isactual": 1, "objectid": 986685, "typename": "тер", "startdate": "1900-01-01", "objectguid": "4a1fd887-b73d-4568-84ce-bddc6051a03e", "opertypeid": 1, "updatedate": "2014-01-05"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":989833,"types":["addr_obj"],"relations":[{"id": 1226333, "data": {"id": 1226333, "name": "Абрикосовая", "level": "8", "nextid": 0, "previd": 1226329, "enddate": "2079-06-06", "changeid": 2714338, "isactive": 1, "isactual": 1, "objectid": 989833, "typename": "ул", "startdate": "2016-09-29", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 51, "updatedate": "2017-04-17"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1226329, "data": {"id": 1226329, "name": "Абрикосовая", "level": "16", "nextid": 1226333, "previd": 0, "enddate": "2016-09-29", "changeid": 2714332, "isactive": 0, "isactual": 0, "objectid": 989833, "typename": "ул", "startdate": "1900-01-01", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 10, "updatedate": "2016-12-11"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":998330,"types":["addr_obj"],"relations":[{"id": 1235857, "data": {"id": 1235857, "name": "Кристалл", "level": "7", "nextid": 0, "previd": 1235812, "enddate": "2079-06-06", "changeid": 2734646, "isactive": 1, "isactual": 1, "objectid": 998330, "typename": "днп", "startdate": "2016-09-29", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 50, "updatedate": "2016-09-29"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1235812, "data": {"id": 1235812, "name": "Кристалл", "level": "15", "nextid": 1235857, "previd": 0, "enddate": "2016-09-29", "changeid": 2734553, "isactive": 0, "isactual": 0, "objectid": 998330, "typename": "днп", "startdate": "1900-01-01", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":976397,"values":[{"value": "Оренбургская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "530000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":986313,"values":[{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "461100", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 1, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "5601900000001", "type_id": 10, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "530000000000000000000", "type_id": 13, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "536340000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"}]},{"object_id":986685,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "5601900009201", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900009200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "536344480000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":989833,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092001700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "560190000920017", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007001701", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0017", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":998330,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "560190000920007", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092000751", "type_id": 10, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "56019000092000700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0007", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Оренбургская обл., Оренбургский р-н, тер. Пригородный сельсовет, днп Кристалл, ул. Абрикосовая',
            $address->getShortString()
        );
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsTerritoryHouse(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 52602183,
                'path_ltree' => '976397.986313.986685.998330.989833.52602183',
                'objects' => '[{"object_id":976397,"types":["addr_obj"],"relations":[{"id": 1211099, "data": {"id": 1211099, "name": "Оренбургская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2683816, "isactive": 1, "isactual": 1, "objectid": 976397, "typename": "обл", "startdate": "1900-01-01", "objectguid": "8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986313,"types":["addr_obj"],"relations":[{"id": 1222392, "data": {"id": 1222392, "name": "Оренбургский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2706517, "isactive": 1, "isactual": 1, "objectid": 986313, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "214f9132-f231-4f81-89c1-1241a6fb003a", "opertypeid": 1, "updatedate": "2011-09-13"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986685,"types":["addr_obj"],"relations":[{"id": 1222809, "data": {"id": 1222809, "name": "Пригородный сельсовет", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2707361, "isactive": 1, "isactual": 1, "objectid": 986685, "typename": "тер", "startdate": "1900-01-01", "objectguid": "4a1fd887-b73d-4568-84ce-bddc6051a03e", "opertypeid": 1, "updatedate": "2014-01-05"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":989833,"types":["addr_obj"],"relations":[{"id": 1226333, "data": {"id": 1226333, "name": "Абрикосовая", "level": "8", "nextid": 0, "previd": 1226329, "enddate": "2079-06-06", "changeid": 2714338, "isactive": 1, "isactual": 1, "objectid": 989833, "typename": "ул", "startdate": "2016-09-29", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 51, "updatedate": "2017-04-17"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1226329, "data": {"id": 1226329, "name": "Абрикосовая", "level": "16", "nextid": 1226333, "previd": 0, "enddate": "2016-09-29", "changeid": 2714332, "isactive": 0, "isactual": 0, "objectid": 989833, "typename": "ул", "startdate": "1900-01-01", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 10, "updatedate": "2016-12-11"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":998330,"types":["addr_obj"],"relations":[{"id": 1235857, "data": {"id": 1235857, "name": "Кристалл", "level": "7", "nextid": 0, "previd": 1235812, "enddate": "2079-06-06", "changeid": 2734646, "isactive": 1, "isactual": 1, "objectid": 998330, "typename": "днп", "startdate": "2016-09-29", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 50, "updatedate": "2016-09-29"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1235812, "data": {"id": 1235812, "name": "Кристалл", "level": "15", "nextid": 1235857, "previd": 0, "enddate": "2016-09-29", "changeid": 2734553, "isactive": 0, "isactual": 0, "objectid": 998330, "typename": "днп", "startdate": "1900-01-01", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":52602183,"types":["house"],"relations":[{"id": 31421349, "data": {"id": 31421349, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 78793554, "housenum": "2", "isactive": 1, "isactual": 1, "objectid": 52602183, "housetype": 2, "startdate": "1900-01-01", "objectguid": "0830319a-575c-4402-a037-59ff671e321f", "opertypeid": 10, "updatedate": "2017-12-18"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":976397,"values":[{"value": "Оренбургская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "530000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":986313,"values":[{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "461100", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 1, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "5601900000001", "type_id": 10, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "530000000000000000000", "type_id": 13, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "536340000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"}]},{"object_id":986685,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "5601900009201", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900009200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "536344480000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":989833,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092001700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "560190000920017", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007001701", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0017", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":998330,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "560190000920007", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092000751", "type_id": 10, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "56019000092000700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0007", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":52602183,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "536344481010007001720001000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Оренбургская обл., Оренбургский р-н, тер. Пригородный сельсовет, днп Кристалл, ул. Абрикосовая, д. 2',
            $address->getShortString()
        );
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsStamp(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 52602183,
                'path_ltree' => '976397.986313.986685.998330.989833.52602183',
                'objects' => '[{"object_id":976397,"types":["addr_obj"],"relations":[{"id": 1211099, "data": {"id": 1211099, "name": "Оренбургская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2683816, "isactive": 1, "isactual": 1, "objectid": 976397, "typename": "обл", "startdate": "1900-01-01", "objectguid": "8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986313,"types":["addr_obj"],"relations":[{"id": 1222392, "data": {"id": 1222392, "name": "Оренбургский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2706517, "isactive": 1, "isactual": 1, "objectid": 986313, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "214f9132-f231-4f81-89c1-1241a6fb003a", "opertypeid": 1, "updatedate": "2011-09-13"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986685,"types":["addr_obj"],"relations":[{"id": 1222809, "data": {"id": 1222809, "name": "Пригородный сельсовет", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2707361, "isactive": 1, "isactual": 1, "objectid": 986685, "typename": "тер", "startdate": "1900-01-01", "objectguid": "4a1fd887-b73d-4568-84ce-bddc6051a03e", "opertypeid": 1, "updatedate": "2014-01-05"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":989833,"types":["addr_obj"],"relations":[{"id": 1226333, "data": {"id": 1226333, "name": "Абрикосовая", "level": "8", "nextid": 0, "previd": 1226329, "enddate": "2079-06-06", "changeid": 2714338, "isactive": 1, "isactual": 1, "objectid": 989833, "typename": "ул", "startdate": "2016-09-29", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 51, "updatedate": "2017-04-17"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1226329, "data": {"id": 1226329, "name": "Абрикосовая", "level": "16", "nextid": 1226333, "previd": 0, "enddate": "2016-09-29", "changeid": 2714332, "isactive": 0, "isactual": 0, "objectid": 989833, "typename": "ул", "startdate": "1900-01-01", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 10, "updatedate": "2016-12-11"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":998330,"types":["addr_obj"],"relations":[{"id": 1235857, "data": {"id": 1235857, "name": "Кристалл", "level": "7", "nextid": 0, "previd": 1235812, "enddate": "2079-06-06", "changeid": 2734646, "isactive": 1, "isactual": 1, "objectid": 998330, "typename": "днп", "startdate": "2016-09-29", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 50, "updatedate": "2016-09-29"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1235812, "data": {"id": 1235812, "name": "Кристалл", "level": "15", "nextid": 1235857, "previd": 0, "enddate": "2016-09-29", "changeid": 2734553, "isactive": 0, "isactual": 0, "objectid": 998330, "typename": "днп", "startdate": "1900-01-01", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":52602183,"types":["house"],"relations":[{"id": 31421349, "data": {"id": 31421349, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 78793554, "housenum": "2", "isactive": 1, "isactual": 1, "objectid": 52602183, "housetype": 2, "startdate": "1900-01-01", "objectguid": "0830319a-575c-4402-a037-59ff671e321f", "opertypeid": 10, "updatedate": "2017-12-18"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":976397,"values":[{"value": "Оренбургская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "530000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":986313,"values":[{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "461100", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 1, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "5601900000001", "type_id": 10, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "530000000000000000000", "type_id": 13, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "536340000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"}]},{"object_id":986685,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "5601900009201", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900009200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "536344480000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":989833,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092001700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "560190000920017", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007001701", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0017", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":998330,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "560190000920007", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092000751", "type_id": 10, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "56019000092000700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0007", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":52602183,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "536344481010007001720001000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Оренбургская#Оренбургский#Пригородный сельсовет#Кристалл#Абрикосовая#2',
            $address->getStamp('#')
        );

        $address = $this->builder->build(
            [
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'objects' => '[{"object_id":5512,"types":["addr_obj"],"relations":[{"id": 6118, "data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 6108, "data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5512,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452680", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300401", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200000300402", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Башкортостан#Нефтекамск#Крым-Сараево#Крымсараево',
            $address->getStamp('#', true)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsFullString(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 52602183,
                'path_ltree' => '976397.986313.986685.998330.989833.52602183',
                'objects' => '[{"object_id":976397,"types":["addr_obj"],"relations":[{"id": 1211099, "data": {"id": 1211099, "name": "Оренбургская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2683816, "isactive": 1, "isactual": 1, "objectid": 976397, "typename": "обл", "startdate": "1900-01-01", "objectguid": "8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986313,"types":["addr_obj"],"relations":[{"id": 1222392, "data": {"id": 1222392, "name": "Оренбургский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2706517, "isactive": 1, "isactual": 1, "objectid": 986313, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "214f9132-f231-4f81-89c1-1241a6fb003a", "opertypeid": 1, "updatedate": "2011-09-13"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":986685,"types":["addr_obj"],"relations":[{"id": 1222809, "data": {"id": 1222809, "name": "Пригородный сельсовет", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2707361, "isactive": 1, "isactual": 1, "objectid": 986685, "typename": "тер", "startdate": "1900-01-01", "objectguid": "4a1fd887-b73d-4568-84ce-bddc6051a03e", "opertypeid": 1, "updatedate": "2014-01-05"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":989833,"types":["addr_obj"],"relations":[{"id": 1226333, "data": {"id": 1226333, "name": "Абрикосовая", "level": "8", "nextid": 0, "previd": 1226329, "enddate": "2079-06-06", "changeid": 2714338, "isactive": 1, "isactual": 1, "objectid": 989833, "typename": "ул", "startdate": "2016-09-29", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 51, "updatedate": "2017-04-17"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1226329, "data": {"id": 1226329, "name": "Абрикосовая", "level": "16", "nextid": 1226333, "previd": 0, "enddate": "2016-09-29", "changeid": 2714332, "isactive": 0, "isactual": 0, "objectid": 989833, "typename": "ул", "startdate": "1900-01-01", "objectguid": "9346e66e-2d58-4146-b41b-9caa9649a3c0", "opertypeid": 10, "updatedate": "2016-12-11"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":998330,"types":["addr_obj"],"relations":[{"id": 1235857, "data": {"id": 1235857, "name": "Кристалл", "level": "7", "nextid": 0, "previd": 1235812, "enddate": "2079-06-06", "changeid": 2734646, "isactive": 1, "isactual": 1, "objectid": 998330, "typename": "днп", "startdate": "2016-09-29", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 50, "updatedate": "2016-09-29"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1235812, "data": {"id": 1235812, "name": "Кристалл", "level": "15", "nextid": 1235857, "previd": 0, "enddate": "2016-09-29", "changeid": 2734553, "isactive": 0, "isactual": 0, "objectid": 998330, "typename": "днп", "startdate": "1900-01-01", "objectguid": "3935fbc7-a5bf-4fab-9d42-69c6472ca1bb", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":52602183,"types":["house"],"relations":[{"id": 31421349, "data": {"id": 31421349, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 78793554, "housenum": "2", "isactive": 1, "isactual": 1, "objectid": 52602183, "housetype": 2, "startdate": "1900-01-01", "objectguid": "0830319a-575c-4402-a037-59ff671e321f", "opertypeid": 10, "updatedate": "2017-12-18"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":976397,"values":[{"value": "Оренбургская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "530000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":986313,"values":[{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "461100", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 1, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "5601900000001", "type_id": 10, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"},{"value": "530000000000000000000", "type_id": 13, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "536340000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2006-01-01"}]},{"object_id":986685,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "5601900009201", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "5601900009200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "536344480000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":989833,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092001700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "560190000920017", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007001701", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0017", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":998330,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "560190000920007", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56019000092000751", "type_id": 10, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "56019000092000700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "536344481010000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "1900-01-01"},{"value": "536344481010007000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "0007", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"}]},{"object_id":52602183,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53634448101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53234848001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460507", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "536344481010007001720001000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "2", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Оренбургская область, Оренбургский район, территория Пригородный сельсовет, дачное некоммерческое партнерство Кристалл, улица Абрикосовая, д. 2',
            $address->getFullString()
        );

        $address = $this->builder->build(
            [
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'objects' => '[{"object_id":5512,"types":["addr_obj"],"relations":[{"id": 6118, "data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 6108, "data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6143,"types":["addr_obj"],"relations":[{"id": 6890, "data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5512,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452680", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300401", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0200000300402", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6143,"values":[{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200000300001", "type_id": 10, "end_date": "2013-10-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "452681", "type_id": 5, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "0200000300002", "type_id": 10, "end_date": "2013-10-31", "is_actual": false, "start_date": "2013-10-30"},{"value": "80727000", "type_id": 7, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000300003", "type_id": 10, "end_date": "2016-08-31", "is_actual": false, "start_date": "2013-10-31"},{"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "807270000000000000000", "type_id": 13, "end_date": "2016-08-31", "is_actual": false, "start_date": "1900-01-01"},{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2020-03-05", "is_actual": false, "start_date": "2016-08-31"},{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-03-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'республика Башкортостан, город Нефтекамск, деревня Крым-Сараево (бывш. Крымсараево)',
            $address->getFullString(true)
        );
    }

    /**
     * Корректность обработки перемещенных по уровням ФИАС адресов.
     *
     * @see (object_id = 182652) г Казань, тер ГСК Монтажник - был перемещен по уровню ФИАС. был ранее на уровне 8, перемещен на 7.
     * @test
     */
    public function itCorrectlyBuildsMovedEntity(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 182652,
                'path_ltree' => '169363.169398.182652',
                'objects' => '[{"object_id":169363,"types":["addr_obj"],"relations":[{"id": 206152, "data": {"id": 206152, "name": "Татарстан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 452723, "isactive": 1, "isactual": 1, "objectid": 169363, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "0c089b04-099e-4e0e-955a-6bf1ce525f1a", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":169398,"types":["addr_obj"],"relations":[{"id": 206201, "data": {"id": 206201, "name": "Казань", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 452834, "isactive": 1, "isactual": 1, "objectid": 169398, "typename": "г", "startdate": "1900-01-01", "objectguid": "93b3df57-4c89-44df-ac42-96f05e9cd3b9", "opertypeid": 1, "updatedate": "2018-01-09"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":182652,"types":["addr_obj"],"relations":[{"id": 221785, "data": {"id": 221785, "name": "СНТ Монтажник", "level": "7", "nextid": 0, "previd": 221768, "enddate": "2079-06-06", "changeid": 483791, "isactive": 1, "isactual": 1, "objectid": 182652, "typename": "тер.", "startdate": "2019-05-05", "objectguid": "45d4c912-aac0-42c3-a597-50efaf5bbcd5", "opertypeid": 50, "updatedate": "2019-05-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 221768, "data": {"id": 221768, "name": "СДТ Монтажник", "level": "8", "nextid": 221785, "previd": 0, "enddate": "2019-05-05", "changeid": 483762, "isactive": 0, "isactual": 0, "objectid": 182652, "typename": "тер", "startdate": "1900-01-01", "objectguid": "45d4c912-aac0-42c3-a597-50efaf5bbcd5", "opertypeid": 1, "updatedate": "2018-01-09"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":169363,"values":[{"value": "Республика Татарстан (Татарстан)", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1600", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1600", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "92000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "420000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1600000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "16000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "920000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "92000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":169398,"values":[{"value": "92701000", "type_id": 7, "end_date": "2021-02-09", "is_actual": false, "start_date": "1900-01-01"},{"value": "92701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-02-09"},{"value": "92401000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "16000001000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1600000100001", "type_id": 10, "end_date": "2017-12-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "1600000100000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-12-30"},{"value": "927010000000000000000", "type_id": 13, "end_date": "2017-12-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "927010000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2017-12-30"}]},{"object_id":182652,"values":[{"value": "1683", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1683", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "92401363000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "422700", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "1661", "type_id": 3, "end_date": "2017-12-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "1661", "type_id": 4, "end_date": "2017-12-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "16000001000526101", "type_id": 10, "end_date": "2017-12-30", "is_actual": false, "start_date": "1900-01-01"},{"value": "1656", "type_id": 3, "end_date": "2018-01-13", "is_actual": false, "start_date": "2017-12-30"},{"value": "1661", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-01-13"},{"value": "1656", "type_id": 4, "end_date": "2018-01-13", "is_actual": false, "start_date": "2017-12-30"},{"value": "1661", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-01-13"},{"value": "16000001000526102", "type_id": 10, "end_date": "2018-01-13", "is_actual": false, "start_date": "2017-12-30"},{"value": "92701000", "type_id": 7, "end_date": "2019-05-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "92701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-05"},{"value": "16000001000526151", "type_id": 10, "end_date": "2019-05-05", "is_actual": false, "start_date": "2018-01-13"},{"value": "16000001000593100", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-05"},{"value": "160000010005261", "type_id": 11, "end_date": "2019-05-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "160000010005931", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-05"},{"value": "927010000000000526100", "type_id": 13, "end_date": "2019-05-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "927010000015931000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-05"},{"value": "5261", "type_id": 15, "end_date": "2019-05-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "5931", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-05-05"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'республика Татарстан, город Казань, территория СНТ Монтажник',
            $address->getFullString()
        );
    }

    /**
     * Корректность обработки адресов с более чем одним actual relation на уровне.
     *
     * @test
     */
    public function itCorrectlyBuildsEntityWithSeveralRelationsOnLevel(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 11976,
                'path_ltree' => '5705.6326.6513.11976',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6326,"types":["addr_obj"],"relations":[{"id": 7148, "data": {"id": 7148, "name": "Уфа", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19976, "isactive": 1, "isactual": 1, "objectid": 6326, "typename": "г", "startdate": "1900-01-01", "objectguid": "7339e834-2cb4-4734-a4c7-1fca2c66e562", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6513,"types":["addr_obj"],"relations":[{"id": 7398, "data": {"id": 7398, "name": "Кировский", "level": "14", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 20549, "isactive": 1, "isactual": 1, "objectid": 6513, "typename": "р-н", "startdate": "1970-01-01", "objectguid": "65c87dd4-c269-483a-b3ce-c4d37603c4ba", "opertypeid": 10, "updatedate": "2017-11-19"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":11976,"types":["addr_obj"],"relations":[{"id": 14393, "data": {"id": 14393, "name": "ЛОК Солнечные пески в районе Мелькомбина", "level": "7", "nextid": 0, "previd": 14381, "enddate": "2079-06-06", "changeid": 33984, "isactive": 1, "isactual": 1, "objectid": 11976, "typename": "тер", "startdate": "2016-09-29", "objectguid": "c4f42ce4-0bd0-4f64-9bfe-c2ca9218efb8", "opertypeid": 50, "updatedate": "2017-03-11"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 14381, "data": {"id": 14381, "name": "ЛОК Солнечные пески в районе Мелькомбина", "level": "15", "nextid": 14393, "previd": 0, "enddate": "2016-09-29", "changeid": 33968, "isactive": 0, "isactual": 0, "objectid": 11976, "typename": "тер", "startdate": "2012-01-01", "objectguid": "c4f42ce4-0bd0-4f64-9bfe-c2ca9218efb8", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6326,"values":[{"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200100100051", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000000", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"}]},{"object_id":11976,"values":[{"value": "02000001000144651", "type_id": 10, "end_date": "2016-09-29", "is_actual": false, "start_date": "2012-01-01"},{"value": "02000001000144600", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "807010000000000000000", "type_id": 13, "end_date": "2016-09-29", "is_actual": false, "start_date": "2012-01-01"},{"value": "807010000001446000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "1446", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-29"},{"value": "80701000", "type_id": 7, "end_date": "2020-07-10", "is_actual": false, "start_date": "2012-01-01"},{"value": "0274", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2012-01-01"},{"value": "0274", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2012-01-01"},{"value": "80401375000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2012-01-01"},{"value": "450000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2012-01-01"},{"value": "020010010001482", "type_id": 11, "end_date": "2012-01-01", "is_actual": false, "start_date": "2012-01-01"},{"value": "020000010001446", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2012-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2012-01-01"},{"value": "02001001000148251", "type_id": 10, "end_date": "2012-01-01", "is_actual": false, "start_date": "2012-01-01"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-10"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'республика Башкортостан, город Уфа, территория ЛОК Солнечные пески в районе Мелькомбина',
            $address->getFullString()
        );
    }

    /**
     * Корректность обработки домов с пустым housenum, но не пустым addhousenum.
     *
     * @test
     */
    public function itCorrectlyBuildsEntityWithEmptyHouseNumAndNotEmptyAddHouseNum(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 1470768,
                'path_ltree' => '1121548.1136084.1138632.1131609.1470768',
                'objects' => '[{"object_id":1121548,"types":["addr_obj"],"relations":[{"id": 1381007, "data": {"id": 1381007, "name": "Самарская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3083948, "isactive": 1, "isactual": 1, "objectid": 1121548, "typename": "обл", "startdate": "1900-01-01", "objectguid": "df3d7359-afa9-4aaa-8ff9-197e73906b1c", "opertypeid": 1, "updatedate": "2015-10-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1131609,"types":["addr_obj"],"relations":[{"id": 1393461, "data": {"id": 1393461, "name": "Октябрьская", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3111453, "isactive": 1, "isactual": 1, "objectid": 1131609, "typename": "ул", "startdate": "1900-01-01", "objectguid": "a9a3171f-9484-4213-a8e7-000bfdd0933a", "opertypeid": 1, "updatedate": "2014-01-08"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1136084,"types":["addr_obj"],"relations":[{"id": 1399397, "data": {"id": 1399397, "name": "Большечерниговский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3122935, "isactive": 1, "isactual": 1, "objectid": 1136084, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c2be48d4-240a-401e-8462-446a0433df80", "opertypeid": 1, "updatedate": "2011-09-13"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1138632,"types":["addr_obj"],"relations":[{"id": 1402848, "data": {"id": 1402848, "name": "Глушицкий", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3129265, "isactive": 1, "isactual": 1, "objectid": 1138632, "typename": "п", "startdate": "1900-01-01", "objectguid": "0fd08c67-5dba-4485-8eec-2b652881af18", "opertypeid": 1, "updatedate": "2014-01-08"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1470768,"types":["house"],"relations":[{"id": 8374, "data": {"id": 8374, "nextid": 0, "previd": 0, "addnum1": "12", "addnum2": null, "enddate": "2079-06-06", "addtype1": 2, "addtype2": null, "changeid": 4078277, "housenum": null, "isactive": 1, "isactual": 1, "objectid": 1470768, "housetype": null, "startdate": "2007-07-23", "objectguid": "6904dcce-829b-4eff-83cf-e4e7c0f83b3a", "opertypeid": 10, "updatedate": "2015-10-17"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":1121548,"values":[{"value": "Самарская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6300", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6300", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6300000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "63000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "360000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":1131609,"values":[{"value": "6375", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6375", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36210808001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "446292", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "630060000080053", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0053", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36610408", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "36610408101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "63006000008005301", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "63006000008005300", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "366104080000000005300", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "366104081010000005301", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":1136084,"values":[{"value": "36210000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "63006000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6375", "type_id": 1, "end_date": "2011-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "6375", "type_id": 2, "end_date": "2011-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "36610000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2011-01-01"},{"value": "6300600000001", "type_id": 10, "end_date": "2011-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "6300600000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2011-01-01"},{"value": "360000000000000000000", "type_id": 13, "end_date": "2011-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "366100000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2011-01-01"}]},{"object_id":1138632,"values":[{"value": "6375", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6375", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36210808001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "63006000008", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36610408", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "36610408101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "446292", "type_id": 5, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "6300600000801", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "6300600000800", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "366104080000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "366104081010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":1470768,"values":[{"value": "6375", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "6375", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "36610408101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "36210808001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "446292", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "366104081010000005320035000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"},{"value": "35", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2007-07-23"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Самарская область, Большечерниговский район, поселок Глушицкий, улица Октябрьская, стр. 12',
            $address->getFullString()
        );
    }

    /**
     * Квартиры с типом 0.
     *
     * @test
     */
    public function itCorrectlyBuildsApartmentWithType0(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 2320587,
                'path_ltree' => '5705.30981.30923.32862.2319015.2320587',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":30923,"types":["addr_obj"],"relations":[{"id": 36205, "data": {"id": 36205, "name": "Иглино", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 82857, "isactive": 1, "isactual": 1, "objectid": 30923, "typename": "с", "startdate": "1900-01-01", "objectguid": "e0320a32-c4f6-4031-8548-ce08c34657dc", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":30981,"types":["addr_obj"],"relations":[{"id": 36269, "data": {"id": 36269, "name": "Иглинский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 83000, "isactive": 1, "isactual": 1, "objectid": 30981, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c5e868de-943c-4c4a-a4ad-8e50471e1e0d", "opertypeid": 1, "updatedate": "2019-07-19"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":32862,"types":["addr_obj"],"relations":[{"id": 38404, "data": {"id": 38404, "name": "Строителей", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 87726, "isactive": 1, "isactual": 1, "objectid": 32862, "typename": "ул", "startdate": "1900-01-01", "objectguid": "ebc9c061-ea38-47d1-922e-02a15396ba1c", "opertypeid": 1, "updatedate": "2014-01-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":2319015,"types":["house"],"relations":[{"id": 63947866, "data": {"id": 63947866, "nextid": 0, "previd": 527727, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 5316374, "housenum": "23/1", "isactive": 1, "isactual": 1, "objectid": 2319015, "housetype": 2, "startdate": "2018-07-03", "objectguid": "b162b5cf-e18f-480b-b688-9eb86d066976", "opertypeid": 20, "updatedate": "2019-12-14"}, "type": "house", "is_active": 1, "is_actual": 1},{"id": 527727, "data": {"id": 527727, "nextid": 63947866, "previd": 527688, "addnum1": null, "addnum2": null, "enddate": "2018-07-03", "addtype1": null, "addtype2": null, "changeid": 5316363, "housenum": "23/1", "isactive": 0, "isactual": 0, "objectid": 2319015, "housetype": 2, "startdate": "2018-07-03", "objectguid": "b162b5cf-e18f-480b-b688-9eb86d066976", "opertypeid": 20, "updatedate": "2018-07-03"}, "type": "house", "is_active": 0, "is_actual": 0},{"id": 527688, "data": {"id": 527688, "nextid": 527727, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2018-07-03", "addtype1": null, "addtype2": null, "changeid": 5316281, "housenum": "23/1", "isactive": 0, "isactual": 0, "objectid": 2319015, "housetype": 2, "startdate": "2016-08-17", "objectguid": "b162b5cf-e18f-480b-b688-9eb86d066976", "opertypeid": 10, "updatedate": "2018-07-03"}, "type": "house", "is_active": 0, "is_actual": 0}]},{"object_id":2320587,"types":["apartment"],"relations":[{"id": 518162, "data": {"id": 518162, "nextid": 0, "number": "8", "previd": 0, "enddate": "2079-06-06", "changeid": 5318762, "isactive": 1, "isactual": 1, "objectid": 2320587, "aparttype": 0, "startdate": "2016-08-18", "objectguid": "a8a05403-099c-4365-85ad-711df5fd1d39", "opertypeid": 10, "updatedate": "2017-01-29"}, "type": "apartment", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":30923,"values":[{"value": "0224", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-03-04"},{"value": "0224", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-03-04"},{"value": "0273", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0273", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80228816001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02024000001", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0224", "type_id": 3, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0224", "type_id": 4, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80628416", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80628416101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "0202400000101", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "0202400000100", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "806284160000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806284161010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":30981,"values":[{"value": "0273", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80228000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "452411", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0202400000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02024000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":32862,"values":[{"value": "0273", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0273", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0224", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0224", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80228816001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020240000010017", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0017", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80628416", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "80628416101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "452411", "type_id": 5, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "02024000001001701", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "806284160000000001700", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "02024000001001702", "type_id": 10, "end_date": "2018-07-03", "is_actual": false, "start_date": "2014-01-05"},{"value": "02024000001001703", "type_id": 10, "end_date": "2018-07-25", "is_actual": false, "start_date": "2018-07-03"},{"value": "02024000001001700", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-25"},{"value": "806284161010000001700", "type_id": 13, "end_date": "2018-07-25", "is_actual": false, "start_date": "2014-01-05"},{"value": "806284161010000001701", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-25"}]},{"object_id":2319015,"values":[{"value": "0273", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "0273", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "0224", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "0224", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "80628416101", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "80228816001", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "2", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "32", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-17"},{"value": "452410", "type_id": 5, "end_date": "2018-07-03", "is_actual": false, "start_date": "2016-08-17"},{"value": "452411", "type_id": 5, "end_date": "2018-07-03", "is_actual": false, "start_date": "2018-07-03"},{"value": "452410", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-03"},{"value": "806284161010000001720032000000005", "type_id": 13, "end_date": "2018-07-03", "is_actual": false, "start_date": "2016-08-17"},{"value": "806284161010000001720032000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-03"}]},{"object_id":2320587,"values":[{"value": "0224", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-03-04"},{"value": "0224", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-03-04"},{"value": "806284161010000001740032000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-18"},{"value": "452411", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-08-18"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'республика Башкортостан, Иглинский район, село Иглино, улица Строителей, д. 23/1, кв. 8',
            $address->getFullString()
        );
    }

    /**
     * Комнаты с типом 0.
     *
     * @test
     */
    public function itCorrectlyBuildsRoomWithType0(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 2465884,
                'path_ltree' => '976397.989357.991536.1002153.2465620.2465871.2465884',
                'objects' => '[{"object_id":976397,"types":["addr_obj"],"relations":[{"id": 1211099, "data": {"id": 1211099, "name": "Оренбургская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2683816, "isactive": 1, "isactual": 1, "objectid": 976397, "typename": "обл", "startdate": "1900-01-01", "objectguid": "8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":989357,"types":["addr_obj"],"relations":[{"id": 1225806, "data": {"id": 1225806, "name": "Октябрьский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2713314, "isactive": 1, "isactual": 1, "objectid": 989357, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "3e0c3e99-6e18-4ef8-afa4-ace21009790b", "opertypeid": 1, "updatedate": "2011-09-13"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":991536,"types":["addr_obj"],"relations":[{"id": 1228231, "data": {"id": 1228231, "name": "Верхний Гумбет", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2718315, "isactive": 1, "isactual": 1, "objectid": 991536, "typename": "с", "startdate": "1900-01-01", "objectguid": "683650e3-9ea4-414b-a22e-584c9aa39e2e", "opertypeid": 1, "updatedate": "2014-01-05"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1002153,"types":["addr_obj"],"relations":[{"id": 1240049, "data": {"id": 1240049, "name": "Черемушки", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 2744214, "isactive": 1, "isactual": 1, "objectid": 1002153, "typename": "ул", "startdate": "1900-01-01", "objectguid": "b029ac36-88a4-4c98-94de-030b6bb617fa", "opertypeid": 1, "updatedate": "2014-01-05"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":2465620,"types":["house"],"relations":[{"id": 609192, "data": {"id": 609192, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 5526312, "housenum": "7", "isactive": 1, "isactual": 1, "objectid": 2465620, "housetype": 3, "startdate": "2014-01-04", "objectguid": "0316561f-2b7e-4dae-b48c-8d4310f5e00e", "opertypeid": 10, "updatedate": "2015-11-03"}, "type": "house", "is_active": 1, "is_actual": 1}]},{"object_id":2465871,"types":["apartment"],"relations":[{"id": 612179, "data": {"id": 612179, "nextid": 0, "number": "1", "previd": 0, "enddate": "2079-06-06", "changeid": 5526695, "isactive": 1, "isactual": 1, "objectid": 2465871, "aparttype": 2, "startdate": "2014-01-04", "objectguid": "0c413473-5861-4e75-8550-00cb33120ae6", "opertypeid": 1, "updatedate": "2020-01-06"}, "type": "apartment", "is_active": 1, "is_actual": 1}]},{"object_id":2465884,"types":["room"],"relations":[{"id": 5248, "data": {"id": 5248, "nextid": 0, "number": "1", "previd": 0, "enddate": "2079-06-06", "changeid": 5526712, "isactive": 1, "isactual": 1, "objectid": 2465884, "roomtype": 0, "startdate": "2015-07-11", "objectguid": "1fcbb947-e4be-40c6-bfd1-879de387e5be", "opertypeid": 10, "updatedate": "2019-04-12"}, "type": "room", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":976397,"values":[{"value": "Оренбургская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "460000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5600000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "530000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":989357,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53233000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "462030", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56051000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2001-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5637", "type_id": 3, "end_date": "2001-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5637", "type_id": 4, "end_date": "2001-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "53633000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2001-01-01"},{"value": "5605100000001", "type_id": 10, "end_date": "2001-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "5605100000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2001-01-01"},{"value": "530000000000000000000", "type_id": 13, "end_date": "2001-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "536330000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2001-01-01"}]},{"object_id":991536,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5637", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5637", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53233831005", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "462052", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "56051000008", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53633431", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "53633431111", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "5605100000801", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "5605100000800", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "536334310000000000000", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "536334311110000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":1002153,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5637", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "5637", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53233831005", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "462052", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "560510000080002", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0002", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "53633431", "type_id": 7, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "53633431111", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "56051000008000201", "type_id": 10, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "56051000008000200", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"},{"value": "536334310000000000200", "type_id": 13, "end_date": "2014-01-05", "is_actual": false, "start_date": "1900-01-01"},{"value": "536334311110000000201", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-05"}]},{"object_id":2465620,"values":[{"value": "5638", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "5638", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "5637", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "5637", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "53633431111", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "53233831005", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "462052", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "536334311110000000220001000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "2", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"},{"value": "1", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-01-04"}]},{"object_id":2465884,"values":[{"value": "536334311110000000240001000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-11"},{"value": "462052", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-07-11"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Оренбургская область, Октябрьский район, село Верхний Гумбет, улица Черемушки, двлд. 7, кв. 1, пом. 1',
            $address->getFullString()
        );
    }

    /**
     * Здесь есть 2 item уровня house "здание" и "гараж", относящиеся к различным уровням address level.
     *
     * @test
     */
    public function itCorrectlyBuildsWith2House(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 4150391,
                'path_ltree' => '1325381.1325470.1331619.97102739.4150391',
                'objects' => '[{"object_id":1325381,"types":["addr_obj"],"relations":[{"id": 1637437, "data": {"id": 1637437, "name": "Ульяновская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630187, "isactive": 1, "isactual": 1, "objectid": 1325381, "typename": "обл", "startdate": "1900-01-01", "objectguid": "fee76045-fe22-43a4-ad58-ad99e903bd58", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1325470,"types":["addr_obj"],"relations":[{"id": 1637535, "data": {"id": 1637535, "name": "Димитровград", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630427, "isactive": 1, "isactual": 1, "objectid": 1325470, "typename": "г", "startdate": "1900-01-01", "objectguid": "73b29372-242c-42c5-89cd-8814bc2368af", "opertypeid": 1, "updatedate": "2014-11-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1331619,"types":["addr_obj"],"relations":[{"id": 1644402, "data": {"id": 1644402, "name": "ГСК Автомобилист-22", "level": "7", "nextid": 0, "previd": 1644317, "enddate": "2079-06-06", "changeid": 3645354, "isactive": 1, "isactual": 1, "objectid": 1331619, "typename": "тер.", "startdate": "2019-09-06", "objectguid": "27860043-0d01-4948-b32b-081595d45ade", "opertypeid": 50, "updatedate": "2019-09-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1644317, "data": {"id": 1644317, "name": "Автомобилист-22", "level": "8", "nextid": 1644402, "previd": 0, "enddate": "2019-09-06", "changeid": 3645165, "isactive": 0, "isactual": 0, "objectid": 1331619, "typename": "гск", "startdate": "1900-01-01", "objectguid": "27860043-0d01-4948-b32b-081595d45ade", "opertypeid": 1, "updatedate": "2019-09-06"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":4150391,"types":["house"],"relations":[{"id": 1614117, "data": {"id": 1614117, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 7956961, "housenum": "222", "isactive": 1, "isactual": 1, "objectid": 4150391, "housetype": 4, "startdate": "2019-09-09", "objectguid": "60c7c3b5-a0a3-4f61-a1ea-6c97d92544d1", "opertypeid": 10, "updatedate": "2019-09-09"}, "type": "house", "is_active": 1, "is_actual": 1}]},{"object_id":97102739,"types":["house"],"relations":[{"id": 69765528, "data": {"id": 69765528, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 151799652, "housenum": "1", "isactive": 1, "isactual": 1, "objectid": 97102739, "housetype": 5, "startdate": "2020-06-10", "objectguid": "3aaf91fe-73d2-4e35-bdad-73aa40dd8265", "opertypeid": 10, "updatedate": "2020-06-10"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":1325381,"values":[{"value": "Ульяновская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "433000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "730000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":1325470,"values":[{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-03-29"},{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000002000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73705000", "type_id": 7, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "7300000200001", "type_id": 10, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "737050000000000000000", "type_id": 13, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "73705000001", "type_id": 7, "end_date": "2014-11-01", "is_actual": false, "start_date": "2006-01-01"},{"value": "7300000200002", "type_id": 10, "end_date": "2014-11-01", "is_actual": false, "start_date": "2006-01-01"},{"value": "7300000200000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-11-01"},{"value": "737050000010000000000", "type_id": 13, "end_date": "2014-11-01", "is_actual": false, "start_date": "2006-01-01"},{"value": "737050000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-11-01"},{"value": "73705000", "type_id": 7, "end_date": "2021-03-29", "is_actual": false, "start_date": "2014-11-01"}]},{"object_id":1331619,"values":[{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "433500", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000002000032351", "type_id": 10, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "73000002000043500", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"},{"value": "730000020000323", "type_id": 11, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "730000020000435", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"},{"value": "737050000010000032300", "type_id": 13, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "737050000010435000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"},{"value": "0323", "type_id": 15, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "0435", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"}]},{"object_id":4150391,"values":[{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "433500", "type_id": 5, "end_date": "2021-04-05", "is_actual": false, "start_date": "2019-09-09"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "523", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "73:23:010101:2539", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-06"},{"value": "737050000010435000020523000000005", "type_id": 13, "end_date": "2020-07-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "737050000010435000020523000000010", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-06"}]},{"object_id":97102739,"values":[{"value": "73:23:014001:2456", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "648", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "737050000010435000020648000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "433500", "type_id": 5, "end_date": "2021-04-05", "is_actual": false, "start_date": "2020-06-10"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Ульяновская область, город Димитровград, территория ГСК Автомобилист-22, зд. 1, гар. 222',
            $address->getFullString()
        );
    }

    /**
     * Empty level type "housetype" for address.
     *
     * @test
     */
    public function itCorrectlyBuildsHouseWithType0(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 76943274,
                'path_ltree' => '1121548.1122019.1138016.76943274',
                'objects' => '[{"object_id":1121548,"types":["addr_obj"],"relations":[{"id": 1381007, "data": {"id": 1381007, "name": "Самарская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3083948, "isactive": 1, "isactual": 1, "objectid": 1121548, "typename": "обл", "startdate": "1900-01-01", "objectguid": "df3d7359-afa9-4aaa-8ff9-197e73906b1c", "opertypeid": 1, "updatedate": "2015-10-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1122019,"types":["addr_obj"],"relations":[{"id": 1381558, "data": {"id": 1381558, "name": "Жигулевск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3085281, "isactive": 1, "isactual": 1, "objectid": 1122019, "typename": "г", "startdate": "1900-01-01", "objectguid": "2f44f8ee-a505-46bf-b6de-6648a166295e", "opertypeid": 1, "updatedate": "2018-02-12"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1138016,"types":["addr_obj"],"relations":[{"id": 1402038, "data": {"id": 1402038, "name": "11-й", "level": "7", "nextid": 0, "previd": 1402001, "enddate": "2079-06-06", "changeid": 3127795, "isactive": 1, "isactual": 1, "objectid": 1138016, "typename": "гск", "startdate": "2016-09-28", "objectguid": "e66c4c93-9e27-480c-a549-e8b681964b61", "opertypeid": 50, "updatedate": "2018-02-12"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1402001, "data": {"id": 1402001, "name": "11-й", "level": "15", "nextid": 1402038, "previd": 0, "enddate": "2016-09-28", "changeid": 3127697, "isactive": 0, "isactual": 0, "objectid": 1138016, "typename": "гск", "startdate": "1900-01-01", "objectguid": "e66c4c93-9e27-480c-a549-e8b681964b61", "opertypeid": 10, "updatedate": "2017-12-10"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":76943274,"types":["house"],"relations":[{"id": 46501392, "data": {"id": 46501392, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 114448983, "housenum": "30", "isactive": 1, "isactual": 1, "objectid": 76943274, "housetype": 0, "startdate": "2018-07-04", "objectguid": "c9f753bd-d476-4615-b0df-2d7e6b0f371c", "opertypeid": 10, "updatedate": "2018-07-04"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":1121548,"values":[{"value": "Самарская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6300", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6300", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6300000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "63000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "360000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":1122019,"values":[{"value": "6382", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6382", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36404000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "63000002000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36704000", "type_id": 7, "end_date": "2018-02-09", "is_actual": false, "start_date": "1900-01-01"},{"value": "36704000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-02-09"},{"value": "6300000200001", "type_id": 10, "end_date": "2018-02-09", "is_actual": false, "start_date": "1900-01-01"},{"value": "6300000200000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-02-09"},{"value": "367040000000000000000", "type_id": 13, "end_date": "2018-02-09", "is_actual": false, "start_date": "1900-01-01"},{"value": "367040000010000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-02-09"}]},{"object_id":1138016,"values":[{"value": "6382", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6382", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6345", "type_id": 3, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "6345", "type_id": 4, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "36404000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "445350", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "630000020000235", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "63000002000023551", "type_id": 10, "end_date": "2016-09-28", "is_actual": false, "start_date": "1900-01-01"},{"value": "367040000010000000000", "type_id": 13, "end_date": "2016-09-28", "is_actual": false, "start_date": "1900-01-01"},{"value": "0235", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2016-09-28"},{"value": "36704000", "type_id": 7, "end_date": "2018-02-09", "is_actual": false, "start_date": "1900-01-01"},{"value": "36704000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-02-09"},{"value": "63000002000023501", "type_id": 10, "end_date": "2018-02-09", "is_actual": false, "start_date": "2016-09-28"},{"value": "63000002000023500", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-02-09"},{"value": "367040000000235000000", "type_id": 13, "end_date": "2018-02-09", "is_actual": false, "start_date": "2016-09-28"},{"value": "367040000010235000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-02-09"}]},{"object_id":76943274,"values":[{"value": "6382", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "6382", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "36704000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "36404000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "367040000010235000020001000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "1", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2018-07-04"},{"value": "445350", "type_id": 5, "end_date": "2021-04-05", "is_actual": false, "start_date": "2018-07-04"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            'Самарская область, город Жигулевск, гаражно-строительный кооператив 11-й, зд. 30',
            $address->getFullString()
        );
    }

    /**
     * @test
     */
    public function itShouldChooseMaxDeltaVersion(): void
    {
        $address = $this->builder->build(
            [
                'object_id' => 4150391,
                'path_ltree' => '1325381.1325470.1331619.97102739.4150391',
                'objects' => '[{"object_id":1325381,"types":["addr_obj"],"relations":[{"id": 1637437, "data": {"id": 1637437, "name": "Ульяновская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630187, "isactive": 1, "isactual": 1, "objectid": 1325381, "typename": "обл", "startdate": "1900-01-01", "objectguid": "fee76045-fe22-43a4-ad58-ad99e903bd58", "opertypeid": 1, "updatedate": "2015-09-15"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1325470,"types":["addr_obj"],"relations":[{"id": 1637535, "data": {"id": 1637535, "name": "Димитровград", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630427, "isactive": 1, "isactual": 1, "objectid": 1325470, "typename": "г", "startdate": "1900-01-01", "objectguid": "73b29372-242c-42c5-89cd-8814bc2368af", "opertypeid": 1, "updatedate": "2014-11-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":1331619,"types":["addr_obj"],"relations":[{"id": 1644402, "data": {"id": 1644402, "name": "ГСК Автомобилист-22", "level": "7", "nextid": 0, "previd": 1644317, "enddate": "2079-06-06", "changeid": 3645354, "isactive": 1, "isactual": 1, "objectid": 1331619, "typename": "тер.", "startdate": "2019-09-06", "objectguid": "27860043-0d01-4948-b32b-081595d45ade", "opertypeid": 50, "updatedate": "2019-09-06"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 1644317, "data": {"id": 1644317, "name": "Автомобилист-22", "level": "8", "nextid": 1644402, "previd": 0, "enddate": "2019-09-06", "changeid": 3645165, "isactive": 0, "isactual": 0, "objectid": 1331619, "typename": "гск", "startdate": "1900-01-01", "objectguid": "27860043-0d01-4948-b32b-081595d45ade", "opertypeid": 1, "updatedate": "2019-09-06"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]},{"object_id":4150391,"types":["house"],"relations":[{"id": 1614117, "data": {"id": 1614117, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 7956961, "housenum": "222", "isactive": 1, "isactual": 1, "objectid": 4150391, "housetype": 4, "startdate": "2019-09-09", "objectguid": "60c7c3b5-a0a3-4f61-a1ea-6c97d92544d1", "opertypeid": 10, "updatedate": "2019-09-09"}, "type": "house", "is_active": 1, "is_actual": 1}]},{"object_id":97102739,"types":["house"],"relations":[{"id": 69765528, "data": {"id": 69765528, "nextid": 0, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 151799652, "housenum": "1", "isactive": 1, "isactual": 1, "objectid": 97102739, "housetype": 5, "startdate": "2020-06-10", "objectguid": "3aaf91fe-73d2-4e35-bdad-73aa40dd8265", "opertypeid": 10, "updatedate": "2020-06-10"}, "type": "house", "is_active": 1, "is_actual": 1}]}]',
                'params' => '[{"object_id":1325381,"values":[{"value": "Ульяновская область", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "433000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7300000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "730000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":1325470,"values":[{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2021-03-29"},{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000002000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73705000", "type_id": 7, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "7300000200001", "type_id": 10, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "737050000000000000000", "type_id": 13, "end_date": "2006-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "73705000001", "type_id": 7, "end_date": "2014-11-01", "is_actual": false, "start_date": "2006-01-01"},{"value": "7300000200002", "type_id": 10, "end_date": "2014-11-01", "is_actual": false, "start_date": "2006-01-01"},{"value": "7300000200000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-11-01"},{"value": "737050000010000000000", "type_id": 13, "end_date": "2014-11-01", "is_actual": false, "start_date": "2006-01-01"},{"value": "737050000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2014-11-01"},{"value": "73705000", "type_id": 7, "end_date": "2021-03-29", "is_actual": false, "start_date": "2014-11-01"}]},{"object_id":1331619,"values":[{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "433500", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "73000002000032351", "type_id": 10, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "73000002000043500", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"},{"value": "730000020000323", "type_id": 11, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "730000020000435", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"},{"value": "737050000010000032300", "type_id": 13, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "737050000010435000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"},{"value": "0323", "type_id": 15, "end_date": "2019-09-06", "is_actual": false, "start_date": "1900-01-01"},{"value": "0435", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-06"}]},{"object_id":4150391,"values":[{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "433500", "type_id": 5, "end_date": "2021-04-05", "is_actual": false, "start_date": "2019-09-09"},{"value": "1", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "523", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "73:23:010101:2539", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-06"},{"value": "737050000010435000020523000000005", "type_id": 13, "end_date": "2020-07-06", "is_actual": true, "start_date": "2019-09-09"},{"value": "737050000010435000020523000000010", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-06"}]},{"object_id":97102739,"values":[{"value": "73:23:014001:2456", "type_id": 8, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "73705000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "73405000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "7329", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "7329", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "648", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "737050000010435000020648000000000", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-06-10"},{"value": "433500", "type_id": 5, "end_date": "2021-04-05", "is_actual": false, "start_date": "2020-06-10"}]}]',
                'max_delta_version' => 3,
                'objects_max_delta_version' => 200,
                'params_max_delta_version' => 1,
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals(
            200,
            $address->getDeltaVersion()
        );
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsLocation(): void
    {
        // переименование улицы
        $address = $this->builder->build(
            [
                'object_id' => 8654,
                'path_ltree' => '5705.6326.8654',
                'objects' => '[{"object_id":5705,"types":["addr_obj"],"relations":[{"id": 6356, "data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":6326,"types":["addr_obj"],"relations":[{"id": 7148, "data": {"id": 7148, "name": "Уфа", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19976, "isactive": 1, "isactual": 1, "objectid": 6326, "typename": "г", "startdate": "1900-01-01", "objectguid": "7339e834-2cb4-4734-a4c7-1fca2c66e562", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1}]},{"object_id":8654,"types":["addr_obj"],"relations":[{"id": 10275, "data": {"id": 10275, "name": "Мустая Карима", "level": "8", "nextid": 0, "previd": 10268, "enddate": "2079-06-06", "changeid": 27353, "isactive": 1, "isactual": 1, "objectid": 8654, "typename": "ул", "startdate": "1900-01-01", "objectguid": "76293e30-b0d7-4260-8d26-02c14a504ab7", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 1, "is_actual": 1},{"id": 10268, "data": {"id": 10268, "name": "Социалистическая", "level": "8", "nextid": 10275, "previd": 0, "enddate": "1900-01-01", "changeid": 27336, "isactive": 0, "isactual": 0, "objectid": 8654, "typename": "ул", "startdate": "1900-01-01", "objectguid": "76293e30-b0d7-4260-8d26-02c14a504ab7", "opertypeid": 1, "updatedate": "2017-11-16"}, "type": "addr_obj", "is_active": 0, "is_actual": 0}]}]',
                'params' => '[{"object_id":5705,"values":[{"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80000000000", "type_id": 6, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000000001", "type_id": 10, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "800000000000000000000", "type_id": 13, "end_date": "2015-11-18", "is_actual": false, "start_date": "1900-01-01"},{"value": "80202858001", "type_id": 6, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "452112", "type_id": 5, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "452000", "type_id": 5, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "0200000000002", "type_id": 10, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "806024580000000000000", "type_id": 13, "end_date": "2015-12-01", "is_actual": false, "start_date": "2015-11-18"},{"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2015-12-01"},{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"}]},{"object_id":6326,"values":[{"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "450000", "type_id": 5, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200100100051", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000000", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "is_actual": true, "start_date": "1900-01-01"},{"value": "0200", "type_id": 1, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"},{"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-02-11"}]},{"object_id":8654,"values":[{"value": "0", "type_id": 14, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "02001001000085201", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02001001000085202", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "80701000", "type_id": 7, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02001001000085251", "type_id": 10, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "020010010000852", "type_id": 11, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "807010000000000085200", "type_id": 13, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "0852", "type_id": 15, "end_date": "1900-01-01", "is_actual": false, "start_date": "1900-01-01"},{"value": "0544", "type_id": 15, "end_date": "2079-06-06", "is_actual": true, "start_date": "1900-01-01"},{"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "is_actual": true, "start_date": "2020-07-10"}]}]',
                'max_delta_version' => '20200303',
                'objects_max_delta_version' => '20200303',
                'params_max_delta_version' => '20200303',
                'lon' => 10,
                'lat' => -10,
            ]
        );

        $this->assertEquals([10, -10], $address->getLocation());
    }
}
