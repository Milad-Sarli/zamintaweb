<?php

namespace App\Helpers;

class PersianNumberHelper
{
    private static $ones = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];

    private static $teens = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];

    private static $tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];

    private static $hundreds = ['', 'صد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];

    private static $stages = ['', 'هزار', 'میلیون', 'میلیارد', 'تریلیون'];

    public static function toWords($number): string
    {
        if ($number === null || $number === '') {
            return '';
        }

        $number = (string) str_replace(',', '', $number);

        if (!is_numeric($number)) {
            return '';
        }

        $number = (int) $number;

        if ($number === 0) {
            return 'صفر';
        }

        if ($number < 0) {
            return 'منفی ' . self::toWords(abs($number));
        }

        $result = [];
        $stageIndex = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk > 0) {
                $chunkWords = self::convertChunk($chunk);
                $stageWord = self::$stages[$stageIndex];
                $result[] = $chunkWords . ($stageWord ? ' ' . $stageWord : '');
            }
            $number = (int) ($number / 1000);
            $stageIndex++;
        }

        return implode(' و ', array_reverse($result)) . ' تومان';
    }

    private static function convertChunk($number): string
    {
        $res = [];
        $hundreds = (int) ($number / 100);
        $remainder = $number % 100;

        if ($hundreds > 0) {
            $res[] = self::$hundreds[$hundreds];
        }

        if ($remainder > 0) {
            if ($remainder < 10) {
                $res[] = self::$ones[$remainder];
            } elseif ($remainder < 20) {
                $res[] = self::$teens[$remainder - 10];
            } else {
                $tens = (int) ($remainder / 10);
                $ones = $remainder % 10;
                $res[] = self::$tens[$tens] . ($ones > 0 ? ' و ' . self::$ones[$ones] : '');
            }
        }

        return implode(' و ', $res);
    }
}
