<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

class AddressSynonymizer
{
    public function getSynonyms(string $fiasId): array
    {
        switch ($fiasId) {
            case '6f2cbfd8-692a-4ee4-9b16-067210bde3fc':
                return ['Башкирия'];
            case '0c089b04-099e-4e0e-955a-6bf1ce525f1a':
                return ['Татария'];
            case '52618b9c-bcbb-47e7-8957-95c63f0b17cc':
                return ['Удмуртия'];
            case '878fc621-3708-46c7-a97f-5a13a4176b3e':
                return ['Чувашия'];
        }

        return [];
    }
}
