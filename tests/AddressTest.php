<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Tests;

use Addresser\FiasAddressStructure\Address;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AddressTest extends TestCase
{
    /**
     * @test
     */
    public function itIsSerializableADeserializable(): void
    {
        $address = new Address();
        $address->setFiasId('730824b6-1b9b-11ec-9621-0242ac130002');
        $address->setFiasObjectId(1);
        $address->setFiasLevel(2);
        $address->setAddressLevel(2);
        $address->setKladrId('0200000300000');
        $address->setOkato('80427000000');
        $address->setOktmo('465445');
        $address->setPostalCode('452944');
        $address->setRegionFiasId('73082bc8-1b9b-11ec-9621-0242ac130002');
        $address->setRegionKladrId('0200000300001');
        $address->setRegionType('респ.');
        $address->setRegionTypeFull('республика');
        $address->setRegion('Башкортостан');
        $address->setRegionWithType('regionWithType');
        $address->setRegionWithFullType('regionWithFullType');
        $address->setAreaFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setAreaKladrId('areaKladrId');
        $address->setAreaType('areaType');
        $address->setAreaTypeFull('areaTypeFull');
        $address->setArea('area');
        $address->setAreaWithType('areaWithType');
        $address->setAreaWithFullType('areaWithFullType');
        $address->setCityFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setCityKladrId('cityKladrId');
        $address->setCityType('cityType');
        $address->setCityTypeFull('cityTypeFull');
        $address->setCity('city');
        $address->setCityWithType('cityWithType');
        $address->setCityWithFullType('cityWithFullType');
        $address->setSettlementFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setSettlementKladrId('settlementKladrId');
        $address->setSettlementType('settlementType');
        $address->setSettlementTypeFull('settlementTypeFull');
        $address->setSettlement('settlement');
        $address->setSettlementWithType('settlementWithType');
        $address->setSettlementWithFullType('settlementWithFullType');
        $address->setTerritoryFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setTerritoryKladrId('territoryKladrId');
        $address->setTerritoryType('territoryType');
        $address->setTerritoryTypeFull('territoryTypeFull');
        $address->setTerritory('territory');
        $address->setTerritoryWithType('territoryWithType');
        $address->setTerritoryWithFullType('territoryWithFullType');
        $address->setStreetFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setStreetKladrId('streetKladrId');
        $address->setStreetType('streetType');
        $address->setStreetTypeFull('streetTypeFull');
        $address->setStreet('street');
        $address->setStreetWithType('streetWithType');
        $address->setStreetWithFullType('streetWithFullType');
        $address->setHouseFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setHouseKladrId('houseKladrId');
        $address->setHouseType('houseType');
        $address->setHouseTypeFull('houseTypeFull');
        $address->setHouse('house');
        $address->setBlockType1('blockType1');
        $address->setBlockTypeFull1('blockTypeFull1');
        $address->setBlock1('block1');
        $address->setBlockType2('blockType2');
        $address->setBlockTypeFull2('blockTypeFull2');
        $address->setBlock2('block2');
        $address->setFlatFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setFlatType('flatType');
        $address->setFlatTypeFull('flatTypeFull');
        $address->setFlat('flat');
        $address->setRoomFiasId('7308399c-1b9b-11ec-9621-0242ac130002');
        $address->setRoomType('roomType');
        $address->setRoomTypeFull('roomTypeFull');
        $address->setRoom('room');
        $address->setSynonyms(['synonyms']);
        $address->setRenaming(['renaming']);
        $address->setLocationFromArray([45.45565, 35.45655]);
        $address->setDeltaVersion(20200505);

        $this->assertEquals($address, Address::fromArray($address->toArray()));
    }
}
