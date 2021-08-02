<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

/**
 * Этот класс НОРМАЛИЗУЕТ LevelName, а НЕ РАСПОЗНАЕТ их. Поэтому требуется чтобы оба поля (name и shortname)
 * были заполнены корректными данными.
 *
 * Поэтому для ("undefined", "undefined") будет возвращен ("undefined", "undefined")
 * Поэтому для ("undefined", "д."), ("undefined", "п.") - могут возвращаться неправильные данные.
 *
 * Нормализует сегменты адреса (ул, аллея, респ, аул, аал, арбар и тд).
 * Правила: 1) сокращения слов оканчиваются точкой, аббревиатуры без точек 2) lowercase для всего кроме аббревиатур.
 */
class AddresLevelSpecNormalizer
{
    // здесь только повторы
    // + те у которых сокращение требует модификации
    // + те у которых полное название требует модификации
    private const REPLACES = [
        // свои
        [
            'name' => 'район',
            'shortName' => 'р-н',
            'variants' => ['р-н', 'район', 'р-н.'],
        ],
        [
            'name' => 'квартира',
            'shortName' => 'кв.',
            'variants' => ['кв'],
        ],
        [
            'name' => 'автономная область',
            'shortName' => 'АО',
            'variants' => ['аобл', 'а.обл.'],
        ],
        [
            'name' => 'автономный округ',
            'shortName' => 'АО',
            'variants' => ['а.окр.'],
        ],
        [
            'name' => 'аллея',
            'shortName' => 'ал.',
            'variants' => ['аллея', 'ал'],
        ],
        [
            'name' => 'берег',
            'shortName' => 'б-г',
            'variants' => ['берег'],
        ],
        [
            'name' => 'внутригородская территория',
            'shortName' => 'вн.тер.г.',
            'variants' => ['вн.тер. г.'],
        ],
        [
            'name' => 'въезд',
            'shortName' => 'въезд',
            'variants' => ['взд.'],
        ],
        [
            'name' => 'гаражно-строительный кооператив',
            'shortName' => 'гск',
            'variants' => ['гск'],
        ],
        [
            'name' => 'город',
            'shortName' => 'г.',
            'variants' => ['г'],
        ],
        [
            'name' => 'дачный поселок',
            'shortName' => 'дп',
            'variants' => ['дп.'],
        ],
        [
            'name' => 'деревня',
            'shortName' => 'дер.',
            'variants' => ['д', 'дер', 'дер.'],
            'addressLevels' => [3, 4],
        ],
        [
            'name' => 'дом',
            'shortName' => 'д.',
            'variants' => ['д'],
            'addressLevels' => [6],
        ],
        [
            'name' => 'дорога',
            'shortName' => 'дор.',
            'variants' => ['дор'],
        ],
        [
            'name' => 'железнодорожная будка',
            'shortName' => 'ж/д б-ка',
            'variants' => ['ж/д_будка'],
        ],
        [
            'name' => 'железнодорожная казарма',
            'shortName' => 'ж/д к-ма',
            'variants' => ['ж/д_казарм'],
        ],
        [
            'name' => 'железнодорожная платформа',
            'shortName' => 'ж/д платф.',
            'variants' => ['ж/д_платф', 'ж/д пл-ма'],
        ],
        [
            'name' => 'железнодорожная станция',
            'shortName' => 'ж/д ст.',
            'variants' => ['ж/д_ст'],
        ],
        [
            'name' => 'железнодорожный пост',
            'shortName' => 'ж/д пост.',
            'variants' => ['ж/д_пост'],
        ],
        [
            'name' => 'железнодорожный разъезд',
            'shortName' => 'ж/д рзд.',
            'variants' => ['ж/д_рзд'],
        ],
        [
            'name' => 'жилой район',
            'shortName' => 'ж/р',
            'variants' => ['жилрайон'],
        ],
        [
            'name' => 'заезд',
            'shortName' => 'заезд',
            'variants' => ['ззд.'],
        ],
        [
            // чтобы переписать Зона (массив)
            'name' => 'зона',
            'shortName' => 'зона',
            'variants' => ['зона'],
        ],
        [
            'name' => 'кольцо',
            'shortName' => 'кольцо',
            'variants' => ['к-цо'],
        ],
        [
            'name' => 'линия',
            'shortName' => 'лн.',
            'variants' => ['линия'],
        ],
        [
            'name' => 'межселенная территория',
            'shortName' => 'межсел. тер.',
            'variants' => ['межсел.тер.'],
        ],
        [
            'name' => 'местечко',
            'shortName' => 'м-ко',
            'variants' => ['м'],
        ],
        [
            'name' => 'месторождение',
            'shortName' => 'месторожд.',
            'variants' => ['мр.'],
        ],
        [
            'name' => 'микрорайон',
            'shortName' => 'мкр.',
            'variants' => ['мкр'],
        ],
        [
            'name' => 'набережная',
            'shortName' => 'наб.',
            'variants' => ['наб'],
        ],
        [
            'name' => 'населенный пункт',
            'shortName' => 'нп',
            'variants' => ['нп.'],
        ],
        [
            'name' => 'область',
            'shortName' => 'обл.',
            'variants' => ['обл'],
        ],
        [
            'name' => 'остров',
            'shortName' => 'остров',
            'variants' => ['ост-в'],
        ],
        [
            'name' => 'переезд',
            'shortName' => 'переезд',
            'variants' => ['пер-д'],
        ],
        [
            'name' => 'переулок',
            'shortName' => 'пер.',
            'variants' => ['пер'],
        ],
        [
            'name' => 'платформа',
            'shortName' => 'платф.',
            'variants' => ['платф'],
        ],
        [
            'name' => 'площадь',
            'shortName' => 'пл.',
            'variants' => ['пл'],
        ],
        // TODO: т.к. "п" используется так же для поселка (пока убрал)
        [
            'name' => 'поселение',
            'shortName' => 'пос.',
            'variants' => ['п'],
            // т.к. "п" используется так же для поселка
            'addressLevels' => [4],
        ],
        [
            'name' => 'поселок',
            'shortName' => 'п.',
            'variants' => ['п'],
            // т.к. "п" используется так же для поселка
            'addressLevels' => [3],
        ],
        [
            'name' => 'поселок городского типа',
            'shortName' => 'пгт',
            'variants' => ['пгт.'],
        ],
        [
            'name' => 'поселок при железнодорожной станции',
            'shortName' => 'п. ж/д ст.',
            'variants' => ['п. ст.', 'п/ст'],
        ],
        [
            'name' => 'починок',
            'shortName' => 'п-к',
            'variants' => ['починок'],
        ],
        [
            'name' => 'проезд',
            'shortName' => 'пр-д',
            'variants' => ['проезд'],
        ],
        // чтобы убрать name = Промзона
        [
            'name' => 'промышленная зона',
            'shortName' => 'промзона',
            'variants' => ['промзона'],
        ],
        [
            'name' => 'просека',
            'shortName' => 'просека',
            'variants' => ['пр-к', 'просек'],
        ],
        [
            'name' => 'проселок',
            'shortName' => 'проселок',
            'variants' => ['пр-лок'],
        ],
        [
            'name' => 'проулок',
            'shortName' => 'проул.',
            'variants' => ['проулок'],
        ],
        [
            'name' => 'планировочный район',
            'shortName' => 'пл. р-н.',
            'variants' => ['пл.р-н'],
        ],
        [
            'name' => 'разъезд',
            'shortName' => 'рзд.',
            'variants' => ['рзд'],
        ],
        [
            'name' => 'республика',
            'shortName' => 'респ.',
            'variants' => ['Респ'],
        ],
        [
            'name' => 'ряд',
            'shortName' => 'ряд',
            'variants' => ['ряды'],
        ],
        [
            'name' => 'садовое товарищество',
            'shortName' => 'снт',
            'variants' => ['с/т'],
        ],
        [
            'name' => 'село',
            'shortName' => 'с.',
            'variants' => ['с'],
        ],
        [
            'name' => 'сельский поселок',
            'shortName' => 'сп',
            'variants' => ['сп.'],
        ],
        [
            'name' => 'сельское поселение',
            'shortName' => 'с/п',
            'variants' => ['с.п.'],
        ],
        [
            'name' => 'сквер',
            'shortName' => 'сквер',
            'variants' => ['с-р'],
        ],
        [
            'name' => 'слобода',
            'shortName' => 'сл.',
            'variants' => ['сл'],
        ],
        [
            'name' => 'спуск',
            'shortName' => 'спуск',
            'variants' => ['с-к'],
        ],
        [
            'name' => 'станция',
            'shortName' => 'ст.',
            'variants' => ['ст'],
        ],
        [
            'name' => 'строение',
            'shortName' => 'стр.',
            'variants' => ['стр'],
        ],
        [
            'name' => 'территория',
            'shortName' => 'тер.',
            'variants' => ['тер'],
        ],
        // пробел добавил
        [
            'name' => 'территория СОСН',
            'shortName' => 'тер. СОСН',
            'variants' => ['тер.СОСН'],
        ],
        [
            'name' => 'территория ФХ',
            'shortName' => 'тер. ф.х.',
            'variants' => ['тер.ф.х.'],
        ],
        [
            'name' => 'тупик',
            'shortName' => 'туп.',
            'variants' => ['туп'],
        ],
        [
            'name' => 'улица',
            'shortName' => 'ул.',
            'variants' => ['ул'],
        ],
        [
            'name' => 'улус',
            'shortName' => 'у.',
            'variants' => ['у'],
        ],
        [
            'name' => 'хутор',
            'shortName' => 'х.',
            'variants' => ['х'],
        ],
        [
            'name' => 'республика',
            'shortName' => 'респ.',
            'variants' => ['Чувашия'],
        ],
    ];

    private const NAME_WEIGHT = 1;
    private const VARIANT_WEIGHT = 0.8;

    public function normalize(AddressLevelSpec $levelName): AddressLevelSpec
    {
        $shortName = $levelName->getShortName();
        $name = $levelName->getName();

        $map = array_map(
            function ($item, $key) use ($shortName, $name): array {
                return [
                    'key' => $key,
                    'equality' => $this->equality($shortName, $item) + $this->equality($name, $item),
                ];
            },
            self::REPLACES,
            array_keys(self::REPLACES)
        );

        $repl = array_values(
            array_filter(
                $map,
                static function ($item) {
                    return $item['equality'] > 0;
                }
            )
        );

        // замен не найдено
        if (empty($repl)) {
            return $levelName;
        }

        // сортируем по equality
        $equality = array_column($repl, 'equality');
        array_multisort($equality, SORT_DESC, $repl);

        $repl = self::REPLACES[$repl[0]['key']];

        return new AddressLevelSpec($repl['name'], $repl['shortName']);
    }

    private function equality(string $s, array $item): float
    {
        $s = $this->prepareString($s);

        if ($this->prepareString($item['shortName']) === $s || $this->prepareString($item['name']) === $s) {
            return self::NAME_WEIGHT;
        }

        if (in_array($s, array_map('mb_strtolower', $item['variants']), true)) {
            return self::VARIANT_WEIGHT;
        }

        return 0;
    }

    private function prepareString(string $s): string
    {
        return mb_strtolower(trim($s));
    }
}
