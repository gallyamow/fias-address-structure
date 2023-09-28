<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure;

class ActualityComparator
{
    public function compare(string $startDate1, string $endDate1, string $startDate2, string $endDate2): int
    {
        if ($startDate1 === $startDate2 && $endDate1 === $endDate2) {
            return 0;
        }

        // сравниваем по дате окончания
        if ($endDate1 > $endDate2) {
            return 1;
        }

        if ($endDate2 > $endDate1) {
            return -1;
        }

        // если даты окончания равны, тогда по дате начала
        if ($startDate1 > $startDate2) {
            return 1;
        }

        if ($startDate2 > $startDate1) {
            return -1;
        }
    }
}
