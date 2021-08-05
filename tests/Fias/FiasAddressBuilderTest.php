<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias;

use Addresser\AddressRepository\ActualityComparator;
use Addresser\AddressRepository\AddressBuilderInterface;
use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressSynonymizer;
use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;
use Addresser\AddressRepository\Exceptions\RuntimeException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\AddHouseAddressLevelSpecResolver;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\ApartmentAddressLevelSpecResolver;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\HouseAddressLevelSpecResolver;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\ObjectAddressLevelSpecResolver;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\RoomAddressLevelSpecResolver;
use Addresser\AddressRepository\Fias\FiasAddressBuilder;
use Addresser\AddressRepository\Fias\FiasLevel;
use PHPUnit\Framework\TestCase;

class FiasAddressBuilderTest extends TestCase
{
    private AddressBuilderInterface $builder;

    protected function setUp(): void
    {
        $this->builder = new FiasAddressBuilder(
            new ObjectAddressLevelSpecResolver(),
            new HouseAddressLevelSpecResolver(),
            new AddHouseAddressLevelSpecResolver(),
            new ApartmentAddressLevelSpecResolver(),
            new RoomAddressLevelSpecResolver(),
            new ActualityComparator(),
            new AddressSynonymizer()
        );
    }

    /**
     * CAR_PLACE - пока не обрабатываем
     * @test
     */
    public function itShouldThrowExceptionWhenBuildsCarPlace(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported address level.');

        $this->builder->build(
            [
                'hierarchy_id' => 110545915,
                'object_id' => 95392599,
                'path_ltree' => '5705.6326.8931.70915638.95392599',
                'parents' => '[{"params": [{"values": [{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 6326, "hierarchy_id": 5606171}, {"values": [{"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "start_date": "2020-02-11"}], "object_id": 6326, "hierarchy_id": 22111227}], "relation": {"object_id": 6326, "relation_id": 7148, "hierarchy_id": 5606171, "relation_data": {"id": 7148, "name": "Уфа", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19976, "isactive": 1, "isactual": 1, "objectid": 6326, "typename": "г", "startdate": "1900-01-01", "objectguid": "7339e834-2cb4-4734-a4c7-1fca2c66e562", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 10606008}, {"values": [{"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 10620845}, {"values": [{"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 23942359}, {"values": [{"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 23944393}], "relation": {"object_id": 8931, "relation_id": 10603, "hierarchy_id": 10620845, "relation_data": {"id": 10603, "name": "Горького", "level": "8", "nextid": 10636, "previd": 0, "enddate": "1900-01-01", "changeid": 28056, "isactive": 0, "isactual": 0, "objectid": 8931, "typename": "ул", "startdate": "1900-01-01", "objectguid": "6697bd2e-7a91-4524-ba4e-d543c2324da4", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 10606008}, {"values": [{"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 10620845}, {"values": [{"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 23942359}, {"values": [{"value": "02000001000090300", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0903", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000010000090301", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000903", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8931, "hierarchy_id": 23944393}], "relation": {"object_id": 8931, "relation_id": 10636, "hierarchy_id": 10620845, "relation_data": {"id": 10636, "name": "Максима Горького", "level": "8", "nextid": 0, "previd": 10603, "enddate": "2079-06-06", "changeid": 28103, "isactive": 1, "isactual": 1, "objectid": 8931, "typename": "ул", "startdate": "1900-01-01", "objectguid": "6697bd2e-7a91-4524-ba4e-d543c2324da4", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "2", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "55", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "02:55:030317:18", "type_id": 8, "end_date": "2079-06-06", "start_date": "2017-01-20"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "807010000010000090320055000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-04-09"}, {"value": "450112", "type_id": 5, "end_date": "2079-06-06", "start_date": "2018-04-09"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-12-08"}], "object_id": 70915638, "hierarchy_id": 10631036}], "relation": {"object_id": 70915638, "relation_id": 68135167, "hierarchy_id": 10631036, "relation_data": {"id": 68135167, "nextid": 0, "previd": 42771811, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 105648732, "housenum": "51", "isactive": 1, "isactual": 1, "objectid": 70915638, "housetype": 2, "startdate": "2018-04-09", "objectguid": "ec8a5184-f20d-4e07-8ef4-32d3ab3d0464", "opertypeid": 20, "updatedate": "2018-04-12"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "2", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "55", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "02:55:030317:18", "type_id": 8, "end_date": "2079-06-06", "start_date": "2017-01-20"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "807010000010000090320055000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-04-09"}, {"value": "450112", "type_id": 5, "end_date": "2079-06-06", "start_date": "2018-04-09"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-12-08"}], "object_id": 70915638, "hierarchy_id": 10631036}], "relation": {"object_id": 70915638, "relation_id": 42771811, "hierarchy_id": 10631036, "relation_data": {"id": 42771811, "nextid": 68135167, "previd": 42771639, "addnum1": null, "addnum2": null, "enddate": "2018-04-09", "addtype1": null, "addtype2": null, "changeid": 105648702, "housenum": "51", "isactive": 0, "isactual": 0, "objectid": 70915638, "housetype": 2, "startdate": "2017-01-20", "objectguid": "ec8a5184-f20d-4e07-8ef4-32d3ab3d0464", "opertypeid": 20, "updatedate": "2018-04-12"}, "relation_type": "house", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "2", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "55", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "02:55:030317:18", "type_id": 8, "end_date": "2079-06-06", "start_date": "2017-01-20"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-12-08"}, {"value": "807010000010000090320055000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-04-09"}, {"value": "450112", "type_id": 5, "end_date": "2079-06-06", "start_date": "2018-04-09"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-12-08"}], "object_id": 70915638, "hierarchy_id": 10631036}], "relation": {"object_id": 70915638, "relation_id": 42771639, "hierarchy_id": 10631036, "relation_data": {"id": 42771639, "nextid": 42771811, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2017-01-20", "addtype1": null, "addtype2": null, "changeid": 105648294, "housenum": "51", "isactive": 0, "isactual": 0, "objectid": 70915638, "housetype": 2, "startdate": "2016-12-08", "objectguid": "ec8a5184-f20d-4e07-8ef4-32d3ab3d0464", "opertypeid": 10, "updatedate": "2018-04-12"}, "relation_type": "house", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "450112", "type_id": 5, "end_date": "2079-06-06", "start_date": "2020-02-02"}, {"value": "80401385000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2020-02-02"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-02-02"}, {"value": "02:55:030317:433", "type_id": 8, "end_date": "2079-06-06", "start_date": "2020-02-02"}, {"value": "0277", "type_id": 1, "end_date": "2079-06-06", "start_date": "2020-02-02"}, {"value": "0277", "type_id": 2, "end_date": "2079-06-06", "start_date": "2020-02-02"}], "object_id": 95392599, "hierarchy_id": 110545915}], "relation": {"object_id": 95392599, "relation_id": 1530, "hierarchy_id": 110545915, "relation_data": {"id": 1530, "nextid": 0, "number": "2108", "previd": 0, "enddate": "2079-06-06", "changeid": 138548530, "isactive": 1, "isactual": 1, "objectid": 95392599, "startdate": "2020-02-02", "objectguid": "4dba70c7-937e-42d2-a2a5-5ecbbfac7c1f", "opertypeid": 10, "updatedate": "2020-02-02"}, "relation_type": "carplace", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );
    }

    /**
     * STEAD - пока не обрабатываем
     * @test
     */
    public function itShouldThrowExceptionWhenBuildsStead(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported address level.');

        $this->builder->build(
            [
                'hierarchy_id' => 111485344,
                'object_id' => 96170133,
                'path_ltree' => '5705.11745.13232.15675.96170133',
                'parents' => '[{"params": [{"values": [{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80652000", "type_id": 7, "end_date": "2079-06-06", "start_date": "2017-04-19"}, {"value": "02001000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "806520000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2017-04-19"}, {"value": "0200100000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2017-04-19"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "450000", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80252000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 11745, "hierarchy_id": 22107662}], "relation": {"object_id": 11745, "relation_id": 14070, "hierarchy_id": 22107662, "relation_data": {"id": 14070, "name": "Уфимский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 33545, "isactive": 1, "isactual": 1, "objectid": 11745, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c7a81174-8d01-4ae6-83e6-386ae23ee629", "opertypeid": 1, "updatedate": "2017-04-20"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0272", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200100005500", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "80652440116", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0245", "type_id": 4, "end_date": "2079-06-06", "start_date": "2021-04-27"}, {"value": "0245", "type_id": 3, "end_date": "2079-06-06", "start_date": "2021-04-27"}, {"value": "0272", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80252840005", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "450511", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02001000055", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "806524401160000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}], "object_id": 13232, "hierarchy_id": 27034223}], "relation": {"object_id": 13232, "relation_id": 16061, "hierarchy_id": 27034223, "relation_data": {"id": 16061, "name": "Суровка", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 36668, "isactive": 1, "isactual": 1, "objectid": 13232, "typename": "д", "startdate": "1900-01-01", "objectguid": "fda8f95f-78f8-4030-b39a-35efee12782f", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0272", "type_id": 1, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "02001000055004000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "450511", "type_id": 5, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "80252840005", "type_id": 6, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "80652440116", "type_id": 7, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "0272", "type_id": 2, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "0040", "type_id": 15, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "806524401160000004001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "020010000550040", "type_id": 11, "end_date": "2079-06-06", "start_date": "2019-10-21"}, {"value": "0245", "type_id": 3, "end_date": "2079-06-06", "start_date": "2021-04-27"}, {"value": "0245", "type_id": 4, "end_date": "2079-06-06", "start_date": "2021-04-27"}], "object_id": 15675, "hierarchy_id": 27050615}], "relation": {"object_id": 15675, "relation_id": 19083, "hierarchy_id": 27050615, "relation_data": {"id": 19083, "name": "Янтарная", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 42474, "isactive": 1, "isactual": 1, "objectid": 15675, "typename": "ул", "startdate": "2019-10-21", "objectguid": "754edbc7-9db7-44b2-9547-9dd884c0aa7f", "opertypeid": 10, "updatedate": "2019-10-21"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "02:47:110901:97", "type_id": 8, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "80252840005", "type_id": 6, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "0245", "type_id": 4, "end_date": "2079-06-06", "start_date": "2021-04-27"}, {"value": "806524401160000004010008000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "8", "type_id": 15, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "0245", "type_id": 3, "end_date": "2079-06-06", "start_date": "2021-04-27"}, {"value": "0272", "type_id": 2, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "450511", "type_id": 5, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "0272", "type_id": 1, "end_date": "2079-06-06", "start_date": "2020-03-30"}, {"value": "80652440116", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-30"}], "object_id": 96170133, "hierarchy_id": 111485344}], "relation": {"object_id": 96170133, "relation_id": 11255306, "hierarchy_id": 111485344, "relation_data": {"id": 11255306, "nextid": 0, "number": "16", "previd": 0, "enddate": "2079-06-06", "changeid": 142857252, "isactive": 1, "isactual": 1, "objectid": 96170133, "startdate": "2020-03-30", "objectguid": "8a4e0bb4-9349-4ea2-9520-c0a840117af6", "opertypeid": "10", "updatedate": "2020-03-30"}, "relation_type": "stead", "relation_is_active": 1, "relation_is_actual": 1}}]',
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
                'hierarchy_id' => 5661245,
                'object_id' => 259389,
                'path_ltree' => '259389',
                'parents' => '[{"params": [{"values": [{"value": "Чувашская республика - Чувашия", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Чувашская республика", "type_id": 16, "end_date": "1900-01-01", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "21000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "970000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 259389, "hierarchy_id": 5661245}], "relation": {"object_id": 259389, "relation_id": 318070, "hierarchy_id": 5661245, "relation_data": {"id": 318070, "name": "Чувашская Республика -", "level": "1", "nextid": 318076, "previd": 0, "enddate": "1900-01-01", "changeid": 667675, "isactive": 0, "isactual": 0, "objectid": 259389, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "Чувашская республика - Чувашия", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Чувашская республика", "type_id": 16, "end_date": "1900-01-01", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "21000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "970000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 259389, "hierarchy_id": 5661245}], "relation": {"object_id": 259389, "relation_id": 318076, "hierarchy_id": 5661245, "relation_data": {"id": 318076, "name": "Чувашская Республика -", "level": "1", "nextid": 0, "previd": 318070, "enddate": "2079-06-06", "changeid": 667684, "isactive": 1, "isactual": 1, "objectid": 259389, "typename": "Чувашия", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2016-02-24"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals('878fc621-3708-46c7-a97f-5a13a4176b3e', $address->getFiasId());
        $this->assertEquals(5661245, $address->getFiasHierarchyId());
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
                'hierarchy_id' => 8849211,
                'object_id' => 8654,
                'path_ltree' => '5705.6326.8654',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "2020-02-11"}], "object_id": 6326, "hierarchy_id": 5606171}, {"values": [{"value": "807010000010000000011", "type_id": 13, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80401000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000", "type_id": 7, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "807010000000000000002", "type_id": 13, "end_date": "2020-02-11", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "2020-02-11"}, {"value": "0200000100000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 6326, "hierarchy_id": 22111227}], "relation": {"object_id": 6326, "relation_id": 7148, "hierarchy_id": 5606171, "relation_data": {"id": 7148, "name": "Уфа", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19976, "isactive": 1, "isactual": 1, "objectid": 6326, "typename": "г", "startdate": "1900-01-01", "objectguid": "7339e834-2cb4-4734-a4c7-1fca2c66e562", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 8841595}, {"values": [{"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 8849211}, {"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 23426713}, {"values": [{"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 23429054}], "relation": {"object_id": 8654, "relation_id": 10268, "hierarchy_id": 8849211, "relation_data": {"id": 10268, "name": "Социалистическая", "level": "8", "nextid": 10275, "previd": 0, "enddate": "1900-01-01", "changeid": 27336, "isactive": 0, "isactual": 0, "objectid": 8654, "typename": "ул", "startdate": "1900-01-01", "objectguid": "76293e30-b0d7-4260-8d26-02c14a504ab7", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 8841595}, {"values": [{"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 8849211}, {"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 23426713}, {"values": [{"value": "0544", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807010000000000054401", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000001000054400", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-10"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000010000544", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 8654, "hierarchy_id": 23429054}], "relation": {"object_id": 8654, "relation_id": 10275, "hierarchy_id": 8849211, "relation_data": {"id": 10275, "name": "Мустая Карима", "level": "8", "nextid": 0, "previd": 10268, "enddate": "2079-06-06", "changeid": 27353, "isactive": 1, "isactual": 1, "objectid": 8654, "typename": "ул", "startdate": "1900-01-01", "objectguid": "76293e30-b0d7-4260-8d26-02c14a504ab7", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(['Социалистическая'], $address->getRenaming());

        $address = $this->builder->build(
            [
                'hierarchy_id' => 3245193,
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 6143, "hierarchy_id": 3245176}], "relation": {"object_id": 6143, "relation_id": 6890, "hierarchy_id": 3245176, "relation_data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5512, "hierarchy_id": 3245193}], "relation": {"object_id": 5512, "relation_id": 6108, "hierarchy_id": 3245193, "relation_data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5512, "hierarchy_id": 3245193}], "relation": {"object_id": 5512, "relation_id": 6118, "hierarchy_id": 3245193, "relation_data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        // есть переименования
        $this->assertEquals(['Крымсараево'], $address->getRenaming());
    }

    /**
     * @test
     */
    public function itCorrectlyBuildsSynonyms(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 1,
                'object_id' => 5705,
                'path_ltree' => '5705',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );
        $this->assertEquals(['Башкирия'], $address->getSynonyms());

        $address = $this->builder->build(
            [
                'hierarchy_id' => 3388851,
                'object_id' => 211522,
                'path_ltree' => '211522',
                'parents' => '[{"params": [{"values": [{"value": "94000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1800", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1800000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "18000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "940000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1800", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "94000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Удмуртская Республика", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 211522, "hierarchy_id": 3388851}], "relation": {"object_id": 211522, "relation_id": 254908, "hierarchy_id": 3388851, "relation_data": {"id": 254908, "name": "Удмуртская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 543325, "isactive": 1, "isactual": 1, "objectid": 211522, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "52618b9c-bcbb-47e7-8957-95c63f0b17cc", "opertypeid": 1, "updatedate": "2017-12-04"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );
        $this->assertEquals(['Удмуртия'], $address->getSynonyms());

        $address = $this->builder->build(
            [
                'hierarchy_id' => 5661245,
                'object_id' => 259389,
                'path_ltree' => '259389',
                'parents' => '[{"params": [{"values": [{"value": "Чувашская республика - Чувашия", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Чувашская республика", "type_id": 16, "end_date": "1900-01-01", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "21000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "970000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 259389, "hierarchy_id": 5661245}], "relation": {"object_id": 259389, "relation_id": 318070, "hierarchy_id": 5661245, "relation_data": {"id": 318070, "name": "Чувашская Республика -", "level": "1", "nextid": 318076, "previd": 0, "enddate": "1900-01-01", "changeid": 667675, "isactive": 0, "isactual": 0, "objectid": 259389, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "Чувашская республика - Чувашия", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Чувашская республика", "type_id": 16, "end_date": "1900-01-01", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "21000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "2100000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "97000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "970000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 259389, "hierarchy_id": 5661245}], "relation": {"object_id": 259389, "relation_id": 318076, "hierarchy_id": 5661245, "relation_data": {"id": 318076, "name": "Чувашская Республика -", "level": "1", "nextid": 0, "previd": 318070, "enddate": "2079-06-06", "changeid": 667684, "isactive": 1, "isactual": 1, "objectid": 259389, "typename": "Чувашия", "startdate": "1900-01-01", "objectguid": "878fc621-3708-46c7-a97f-5a13a4176b3e", "opertypeid": 1, "updatedate": "2016-02-24"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
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
                'hierarchy_id' => 1,
                'object_id' => 5705,
                'path_ltree' => '5705',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals('респ. Башкортостан', $address->getCompleteShortAddress());

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

        $this->assertNull($address->getCityFiasId());
        $this->assertNull($address->getCityKladrId());
        $this->assertNull($address->getCityType());
        $this->assertNull($address->getCityTypeFull());
        $this->assertNull($address->getCity());

        $this->assertNull($address->getSettlementFiasId());
        $this->assertNull($address->getSettlementKladrId());
        $this->assertNull($address->getSettlementType());
        $this->assertNull($address->getSettlementTypeFull());
        $this->assertNull($address->getSettlement());

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
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getFiasId());
        $this->assertEquals(1, $address->getFiasHierarchyId());
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
                'hierarchy_id' => 43309780,
                'object_id' => 36249,
                'path_ltree' => '5705.36249',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452930", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 36249, "hierarchy_id": 43309780}], "relation": {"object_id": 36249, "relation_id": 42085, "hierarchy_id": 43309780, "relation_data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals('респ. Башкортостан, Краснокамский р-н', $address->getCompleteShortAddress());

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

        $this->assertNull($address->getSettlementFiasId());
        $this->assertNull($address->getSettlementKladrId());
        $this->assertNull($address->getSettlementType());
        $this->assertNull($address->getSettlementTypeFull());
        $this->assertNull($address->getSettlement());

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
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getFiasId());
        $this->assertEquals(43309780, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildCity(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 3245176,
                'object_id' => 6143,
                'path_ltree' => '5705.6143',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 6143, "hierarchy_id": 3245176}], "relation": {"object_id": 6143, "relation_id": 6890, "hierarchy_id": 3245176, "relation_data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals('респ. Башкортостан, г. Нефтекамск', $address->getCompleteShortAddress());

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
        $this->assertEquals(3245176, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildCitySettlement(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 3245193,
                'object_id' => 5512,
                'path_ltree' => '5705.6143.5512',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 6143, "hierarchy_id": 3245176}], "relation": {"object_id": 6143, "relation_id": 6890, "hierarchy_id": 3245176, "relation_data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5512, "hierarchy_id": 3245193}], "relation": {"object_id": 5512, "relation_id": 6108, "hierarchy_id": 3245193, "relation_data": {"id": 6108, "name": "Крымсараево", "level": "6", "nextid": 6118, "previd": 0, "enddate": "1900-01-01", "changeid": 17231, "isactive": 0, "isactual": 0, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2017-11-16"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003004", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000121", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "807270001210000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300400", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427807004", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5512, "hierarchy_id": 3245193}], "relation": {"object_id": 5512, "relation_id": 6118, "hierarchy_id": 3245193, "relation_data": {"id": 6118, "name": "Крым-Сараево", "level": "6", "nextid": 0, "previd": 6108, "enddate": "2079-06-06", "changeid": 17273, "isactive": 1, "isactual": 1, "objectid": 5512, "typename": "д", "startdate": "1900-01-01", "objectguid": "f5b6853e-7787-4127-b60a-a2bcc96a9b3f", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, дер. Крым-Сараево, (бывш. Крымсараево)',
            $address->getCompleteShortAddress()
        );

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());

        // для нас. пунктов внутри города - город заполнен
        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getCityFiasId());
        $this->assertEquals('0200000300000', $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());

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
        $this->assertEquals('f5b6853e-7787-4127-b60a-a2bcc96a9b3f', $address->getFiasId());
        $this->assertEquals(3245193, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildCityFlat(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 3697992,
                'object_id' => 69611691,
                'path_ltree' => '5705.6143.7280.69610029.69611691',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 6143, "hierarchy_id": 3245176}], "relation": {"object_id": 6143, "relation_id": 6890, "hierarchy_id": 3245176, "relation_data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0002", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000030000002", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "02000003000000200", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-07-10"}, {"value": "807270000010000000201", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-07-10"}], "object_id": 7280, "hierarchy_id": 3683317}], "relation": {"object_id": 7280, "relation_id": 8472, "hierarchy_id": 3683317, "relation_data": {"id": 8472, "name": "Социалистическая", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 23087, "isactive": 1, "isactual": 1, "objectid": 7280, "typename": "ул", "startdate": "1900-01-01", "objectguid": "b008fb9b-72d8-4949-9eef-d1935589e84d", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452684", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "154", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807270000010000000220154000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 69610029, "hierarchy_id": 3697964}], "relation": {"object_id": 69610029, "relation_id": 41986684, "hierarchy_id": 3697964, "relation_data": {"id": 41986684, "nextid": 67480068, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2016-08-31", "addtype1": null, "addtype2": null, "changeid": 103747162, "housenum": "18", "isactive": 0, "isactual": 0, "objectid": 69610029, "housetype": 2, "startdate": "1900-01-01", "objectguid": "e3463736-aaa5-4759-b609-a37a2696fe7f", "opertypeid": 10, "updatedate": "2019-07-10"}, "relation_type": "house", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452684", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "154", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "807270000010000000220154000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 69610029, "hierarchy_id": 3697964}], "relation": {"object_id": 69610029, "relation_id": 67480068, "hierarchy_id": 3697964, "relation_data": {"id": 67480068, "nextid": 0, "previd": 41986684, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 103747177, "housenum": "18", "isactive": 1, "isactual": 1, "objectid": 69610029, "housetype": 2, "startdate": "2016-08-31", "objectguid": "e3463736-aaa5-4759-b609-a37a2696fe7f", "opertypeid": 20, "updatedate": "2016-08-31"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "02:66:010105:3137", "type_id": 8, "end_date": "2079-06-06", "start_date": "2017-05-02"}, {"value": "452684", "type_id": 5, "end_date": "2079-06-06", "start_date": "2017-05-02"}, {"value": "807270000010000000240154000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2017-05-02"}], "object_id": 69611691, "hierarchy_id": 3697992}], "relation": {"object_id": 69611691, "relation_id": 41515966, "hierarchy_id": 3697992, "relation_data": {"id": 41515966, "nextid": 0, "number": "1", "previd": 0, "enddate": "2079-06-06", "changeid": 103749605, "isactive": 1, "isactual": 1, "objectid": 69611691, "aparttype": 2, "startdate": "2017-05-02", "objectguid": "280df371-0124-45ea-947a-b7b67052c8ee", "opertypeid": 10, "updatedate": "2019-06-20"}, "relation_type": "apartment", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, ул. Социалистическая, д. 18, кв. 1',
            $address->getCompleteShortAddress()
        );

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

        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getCityFiasId());
        $this->assertEquals('0200000300000', $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());

        $this->assertEquals('b008fb9b-72d8-4949-9eef-d1935589e84d', $address->getStreetFiasId());
        $this->assertEquals('02000003000000200', $address->getStreetKladrId());
        $this->assertEquals('ул.', $address->getStreetType());
        $this->assertEquals('улица', $address->getStreetTypeFull());
        $this->assertEquals('Социалистическая', $address->getStreet());

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
        $this->assertEquals(3697992, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildAreaSettlement(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 43652997,
                'object_id' => 37631,
                'path_ltree' => '5705.36249.37631',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452930", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 36249, "hierarchy_id": 43309780}], "relation": {"object_id": 36249, "relation_id": 42085, "hierarchy_id": 43309780, "relation_data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 37631, "hierarchy_id": 43652997}], "relation": {"object_id": 37631, "relation_id": 43639, "hierarchy_id": 43652997, "relation_data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals('респ. Башкортостан, Краснокамский р-н, с. Куяново', $address->getCompleteShortAddress());

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

        // для нас. пунктов внутри района - район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());

        // соответствующий уровень заполнен
        $this->assertEquals($address->getFiasId(), $address->getSettlementFiasId());
        $this->assertEquals($address->getKladrId(), $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());

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
        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getFiasId());
        $this->assertEquals(43652997, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildSettlementStreet(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 43666877,
                'object_id' => 38528,
                'path_ltree' => '5705.36249.37631.38528',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452930", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 36249, "hierarchy_id": 43309780}], "relation": {"object_id": 36249, "relation_id": 42085, "hierarchy_id": 43309780, "relation_data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 37631, "hierarchy_id": 43652997}], "relation": {"object_id": 37631, "relation_id": 43639, "hierarchy_id": 43652997, "relation_data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "806374121010000001901", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003001900", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020310000030019", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0019", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 38528, "hierarchy_id": 43666877}], "relation": {"object_id": 38528, "relation_id": 44686, "hierarchy_id": 43666877, "relation_data": {"id": 44686, "name": "Комсомольский", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 102031, "isactive": 1, "isactual": 1, "objectid": 38528, "typename": "пр-кт", "startdate": "1900-01-01", "objectguid": "c876fdd0-5f9c-4389-9d98-f1bff7640520", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, Краснокамский р-н, с. Куяново, пр-кт Комсомольский',
            $address->getCompleteShortAddress()
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

        // для поселков и тд район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());

        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getSettlementFiasId());
        $this->assertEquals('0203100000300', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());

        // соответствующий уровень заполнен
        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getStreetFiasId());
        $this->assertEquals('02031000003001900', $address->getStreetKladrId());
        $this->assertEquals('пр-кт', $address->getStreetType());
        $this->assertEquals('проспект', $address->getStreetTypeFull());
        $this->assertEquals('Комсомольский', $address->getStreet());

        // все остальные уровни пустые
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
        $this->assertEquals(43666877, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildSettlementHouse(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 43669947,
                'object_id' => 79959421,
                'path_ltree' => '5705.36249.37631.38528.79959421',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452930", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 36249, "hierarchy_id": 43309780}], "relation": {"object_id": 36249, "relation_id": 42085, "hierarchy_id": 43309780, "relation_data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 37631, "hierarchy_id": 43652997}], "relation": {"object_id": 37631, "relation_id": 43639, "hierarchy_id": 43652997, "relation_data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "806374121010000001901", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003001900", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020310000030019", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0019", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 38528, "hierarchy_id": 43666877}], "relation": {"object_id": 38528, "relation_id": 44686, "hierarchy_id": 43666877, "relation_data": {"id": 44686, "name": "Комсомольский", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 102031, "isactive": 1, "isactual": 1, "objectid": 38528, "typename": "пр-кт", "startdate": "1900-01-01", "objectguid": "c876fdd0-5f9c-4389-9d98-f1bff7640520", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02:33:200108:122", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "47", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "806374121010000001920047000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2019-02-13"}], "object_id": 79959421, "hierarchy_id": 43669947}], "relation": {"object_id": 79959421, "relation_id": 48349501, "hierarchy_id": 43669947, "relation_data": {"id": 48349501, "nextid": 69241846, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2019-02-13", "addtype1": null, "addtype2": null, "changeid": 118837036, "housenum": "33", "isactive": 0, "isactual": 0, "objectid": 79959421, "housetype": 2, "startdate": "1900-01-01", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 10, "updatedate": "2019-02-16"}, "relation_type": "house", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02:33:200108:122", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "47", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "806374121010000001920047000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2019-02-13"}], "object_id": 79959421, "hierarchy_id": 43669947}], "relation": {"object_id": 79959421, "relation_id": 69241846, "hierarchy_id": 43669947, "relation_data": {"id": 69241846, "nextid": 0, "previd": 48349501, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 118837148, "housenum": "33", "isactive": 1, "isactual": 1, "objectid": 79959421, "housetype": 2, "startdate": "2019-02-13", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 20, "updatedate": "2019-02-16"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, Краснокамский р-н, с. Куяново, пр-кт Комсомольский, д. 33',
            $address->getCompleteShortAddress()
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

        // для поселков и тд район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());

        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getSettlementFiasId());
        $this->assertEquals('0203100000300', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());

        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getStreetFiasId());
        $this->assertEquals('02031000003001900', $address->getStreetKladrId());
        $this->assertEquals('пр-кт', $address->getStreetType());
        $this->assertEquals('проспект', $address->getStreetTypeFull());
        $this->assertEquals('Комсомольский', $address->getStreet());

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
        $this->assertEquals(43669947, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildSettlementFlat(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 43669964,
                'object_id' => 79960688,
                'path_ltree' => '5705.36249.37631.38528.79959421.79960688',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80237000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452930", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02031000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 36249, "hierarchy_id": 43309780}], "relation": {"object_id": 36249, "relation_id": 42085, "hierarchy_id": 43309780, "relation_data": {"id": 42085, "name": "Краснокамский", "level": "2", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 95842, "isactive": 1, "isactual": 1, "objectid": 36249, "typename": "р-н", "startdate": "1900-01-01", "objectguid": "c278cbbc-e209-4b0f-b20e-9c19ed6f6802", "opertypeid": 1, "updatedate": "2016-11-25"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0203100000300", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "806374121010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 37631, "hierarchy_id": 43652997}], "relation": {"object_id": 37631, "relation_id": 43639, "hierarchy_id": 43652997, "relation_data": {"id": 43639, "name": "Куяново", "level": "6", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 99535, "isactive": 1, "isactual": 1, "objectid": 37631, "typename": "с", "startdate": "1900-01-01", "objectguid": "3e805a9a-186b-4c0f-9eb2-acb750f77557", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "806374121010000001901", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "02031000003001900", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020310000030019", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0019", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 38528, "hierarchy_id": 43666877}], "relation": {"object_id": 38528, "relation_id": 44686, "hierarchy_id": 43666877, "relation_data": {"id": 44686, "name": "Комсомольский", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 102031, "isactive": 1, "isactual": 1, "objectid": 38528, "typename": "пр-кт", "startdate": "1900-01-01", "objectguid": "c876fdd0-5f9c-4389-9d98-f1bff7640520", "opertypeid": 1, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02:33:200108:122", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "47", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "806374121010000001920047000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2019-02-13"}], "object_id": 79959421, "hierarchy_id": 43669947}], "relation": {"object_id": 79959421, "relation_id": 48349501, "hierarchy_id": 43669947, "relation_data": {"id": 48349501, "nextid": 69241846, "previd": 0, "addnum1": null, "addnum2": null, "enddate": "2019-02-13", "addtype1": null, "addtype2": null, "changeid": 118837036, "housenum": "33", "isactive": 0, "isactual": 0, "objectid": 79959421, "housetype": 2, "startdate": "1900-01-01", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 10, "updatedate": "2019-02-16"}, "relation_type": "house", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "80637412101", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80237812001", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 3, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02:33:200108:122", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "47", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-02-13"}, {"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0231", "type_id": 4, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "806374121010000001920047000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2019-02-13"}], "object_id": 79959421, "hierarchy_id": 43669947}], "relation": {"object_id": 79959421, "relation_id": 69241846, "hierarchy_id": 43669947, "relation_data": {"id": 69241846, "nextid": 0, "previd": 48349501, "addnum1": null, "addnum2": null, "enddate": "2079-06-06", "addtype1": null, "addtype2": null, "changeid": 118837148, "housenum": "33", "isactive": 1, "isactual": 1, "objectid": 79959421, "housetype": 2, "startdate": "2019-02-13", "objectguid": "fc29d0da-e0aa-43a2-bd0e-4466332633aa", "opertypeid": 20, "updatedate": "2019-02-16"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "452946", "type_id": 5, "end_date": "2079-06-06", "start_date": "2017-06-01"}, {"value": "02:33:200108:141", "type_id": 8, "end_date": "2079-06-06", "start_date": "2017-06-01"}, {"value": "806374121010000001940047000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2017-06-01"}], "object_id": 79960688, "hierarchy_id": 43669964}], "relation": {"object_id": 79960688, "relation_id": 47823043, "hierarchy_id": 43669964, "relation_data": {"id": 47823043, "nextid": 0, "number": "2", "previd": 0, "enddate": "2079-06-06", "changeid": 118838821, "isactive": 1, "isactual": 1, "objectid": 79960688, "aparttype": 2, "startdate": "2017-06-01", "objectguid": "87d9a47b-7f3b-4860-ad59-470f29ece6d6", "opertypeid": 10, "updatedate": "2019-02-13"}, "relation_type": "apartment", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, Краснокамский р-н, с. Куяново, пр-кт Комсомольский, д. 33, кв. 2',
            $address->getCompleteShortAddress()
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

        // для поселков и тд район заполнен
        $this->assertEquals('c278cbbc-e209-4b0f-b20e-9c19ed6f6802', $address->getAreaFiasId());
        $this->assertEquals('0203100000000', $address->getAreaKladrId());
        $this->assertEquals('р-н', $address->getAreaType());
        $this->assertEquals('район', $address->getAreaTypeFull());
        $this->assertEquals('Краснокамский', $address->getArea());

        $this->assertEquals('3e805a9a-186b-4c0f-9eb2-acb750f77557', $address->getSettlementFiasId());
        $this->assertEquals('0203100000300', $address->getSettlementKladrId());
        $this->assertEquals('с.', $address->getSettlementType());
        $this->assertEquals('село', $address->getSettlementTypeFull());
        $this->assertEquals('Куяново', $address->getSettlement());

        $this->assertEquals('c876fdd0-5f9c-4389-9d98-f1bff7640520', $address->getStreetFiasId());
        $this->assertEquals('02031000003001900', $address->getStreetKladrId());
        $this->assertEquals('пр-кт', $address->getStreetType());
        $this->assertEquals('проспект', $address->getStreetTypeFull());
        $this->assertEquals('Комсомольский', $address->getStreet());

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
        $this->assertEquals(43669964, $address->getFiasHierarchyId());
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
    public function itCorrectlyBuildFlatWithAdditionalNumbers(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 3740424,
                'object_id' => 70027141,
                'path_ltree' => '5705.6143.7280.70027141',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}], "object_id": 6143, "hierarchy_id": 3245176}], "relation": {"object_id": 6143, "relation_id": 6890, "hierarchy_id": 3245176, "relation_data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0002", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000030000002", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "02000003000000200", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-07-10"}, {"value": "807270000010000000201", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-07-10"}], "object_id": 7280, "hierarchy_id": 3683317}], "relation": {"object_id": 7280, "relation_id": 8472, "hierarchy_id": 3683317, "relation_data": {"id": 8472, "name": "Социалистическая", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 23087, "isactive": 1, "isactual": 1, "objectid": 7280, "typename": "ул", "startdate": "1900-01-01", "objectguid": "b008fb9b-72d8-4949-9eef-d1935589e84d", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "807270000010000000220352000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "352", "type_id": 15, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "02:66:010101:992", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2019-05-30"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}], "object_id": 70027141, "hierarchy_id": 3740424}], "relation": {"object_id": 70027141, "relation_id": 42233509, "hierarchy_id": 3740424, "relation_data": {"id": 42233509, "nextid": 0, "previd": 0, "addnum1": "4", "addnum2": null, "enddate": "2079-06-06", "addtype1": 2, "addtype2": null, "changeid": 104351076, "housenum": "10А", "isactive": 1, "isactual": 1, "objectid": 70027141, "housetype": 5, "startdate": "2019-05-30", "objectguid": "b9433c6d-574a-4224-8197-0f01a5671f68", "opertypeid": 10, "updatedate": "2019-05-30"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, ул. Социалистическая, зд. 10А, стр. 4',
            $address->getCompleteShortAddress()
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
                'hierarchy_id' => 5128655,
                'object_id' => 36105517,
                'path_ltree' => '5705.6177.7215.36105517',
                'parents' => '[{"params": [{"values": [{"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "80423000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80723000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0261", "type_id": 1, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "0261", "type_id": 2, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "02000007000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000700000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "0262", "type_id": 3, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "807230000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 6177, "hierarchy_id": 5006413}], "relation": {"object_id": 6177, "relation_id": 6940, "hierarchy_id": 5006413, "relation_data": {"id": 6940, "name": "Кумертау", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19453, "isactive": 1, "isactual": 1, "objectid": 6177, "typename": "г", "startdate": "1900-01-01", "objectguid": "48e38991-07fd-4aaa-b240-a7280e4a823f", "opertypeid": 1, "updatedate": "2013-01-07"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0262", "type_id": 3, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "807230000010000001401", "type_id": 13, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "02000007000001400", "type_id": 10, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "0262", "type_id": 4, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "80723000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0261", "type_id": 2, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "0261", "type_id": 1, "end_date": "2079-06-06", "start_date": "2013-01-01"}, {"value": "0014", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "020000070000014", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "453303", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80423000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 7215, "hierarchy_id": 5127049}], "relation": {"object_id": 7215, "relation_id": 8388, "hierarchy_id": 5127049, "relation_data": {"id": 8388, "name": "Брикетная", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 22885, "isactive": 1, "isactual": 1, "objectid": 7215, "typename": "ул", "startdate": "1900-01-01", "objectguid": "d4fd2f5a-8c4a-4b05-b8d1-6b0c14f9a392", "opertypeid": 1, "updatedate": "2013-01-07"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "12", "type_id": 15, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "453310", "type_id": 5, "end_date": "2079-06-06", "start_date": "2020-08-15"}, {"value": "80723000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}, {"value": "0261", "type_id": 1, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "0261", "type_id": 2, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "0262", "type_id": 3, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "0262", "type_id": 4, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "80423000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "807230000010000001420012000000000", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-07-09"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2015-07-09"}], "object_id": 36105517, "hierarchy_id": 5128655}], "relation": {"object_id": 36105517, "relation_id": 21463473, "hierarchy_id": 5128655, "relation_data": {"id": 21463473, "nextid": 0, "previd": 0, "addnum1": "А", "addnum2": "1/6", "enddate": "2079-06-06", "addtype1": 1, "addtype2": 2, "changeid": 54819930, "housenum": "5", "isactive": 1, "isactual": 1, "objectid": 36105517, "housetype": 1, "startdate": "2015-07-09", "objectguid": "f581b200-3843-4cc6-baba-c35efe08f5a5", "opertypeid": 10, "updatedate": "2019-07-10"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Кумертау, ул. Брикетная, влд. 5, корп. А, стр. 1/6',
            $address->getCompleteShortAddress()
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
                'hierarchy_id' => 73109373,
                'object_id' => 80354205,
                'path_ltree' => '1325381.1325680.1329639.80354205',
                'parents' => '[{"params": [{"values": [{"value": "73000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "7300", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "7300", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "73000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "433000", "type_id": 5, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "7300000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "73000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "730000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Ульяновская область", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 1325381, "hierarchy_id": 70902160}], "relation": {"object_id": 1325381, "relation_id": 1637437, "hierarchy_id": 70902160, "relation_data": {"id": 1637437, "name": "Ульяновская", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630187, "isactive": 1, "isactual": 1, "objectid": 1325381, "typename": "обл", "startdate": "1900-01-01", "objectguid": "fee76045-fe22-43a4-ad58-ad99e903bd58", "opertypeid": 1, "updatedate": "2015-09-15"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2021-02-11"}, {"value": "73000001000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "7300000100000", "type_id": 10, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "73401000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "737010000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 1325680, "hierarchy_id": 72235435}], "relation": {"object_id": 1325680, "relation_id": 1637755, "hierarchy_id": 72235435, "relation_data": {"id": 1637755, "name": "Ульяновск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3630950, "isactive": 1, "isactual": 1, "objectid": 1325680, "typename": "г", "startdate": "1900-01-01", "objectguid": "bebfd75d-a0da-4bf9-8307-2e2c85eac463", "opertypeid": 1, "updatedate": "2018-10-26"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "730000010000766", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "73000001000076600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-11-25"}, {"value": "737010000000000076601", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-11-25"}, {"value": "7327", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "7327", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "73401373000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2020-07-08"}, {"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-07-07"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0766", "type_id": 15, "end_date": "2079-06-06", "start_date": "1900-01-01"}], "object_id": 1329639, "hierarchy_id": 73038661}], "relation": {"object_id": 1329639, "relation_id": 1642081, "hierarchy_id": 73038661, "relation_data": {"id": 1642081, "name": "Московское", "level": "8", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 3640299, "isactive": 1, "isactual": 1, "objectid": 1329639, "typename": "ш", "startdate": "1900-01-01", "objectguid": "5040339e-9130-490e-bd7a-f209dd36a4a4", "opertypeid": 1, "updatedate": "2015-11-29"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "7327", "type_id": 1, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "309", "type_id": 15, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "7327", "type_id": 2, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "73401373000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "73:24:030803:854", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "737010000000000076620309000000010", "type_id": 13, "end_date": "2079-06-06", "start_date": "2021-02-04"}, {"value": "737010000000000076620309000000005", "type_id": 13, "end_date": "2021-02-04", "start_date": "2019-01-30"}, {"value": "432045", "type_id": 5, "end_date": "2079-06-06", "start_date": "2020-08-15"}], "object_id": 80354205, "hierarchy_id": 73109373}], "relation": {"object_id": 80354205, "relation_id": 70331499, "hierarchy_id": 73109373, "relation_data": {"id": 70331499, "nextid": null, "previd": 48600378, "addnum1": "2", "addnum2": "Б,б,б1,Л", "enddate": "2079-06-06", "addtype1": 1, "addtype2": 4, "changeid": 174547397, "housenum": "9-А", "isactive": 1, "isactual": 1, "objectid": 80354205, "housetype": 2, "startdate": "2021-02-04", "objectguid": "fd7c161b-0765-4e54-9517-1c49f50e03ce", "opertypeid": 20, "updatedate": "2021-02-04"}, "relation_type": "house", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "7327", "type_id": 1, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "309", "type_id": 15, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "7327", "type_id": 2, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "73701000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "73401373000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "73:24:030803:854", "type_id": 8, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "1", "type_id": 14, "end_date": "2079-06-06", "start_date": "2019-01-30"}, {"value": "737010000000000076620309000000010", "type_id": 13, "end_date": "2079-06-06", "start_date": "2021-02-04"}, {"value": "737010000000000076620309000000005", "type_id": 13, "end_date": "2021-02-04", "start_date": "2019-01-30"}, {"value": "432045", "type_id": 5, "end_date": "2079-06-06", "start_date": "2020-08-15"}], "object_id": 80354205, "hierarchy_id": 73109373}], "relation": {"object_id": 80354205, "relation_id": 48600378, "hierarchy_id": 73109373, "relation_data": {"id": 48600378, "nextid": 70331499, "previd": 0, "addnum1": "6", "addnum2": null, "enddate": "2021-02-04", "addtype1": 2, "addtype2": null, "changeid": 119417796, "housenum": "9А/2", "isactive": 0, "isactual": 0, "objectid": 80354205, "housetype": 5, "startdate": "2019-01-30", "objectguid": "fd7c161b-0765-4e54-9517-1c49f50e03ce", "opertypeid": 10, "updatedate": "2021-02-04"}, "relation_type": "house", "relation_is_active": 0, "relation_is_actual": 0}}]',
            ]
        );

        // todo: обл. в конце
        $this->assertEquals(
            'обл. Ульяновская, г. Ульяновск, ш. Московское, д. 9-А, корп. 2, лит. Б,б,б1,Л',
            $address->getCompleteShortAddress()
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
     * @test2
     */
    public function itCorrectlyBuildSnt(): void
    {
        $address = $this->builder->build(
            [
                'hierarchy_id' => 3277596,
                'object_id' => 11454,
                'path_ltree' => '5705.6143.5791.11454',
                'parents' => '[{"params": [{"values": [{"value": "80000000", "type_id": 7, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "800000000000000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "Республика Башкортостан", "type_id": 16, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000000000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80000000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "452000", "type_id": 5, "end_date": "2079-06-06", "start_date": "2015-12-01"}, {"value": "0200", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0200000000000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2015-12-01"}], "object_id": 5705, "hierarchy_id": 1}], "relation": {"object_id": 5705, "relation_id": 6356, "hierarchy_id": 1, "relation_data": {"id": 6356, "name": "Башкортостан", "level": "1", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 17925, "isactive": 1, "isactual": 1, "objectid": 5705, "typename": "Респ", "startdate": "1900-01-01", "objectguid": "6f2cbfd8-692a-4ee4-9b16-067210bde3fc", "opertypeid": 1, "updatedate": "2016-02-27"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "807270000010000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "0200000300000", "type_id": 10, "end_date": "2079-06-06", "start_date": "2016-08-31"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "02000003000", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000000", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000001", "type_id": 7, "end_date": "2079-06-06", "start_date": "2020-03-05"}], "object_id": 6143, "hierarchy_id": 3245176}], "relation": {"object_id": 6143, "relation_id": 6890, "hierarchy_id": 3245176, "relation_data": {"id": 6890, "name": "Нефтекамск", "level": "5", "nextid": 0, "previd": 0, "enddate": "2079-06-06", "changeid": 19302, "isactive": 1, "isactual": 1, "objectid": 6143, "typename": "г", "startdate": "1900-01-01", "objectguid": "2c9997d2-ce94-431a-96c9-722d2238d5c8", "opertypeid": 1, "updatedate": "2016-08-31"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "02000003006", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-08-15"}, {"value": "807270001310000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-08-15"}], "object_id": 5791, "hierarchy_id": 3253661}], "relation": {"object_id": 5791, "relation_id": 6460, "hierarchy_id": 3253661, "relation_data": {"id": 6460, "name": "Энергетик", "level": "6", "nextid": 6468, "previd": 0, "enddate": "2013-10-30", "changeid": 18195, "isactive": 0, "isactual": 0, "objectid": 5791, "typename": "с", "startdate": "1900-01-01", "objectguid": "0823f2aa-86e2-4584-8523-5f487fff95ab", "opertypeid": 1, "updatedate": "2014-08-20"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "02000003006", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-08-15"}, {"value": "807270001310000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-08-15"}], "object_id": 5791, "hierarchy_id": 3253661}], "relation": {"object_id": 5791, "relation_id": 6468, "hierarchy_id": 3253661, "relation_data": {"id": 6468, "name": "Энергетик", "level": "6", "nextid": 6473, "previd": 6460, "enddate": "2014-08-15", "changeid": 18221, "isactive": 0, "isactual": 0, "objectid": 5791, "typename": "п", "startdate": "2013-10-30", "objectguid": "0823f2aa-86e2-4584-8523-5f487fff95ab", "opertypeid": 20, "updatedate": "2014-01-06"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "02000003006", "type_id": 11, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "1900-01-01"}, {"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2014-01-05"}, {"value": "0200000300600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2014-08-15"}, {"value": "807270001310000000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2014-08-15"}], "object_id": 5791, "hierarchy_id": 3253661}], "relation": {"object_id": 5791, "relation_id": 6473, "hierarchy_id": 3253661, "relation_data": {"id": 6473, "name": "Энергетик", "level": "6", "nextid": 0, "previd": 6468, "enddate": "2079-06-06", "changeid": 18238, "isactive": 1, "isactual": 1, "objectid": 5791, "typename": "с", "startdate": "2014-08-15", "objectguid": "0823f2aa-86e2-4584-8523-5f487fff95ab", "opertypeid": 20, "updatedate": "2015-09-15"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0006", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-09-28"}, {"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-09-20"}], "object_id": 11454, "hierarchy_id": 3274655}, {"values": [{"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "0006", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-09-28"}], "object_id": 11454, "hierarchy_id": 3277596}], "relation": {"object_id": 11454, "relation_id": 13676, "hierarchy_id": 3277596, "relation_data": {"id": 13676, "name": "СНТ Родничок", "level": "15", "nextid": 13698, "previd": 0, "enddate": "2016-09-28", "changeid": 33068, "isactive": 0, "isactual": 0, "objectid": 11454, "typename": "снт", "startdate": "2016-03-18", "objectguid": "a4697fc8-eced-4078-881c-2d400a12af21", "opertypeid": 10, "updatedate": "2017-12-10"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0006", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-09-28"}, {"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-09-20"}], "object_id": 11454, "hierarchy_id": 3274655}, {"values": [{"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "0006", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-09-28"}], "object_id": 11454, "hierarchy_id": 3277596}], "relation": {"object_id": 11454, "relation_id": 13698, "hierarchy_id": 3277596, "relation_data": {"id": 13698, "name": "СНТ Родничок", "level": "7", "nextid": 13707, "previd": 13676, "enddate": "2018-09-20", "changeid": 33093, "isactive": 0, "isactual": 0, "objectid": 11454, "typename": "снт", "startdate": "2016-09-28", "objectguid": "a4697fc8-eced-4078-881c-2d400a12af21", "opertypeid": 50, "updatedate": "2018-09-25"}, "relation_type": "addr_obj", "relation_is_active": 0, "relation_is_actual": 0}},{"params": [{"values": [{"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0006", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-09-28"}, {"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-09-20"}], "object_id": 11454, "hierarchy_id": 3274655}, {"values": [{"value": "80727000131", "type_id": 7, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "80427000003", "type_id": 6, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "020000030060006", "type_id": 11, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 2, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0", "type_id": 14, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "0264", "type_id": 1, "end_date": "2079-06-06", "start_date": "2016-03-18"}, {"value": "807270001310006000001", "type_id": 13, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "02000003006000600", "type_id": 10, "end_date": "2079-06-06", "start_date": "2018-09-20"}, {"value": "0006", "type_id": 15, "end_date": "2079-06-06", "start_date": "2016-09-28"}], "object_id": 11454, "hierarchy_id": 3277596}], "relation": {"object_id": 11454, "relation_id": 13707, "hierarchy_id": 3277596, "relation_data": {"id": 13707, "name": "Родничок", "level": "7", "nextid": 0, "previd": 13698, "enddate": "2079-06-06", "changeid": 33103, "isactive": 1, "isactual": 1, "objectid": 11454, "typename": "тер. СНТ", "startdate": "2018-09-20", "objectguid": "a4697fc8-eced-4078-881c-2d400a12af21", "opertypeid": 20, "updatedate": "2018-12-28"}, "relation_type": "addr_obj", "relation_is_active": 1, "relation_is_actual": 1}}]',
            ]
        );

        $this->assertEquals(
            'респ. Башкортостан, г. Нефтекамск, п. Энергетик, тер. СНТ Родничок',
            $address->getCompleteShortAddress()
        );

        // предыдущие уровни заполнены
        $this->assertEquals('6f2cbfd8-692a-4ee4-9b16-067210bde3fc', $address->getRegionFiasId());
        $this->assertEquals('0200000000000', $address->getRegionKladrId());
        $this->assertEquals('респ.', $address->getRegionType());
        $this->assertEquals('республика', $address->getRegionTypeFull());
        $this->assertEquals('Башкортостан', $address->getRegion());

        // для нас. пунктов внутри города - город заполнен
        $this->assertEquals('2c9997d2-ce94-431a-96c9-722d2238d5c8', $address->getCityFiasId());
        $this->assertEquals('0200000300000', $address->getCityKladrId());
        $this->assertEquals('г.', $address->getCityType());
        $this->assertEquals('город', $address->getCityTypeFull());
        $this->assertEquals('Нефтекамск', $address->getCity());

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
        $this->assertEquals('f5b6853e-7787-4127-b60a-a2bcc96a9b3f', $address->getFiasId());
        $this->assertEquals(3245193, $address->getFiasHierarchyId());
        $this->assertEquals(FiasLevel::SETTLEMENT, $address->getFiasLevel());
        $this->assertEquals(AddressLevel::SETTLEMENT, $address->getAddressLevel());
        $this->assertEquals('0200000300400', $address->getKladrId());
        $this->assertEquals('80427807004', $address->getOkato());
        $this->assertEquals('80727000121', $address->getOktmo());
        $this->assertEquals(null, $address->getPostalCode());
        $this->assertEmpty($address->getSynonyms());
    }
}