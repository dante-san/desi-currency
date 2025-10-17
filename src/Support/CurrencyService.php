<?php

namespace Laxmidhar\DesiCurrency\Support;

use NumberFormatter;
use Exception;

class CurrencyService
{
    /**
     * Format amount in Indian currency format (₹1,23,456.78)
     */
    public static function format(float $amount, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $formatted = number_format($amount, 2);
        $parts = explode('.', $formatted);
        $intPart = $parts[0];
        $decPart = $parts[1] ?? '00';

        // Indian numbering system
        $lastThree = substr($intPart, -3);
        $remaining = substr($intPart, 0, -3);

        if ($remaining != '') {
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
            $intPart = $remaining . ',' . $lastThree;
        } else {
            $intPart = $lastThree;
        }

        $result = $intPart . '.' . $decPart;

        if ($isNegative) {
            $result = '-' . $result;
        }

        return $showSymbol ? '₹' . $result : $result;
    }

    /**
     * Format amount in words (1 Lakh, 2.5 Crore, etc.)
     */
    public static function toWords(float $amount, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $crore = 10000000;
        $lakh = 100000;
        $thousand = 1000;

        $result = '';

        if ($amount >= $crore) {
            $value = round($amount / $crore, 2);
            $result = self::formatDecimal($value) . ' Crore';
        } elseif ($amount >= $lakh) {
            $value = round($amount / $lakh, 2);
            $result = self::formatDecimal($value) . ' Lakh';
        } elseif ($amount >= $thousand) {
            $value = round($amount / $thousand, 2);
            $result = self::formatDecimal($value) . 'K';
        } else {
            $result = self::formatDecimal($amount);
        }

        if ($isNegative) {
            $result = '-' . $result;
        }

        return $showSymbol ? '₹' . $result : $result;
    }

    /**
     * Format with shorthand notation (1L, 2.5Cr, etc.)
     */
    public static function toShorthand(float $amount, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $crore = 10000000;
        $lakh = 100000;
        $thousand = 1000;

        $result = '';

        if ($amount >= $crore) {
            $value = round($amount / $crore, 2);
            $result = self::formatDecimal($value) . 'Cr';
        } elseif ($amount >= $lakh) {
            $value = round($amount / $lakh, 2);
            $result = self::formatDecimal($value) . 'L';
        } elseif ($amount >= $thousand) {
            $value = round($amount / $thousand, 2);
            $result = self::formatDecimal($value) . 'K';
        } else {
            $result = self::formatDecimal($amount);
        }

        if ($isNegative) {
            $result = '-' . $result;
        }

        return $showSymbol ? '₹' . $result : $result;
    }

    /**
     * Convert shorthand/words back to number (1L → 100000)
     */
    public static function parse(string $amount): float
    {
        // Remove currency symbol and spaces
        $amount = str_replace(['₹', 'Rs', 'Rs.', ' '], '', $amount);

        // Handle negative
        $isNegative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');

        $multiplier = 1;

        // Check for Crore
        if (preg_match('/([\d.,]+)\s*(Cr|Crore|crore)/i', $amount, $matches)) {
            $multiplier = 10000000;
            $amount = $matches[1];
        }
        // Check for Lakh
        elseif (preg_match('/([\d.,]+)\s*(L|Lakh|lakh)/i', $amount, $matches)) {
            $multiplier = 100000;
            $amount = $matches[1];
        }
        // Check for Thousand
        elseif (preg_match('/([\d.,]+)\s*(K|Thousand|thousand)/i', $amount, $matches)) {
            $multiplier = 1000;
            $amount = $matches[1];
        }

        // Remove commas and convert to float
        $value = (float) str_replace(',', '', $amount);
        $result = $value * $multiplier;

        return $isNegative ? -$result : $result;
    }

    /**
     * Format in lakhs only
     */
    public static function toLakhs(float $amount, int $decimals = 2, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $value = $amount / 100000;
        $formatted = number_format($value, $decimals);

        $result = $formatted . ' Lakh' . ($value != 1 ? 's' : '');

        if ($isNegative) {
            $result = '-' . $result;
        }

        return $showSymbol ? '₹' . $result : $result;
    }

    /**
     * Format in crores only
     */
    public static function toCrores(float $amount, int $decimals = 2, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $value = $amount / 10000000;
        $formatted = number_format($value, $decimals);

        $result = $formatted . ' Crore' . ($value != 1 ? 's' : '');

        if ($isNegative) {
            $result = '-' . $result;
        }

        return $showSymbol ? '₹' . $result : $result;
    }

    /**
     * Get rupee symbol
     */
    public static function symbol(): string
    {
        return '₹';
    }

    /**
     * Format amount without decimals
     */
    public static function formatWhole(float $amount, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $intPart = number_format($amount, 0);

        // Indian numbering system
        $lastThree = substr($intPart, -3);
        $remaining = substr($intPart, 0, -3);

        if ($remaining != '') {
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
            $intPart = $remaining . ',' . $lastThree;
        } else {
            $intPart = $lastThree;
        }

        if ($isNegative) {
            $intPart = '-' . $intPart;
        }

        return $showSymbol ? '₹' . $intPart : $intPart;
    }

    /**
     * Format for accounting (negative in parentheses)
     */
    public static function formatAccounting(float $amount, bool $showSymbol = true): string
    {
        $isNegative = $amount < 0;
        $formatted = self::format(abs($amount), $showSymbol);

        return $isNegative ? "($formatted)" : $formatted;
    }

    /**
     * Check if amount is in lakhs range
     */
    public static function isLakhsRange(float $amount): bool
    {
        $abs = abs($amount);
        return $abs >= 100000 && $abs < 10000000;
    }

    /**
     * Check if amount is in crores range
     */
    public static function isCroresRange(float $amount): bool
    {
        $abs = abs($amount);
        return $abs >= 10000000;
    }

    /**
     * Format amount with custom suffix
     */
    public static function formatWithSuffix(float $amount, string $suffix = '', bool $showSymbol = true): string
    {
        $formatted = self::format($amount, $showSymbol);
        return $suffix ? $formatted . ' ' . $suffix : $formatted;
    }

    /**
     * Split amount into rupees and paise
     */
    public static function splitRupeesPaise(float $amount): array
    {
        $rupees = floor(abs($amount));
        $paise = round((abs($amount) - $rupees) * 100);

        return [
            'rupees' => $amount < 0 ? -$rupees : $rupees,
            'paise' => (int) $paise
        ];
    }

    /**
     * Format amount in words (full Indian numbering)
     */
    public static function toIndianWords(float $amount): string
    {
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $split = self::splitRupeesPaise($amount);
        $rupees = $split['rupees'];
        $paise = $split['paise'];

        $words = self::numberToWords(abs($rupees));
        $result = $words . ' Rupee' . (abs($rupees) != 1 ? 's' : '');

        if ($paise > 0) {
            $paiseWords = self::numberToWords($paise);
            $result .= ' and ' . $paiseWords . ' Paise';
        }

        return $isNegative ? 'Negative ' . $result : $result;
    }

    /**
     * Helper: Convert number to words
     */
    private static function numberToWords(int $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

        if ($number == 0) return 'Zero';

        $crore = floor($number / 10000000);
        $number %= 10000000;

        $lakh = floor($number / 100000);
        $number %= 100000;

        $thousand = floor($number / 1000);
        $number %= 1000;

        $hundred = floor($number / 100);
        $number %= 100;

        $words = '';

        if ($crore) {
            $words .= self::convertTwoDigit($crore) . ' Crore ';
        }

        if ($lakh) {
            $words .= self::convertTwoDigit($lakh) . ' Lakh ';
        }

        if ($thousand) {
            $words .= self::convertTwoDigit($thousand) . ' Thousand ';
        }

        if ($hundred) {
            $words .= $ones[$hundred] . ' Hundred ';
        }

        if ($number > 0) {
            if ($number < 10) {
                $words .= $ones[$number];
            } elseif ($number < 20) {
                $words .= $teens[$number - 10];
            } else {
                $words .= $tens[floor($number / 10)] . ' ' . $ones[$number % 10];
            }
        }

        return trim($words);
    }

    /**
     * Helper: Convert two digit number to words
     */
    private static function convertTwoDigit(int $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

        if ($number < 10) {
            return $ones[$number];
        } elseif ($number < 20) {
            return $teens[$number - 10];
        } else {
            return trim($tens[floor($number / 10)] . ' ' . $ones[$number % 10]);
        }
    }

    /**
     * Helper: Format decimal values
     */
    private static function formatDecimal(float $value): string
    {
        if (floor($value) == $value) {
            return (string) (int) $value;
        }
        return rtrim(rtrim(number_format($value, 2), '0'), '.');
    }
}
