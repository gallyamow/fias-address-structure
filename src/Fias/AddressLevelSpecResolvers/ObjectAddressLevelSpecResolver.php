<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\LevelNameSpecNotFoundException;
use Addresser\AddressRepository\Exceptions\WrongAddressLevelResolvingException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;
use Addresser\AddressRepository\Fias\FiasLevel;

/**
 * Так как нужна нормализация - решил все сделать hardcoded чтобы обойтись без все равно огромного normalizer.
 * Правила: 1) сокращения слов оканчиваются точкой, аббревиатуры без точек 2) lowercase для всего кроме аббревиатур.
 */
class ObjectAddressLevelSpecResolver implements AddressLevelSpecResolverInterface
{
    // sql-запрос для построения этих вариантов:
    // select '[''name'' => '''||name||''', ''shortName'' => ['||shortnames||'],''namePosition'' => AddressLevelSpec::NAME_POSITION_BEFORE,''variants'' => [[''names'' => ['||shortnames||'], ''levels'' => ['||levels||']]]],'
    //from (
    //         select name, '''' || array_to_string(shortnames, ''',''') || '''' as shortnames, array_to_string(levels, ',') as levels
    //         from (
    //                  select name, array_agg(DISTINCT shortname) shortnames, array_agg(DISTINCT level) levels
    //                  from gar.addr_obj_types
    //                  group by name
    //                  order by name
    //              ) as t
    //     ) as t2
    private const VARIANTS = [
        [
            'name' => 'аал',
            'shortName' => 'аал',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['аал'], 'levels' => [6, 7, 16]]],
        ],
        [
            'name' => 'абонентский ящик',
            'shortName' => 'а/я',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['а/я'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'автодорога',
            'shortName' => 'автодорога',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['автодорога'], 'levels' => [6]]],
        ],
        [
            'name' => 'автономная область',
            'shortName' => 'авт. обл', // изменил (так как АО пересекается на level = 1)
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['Аобл', 'а.обл.'], 'levels' => [1]]],
        ],
        [
            'name' => 'автономный округ',
            'shortName' => 'АО', // повторяется
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['АО', 'а.окр.'], 'levels' => [1, 2, 13]]],
        ],
        [
            'name' => 'аллея',
            'shortName' => 'ал.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ал.', 'аллея'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'арбан',
            'shortName' => 'арбан',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['арбан'], 'levels' => [6, 7, 16]]],
        ],
        [
            'name' => 'аул',
            'shortName' => 'аул',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['аул'], 'levels' => [6, 7, 16]]],
        ],
        [
            'name' => 'балка',
            'shortName' => 'балка',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['балка'], 'levels' => [8]]],
        ],
        [
            'name' => 'берег',
            'shortName' => 'бер.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['б-г', 'берег'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'бугор',
            'shortName' => 'буг.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['бугор'], 'levels' => [8]]],
        ],
        [
            'name' => 'бульвар',
            'shortName' => 'б-р',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['б-р'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'вал',
            'shortName' => 'вал',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['вал'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'взвоз',
            'shortName' => 'взв.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['взв.'], 'levels' => [8]]],
        ],
        [
            'name' => 'внутригородская территория',
            'shortName' => 'вн.тер.г',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['вн.тер. г.', 'вн.тер.г.'], 'levels' => [2, 3,]]],
        ],
        [
            'name' => 'внутригородской район',
            'shortName' => 'вн.р-н',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['вн.р-н'], 'levels' => [4]]],
        ],
        [
            'name' => 'волость',
            'shortName' => 'вол.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['волость'], 'levels' => [5, 6]]],
        ],
        [
            'name' => 'въезд',
            'shortName' => 'взд.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['взд.', 'въезд'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'выселок',
            'shortName' => 'высел',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['высел'], 'levels' => [6, 16]]],
        ],
        [
            'name' => 'гаражно-строительный кооператив',
            'shortName' => 'гск',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['гск'], 'levels' => [7, 8, 15, 16]]],
        ],
        [
            'name' => 'город',
            'shortName' => 'г.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['г', 'г.'], 'levels' => [1, 2, 5, 6]]],
        ],
        [
            'name' => 'городок',
            'shortName' => 'г-к',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['г-к'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'городское поселение',
            'shortName' => 'г.п.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['г.п.'], 'levels' => [4]]],
        ],
        [
            'name' => 'городской округ',
            'shortName' => 'г.о.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['г.о.'], 'levels' => [2, 3]]],
        ],
        [
            'name' => 'городской поселок',
            'shortName' => 'гп',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['гп'], 'levels' => [6]]],
        ],
        [
            'name' => 'город федерального значения',
            'shortName' => 'г.ф.з.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['г.ф.з.'], 'levels' => [1]]],
        ],
        [
            'name' => 'дачное некоммерческое партнерство',
            'shortName' => 'днп',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['днп'], 'levels' => [7, 8, 15]]],
        ],
        [
            'name' => 'дачный поселок',
            'shortName' => 'дп.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['дп', 'дп.'], 'levels' => [5, 6]]],
        ],
        [
            'name' => 'деревня',
            'shortName' => 'дер.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['д', 'д.'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'дорога',
            'shortName' => 'дор.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['дор', 'дор.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'ж/д обгонный пункт',
            'shortName' => 'ж/д оп', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д_оп'], 'levels' => [6, 8, 16]]],
        ],
        [
            'name' => 'железнодорожная будка',
            'shortName' => 'ж/д б-ка',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д б-ка', 'ж/д_будка'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'железнодорожная ветка',
            'shortName' => 'ж/д ветка', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д в-ка'], 'levels' => [6]]],
        ],
        [
            'name' => 'железнодорожная казарма',
            'shortName' => 'ж/д к-ма',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д_казарм', 'ж/д к-ма'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'железнодорожная платформа',
            'shortName' => 'ж/д пл-ма',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д_платф', 'ж/д пл-ма'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'железнодорожная площадка',
            'shortName' => 'ж/д пл-ка',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д пл-ка'], 'levels' => [6]]],
        ],
        [
            'name' => 'железнодорожная станция',
            'shortName' => 'ж/д ст.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д ст.', 'ж/д_ст'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'железнодорожный блокпост',
            'shortName' => 'ж/д бл-ст',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д бл-ст'], 'levels' => [6]]],
        ],
        [
            'name' => 'железнодорожный комбинат',
            'shortName' => 'ж/д к-т',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д к-т'], 'levels' => [6]]],
        ],
        [
            'name' => 'железнодорожный остановочный пункт',
            'shortName' => 'ж/д о.п.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д о.п.'], 'levels' => [6]]],
        ],
        [
            'name' => 'железнодорожный пост',
            'shortName' => 'ж/д пост',  // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д_пост'], 'levels' => [6, 8, 16]]],
        ],
        [
            'name' => 'железнодорожный путевой пост',
            'shortName' => 'ж/д п.п.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д п.п.'], 'levels' => [6]]],
        ],
        [
            'name' => 'железнодорожный разъезд',
            'shortName' => 'ж/д рзд.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ж/д рзд.', 'ж/д_рзд'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'животноводческая точка',
            'shortName' => 'жт',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['жт'], 'levels' => [8, 16]]],
        ],
        [
            'name' => 'жилая зона',
            'shortName' => 'жилзона',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['жилзона'], 'levels' => [6]]],
        ],
        [
            'name' => 'жилой район',
            'shortName' => 'ж/р',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['жилрайон', 'ж/р'], 'levels' => [6, 7]]],
        ],
        [
            'name' => 'заезд',
            'shortName' => 'ззд.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['заезд', 'ззд.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'заимка',
            'shortName' => 'заим.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['заимка'], 'levels' => [6]]],
        ],
        [
            'name' => 'земельный участок',
            'shortName' => 'з/у',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['з/у'], 'levels' => [9]]],
        ],
        [
            'name' => 'зимовье',
            'shortName' => 'зим.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['зим.'], 'levels' => [6]]],
        ],
        [
            'name' => 'зона',
            'shortName' => 'зона',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['зона'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'казарма',
            'shortName' => 'казарма',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['казарма'], 'levels' => [6, 8, 16]]],
        ],
        [
            'name' => 'квартал',
            'shortName' => 'кв-л',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['кв-л'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'километр',
            'shortName' => 'км',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['км'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'кишлак',
            'shortName' => 'киш.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['киш.'], 'levels' => [6]]],
        ],
        [
            'name' => 'кольцо',
            'shortName' => 'к-цо',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['кольцо', 'к-цо'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'кордон',
            'shortName' => 'корд.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['кордон'], 'levels' => [6]]],
        ],
        [
            'name' => 'коса',
            'shortName' => 'коса',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['коса'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'край',
            'shortName' => 'край',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['край'], 'levels' => [1]]],
        ],
        [
            'name' => 'курортный поселок',
            'shortName' => 'кп',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['кп'], 'levels' => [5, 6]]],
        ],
        [
            'name' => 'леспромхоз',
            'shortName' => 'лпх',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['лпх'], 'levels' => [6]]],
        ],
        [
            'name' => 'линия',
            'shortName' => 'лн.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['линия', 'лн.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'магистраль',
            'shortName' => 'мгстр.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['мгстр.'], 'levels' => [8]]],
        ],
        [
            'name' => 'массив',
            'shortName' => 'массив',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['массив'], 'levels' => [5, 6]]],
        ],
        [
            'name' => 'машино-место',
            'shortName' => 'м/м',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['м/м'], 'levels' => [17]]],
        ],
        [
            'name' => 'маяк',
            'shortName' => 'маяк',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['маяк'], 'levels' => [8]]],
        ],
        [
            'name' => 'межселенная территория',
            'shortName' => 'межсел.тер.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['межсел.тер.'], 'levels' => [4]]],
        ],
        [
            'name' => 'местечко',
            'shortName' => 'м-ко',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['м', 'м-ко'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'местность',
            'shortName' => 'мест.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['местность'], 'levels' => [7, 8, 15]]],
        ],
        [
            'name' => 'месторождение',
            'shortName' => 'мр.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['месторожд.', 'мр.'], 'levels' => [7]]],
        ],
        [
            'name' => 'микрорайон',
            'shortName' => 'мкр',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['мкр', 'мкр.'], 'levels' => [6, 7, 8, 15, 16]]],
        ],
        [
            'name' => 'мост',
            'shortName' => 'мост',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['мост'], 'levels' => [8, 16]]],
        ],
        [
            'name' => 'муниципальный округ',
            'shortName' => 'м.о.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['м.о.'], 'levels' => [3]]],
        ],
        [
            'name' => 'муниципальный район',
            'shortName' => 'м.р-н',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['м.р-н'], 'levels' => [2, 3]]],
        ],
        [
            'name' => 'набережная',
            'shortName' => 'наб.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['наб', 'наб.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'населенный пункт',
            'shortName' => 'нп',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['нп', 'нп.'], 'levels' => [6, 8, 16]]],
        ],
        [
            'name' => 'некоммерческое партнерство',
            'shortName' => 'н/п',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['н/п'], 'levels' => [7, 8, 15]]],
        ],
        [
            'name' => 'область',
            'shortName' => 'обл.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['обл', 'обл.'], 'levels' => [1]]],
        ],
        [
            'name' => 'округ',
            'shortName' => 'окр.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['округ'], 'levels' => [1]]],
        ],
        [
            'name' => 'остров',
            'shortName' => 'ост-в',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ост-в', 'остров'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'парк',
            'shortName' => 'парк',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['парк'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'переезд',
            'shortName' => 'пер-д',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пер-д', 'переезд'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'переулок',
            'shortName' => 'пер.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пер', 'пер.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'планировочный район',
            'shortName' => 'пл.р-н',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пл.р-н'], 'levels' => [6, 8, 16]]],
        ],
        [
            'name' => 'платформа',
            'shortName' => 'платф.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['платф', 'платф.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'площадка',
            'shortName' => 'пл-ка',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пл-ка'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'площадь',
            'shortName' => 'пл.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пл', 'пл.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'погост',
            'shortName' => 'погост',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['погост'], 'levels' => [6]]],
        ],
        [
            'name' => 'полустанок',
            'shortName' => 'полустанок',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['полустанок'], 'levels' => [8]]],
        ],
        [
            'name' => 'порт',
            'shortName' => 'порт',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['порт'], 'levels' => [7]]],
        ],
        [
            'name' => 'поселение',
            'shortName' => 'пос.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п', 'п.', 'пос.', 'пос'], 'levels' => [2]]], // изменил
        ],
        [
            'name' => 'поселок',
            'shortName' => 'п.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п', 'п.', 'пос.', 'пос'], 'levels' => [5, 6, 7, 8, 16]]], // пос.
        ],
        [
            'name' => 'поселок городского типа',
            'shortName' => 'пгт',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пгт', 'пгт.'], 'levels' => [5, 6]]],
        ],
        [
            'name' => 'поселок при станции',
            'shortName' => 'п/ст',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п/ст', 'п. ст.'], 'levels' => [6, 8, 16]]],
        ],
        [
            'name' => 'поселок при железнодорожной станции',
            'shortName' => 'п. ж/д ст.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п. ж/д ст.'], 'levels' => [6]]],
        ],
        [
            'name' => 'поселок разъезда',
            'shortName' => 'пос.рзд',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пос.рзд'], 'levels' => [6]]],
        ],
        [
            'name' => 'починок',
            'shortName' => 'п-к',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п-к', 'починок'], 'levels' => [6, 7, 16]]],
        ],
        [
            'name' => 'почтовое отделение',
            'shortName' => 'п/о',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п/о'], 'levels' => [5, 6, 8, 16]]],
        ],
        [
            'name' => 'проезд',
            'shortName' => 'пр-д',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пр-д', 'проезд'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'промышленная зона',
            'shortName' => 'промзона',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['промзона'], 'levels' => [8, 6, 7, 15]]], // изменил
        ],
        [
            'name' => 'промышленный район',
            'shortName' => 'п/р',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['п/р'], 'levels' => [7]]],
        ],
        [
            'name' => 'просека',
            'shortName' => 'просека', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пр-к', 'просек', 'пр-ка', 'просека'], 'levels' => [7, 8, 16]]], // изменил
        ],
        [
            'name' => 'проселок',
            'shortName' => 'проселок',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пр-лок', 'проселок'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'проспект',
            'shortName' => 'пр-кт',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['пр-кт'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'проулок',
            'shortName' => 'проул.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['проул.', 'проулок'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'рабочий поселок',
            'shortName' => 'рп',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['рп'], 'levels' => [5, 6]]],
        ],
        [
            'name' => 'разъезд',
            'shortName' => 'рзд.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['рзд', 'рзд.'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'район',
            'shortName' => 'р-н',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['р-н'], 'levels' => [2, 7, 14]]],
        ],
        [
            'name' => 'республика',
            'shortName' => 'респ.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['респ.', 'Респ', 'Чувашия'], 'levels' => [1]]], // изменил
        ],
        [
            'name' => 'ряды',
            'shortName' => 'ряды',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ряды', 'ряд'], 'levels' => [8, 16]]],
        ],
        [
            'name' => 'сад',
            'shortName' => 'сад',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['сад'], 'levels' => [7, 8, 15, 16]]],
        ],
        [
            'name' => 'садовое товарищество',
            'shortName' => 'снт',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['снт', 'с/т'], 'levels' => [6, 7, 8, 16]]], // изменил
        ],
        [
            'name' => 'село',
            'shortName' => 'с.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с', 'с.'], 'levels' => [5, 6, 7, 8, 16]]],
        ],
        [
            'name' => 'сельская администрация',
            'shortName' => 'с/а',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с/а'], 'levels' => [5]]],
        ],
        [
            'name' => 'сельский округ',
            'shortName' => 'с/о',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с/о'], 'levels' => [5]]],
        ],
        [
            'name' => 'сельский поселок',
            'shortName' => 'сп.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['сп', 'сп.'], 'levels' => [6]]],
        ],
        [
            'name' => 'сельское муниципальное образование',
            'shortName' => 'с/мо',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с/мо'], 'levels' => [5]]],
        ],
        [
            'name' => 'сельское поселение',
            'shortName' => 'с/п',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с/п', 'с.п.'], 'levels' => [4, 5]]], // изменил
        ],
        [
            'name' => 'сельсовет',
            'shortName' => 'с/с',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с/с'], 'levels' => [5]]],
        ],
        [
            'name' => 'сквер',
            'shortName' => 'скв.', // изменил
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['сквер', 'с-р'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'слобода',
            'shortName' => 'сл.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['сл', 'сл.'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'спуск',
            'shortName' => 'спуск',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['с-к', 'спуск'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'станица',
            'shortName' => 'ст-ца',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ст-ца'], 'levels' => [6]]],
        ],
        [
            'name' => 'станция',
            'shortName' => 'ст.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ст', 'ст.'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'строение',
            'shortName' => 'стр.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['стр', 'стр.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'съезд',
            'shortName' => 'сзд.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['сзд.'], 'levels' => [8]]],
        ],
        // объединил все территории
        [
            'name' => 'территория',
            'shortName' => 'тер.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [
                [
                    'names' => [
                        'тер',
                        'тер.',
                        'тер. ГСК',
                        'тер. ДНО',
                        'тер. ДНП',
                        'тер. ДНТ',
                        'тер. ДПК',
                        'тер. ОНО',
                        'тер. ОНП',
                        'тер. ОНТ',
                        'тер. ОПК',
                        'тер. ПК',
                        'тер. СНО',
                        'тер. СНП',
                        'тер. СНТ',
                        'тер.СОСН',
                        'тер. СПК',
                        'тер. ТСЖ',
                        'тер. ТСН',
                        'тер.ф.х.',
                    ],
                    'levels' => [2, 5, 6, 7, 8, 14, 15, 16],
                ],
            ],
        ],
        [
            'name' => 'тракт',
            'shortName' => 'тракт',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['тракт'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'тупик',
            'shortName' => 'туп.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['туп', 'туп.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'улица',
            'shortName' => 'ул.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ул', 'ул.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'улус',
            'shortName' => 'у.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['у', 'у.'], 'levels' => [2, 6]]],
        ],
        [
            'name' => 'усадьба',
            'shortName' => 'ус.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ус.'], 'levels' => [7]]],
        ],
        [
            'name' => 'участок',
            'shortName' => 'уч-к',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['уч-к'], 'levels' => [8, 16]]],
        ],
        [
            'name' => 'ферма',
            'shortName' => 'ферма',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ферма'], 'levels' => [8, 16]]],
        ],
        [
            'name' => 'фермерское хозяйство',
            'shortName' => 'ф/х',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ф/х'], 'levels' => [7, 8, 15]]],
        ],
        [
            'name' => 'хутор',
            'shortName' => 'х.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['х', 'х.'], 'levels' => [6, 7, 8, 16]]],
        ],
        [
            'name' => 'шоссе',
            'shortName' => 'ш.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_AFTER,
            'variants' => [['names' => ['ш', 'ш.'], 'levels' => [7, 8, 16]]],
        ],
        [
            'name' => 'юрты',
            'shortName' => 'ю.',
            'namePosition' => AddressLevelSpec::NAME_POSITION_BEFORE,
            'variants' => [['names' => ['ю.'], 'levels' => [7]]],
        ],

    ];

    public function resolve(int $addressLevel, $identifier): AddressLevelSpec
    {
        if (!in_array(
            $addressLevel,
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::STREET,
                AddressLevel::CAR_PLACE,
                AddressLevel::STEAD,
            ],
            true
        )) {
            throw new WrongAddressLevelResolvingException(
                sprintf('"%s" cannot resolve level %d', self::class, $addressLevel)
            );
        }


        $s = $this->prepareString($identifier);

        $variants = array_values(
            array_filter(
                self::VARIANTS,
                function ($variant) use ($s, $addressLevel) {
                    // совпадает по полному и краткому названию
                    if ($this->prepareString($variant['shortName']) === $s
                        || $this->prepareString($variant['name']) === $s
                    ) {
                        return true;
                    }

                    // совпадает по вариантам
                    foreach ($variant['variants'] as $group) {
                        $nameMatched = in_array(
                            $s,
                            array_map(
                                function ($name) {
                                    return $this->prepareString($name);
                                },
                                $group['names']
                            ),
                            true
                        );

                        $levelMatched = in_array(
                            $addressLevel,
                            array_map(
                                static function ($fiasLevel) {
                                    return FiasLevel::mapToAddressLevel($fiasLevel);
                                },
                                $group['levels']
                            ),
                            true
                        );

                        if ($nameMatched && $levelMatched) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );

        if (empty($variants)) {
            throw LevelNameSpecNotFoundException::withFiasRelationTypeAndTypeId('addr_obj_types', $identifier);
        }

        $variant = $variants[0];

        return new AddressLevelSpec(
            $addressLevel,
            $variant['name'],
            $variant['shortName'],
            $variant['namePosition']
        );
    }

    private function prepareString(string $shortName): string
    {
        // приводим все к нижнему регистру, даже аббревиатуры
        return mb_strtolower(trim($shortName));
    }
}
