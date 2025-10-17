<?php

namespace Laxmidhar\DesiCurrency\Support;

use NumberFormatter;
use Exception;

class CurrencyService
{
    /**
     * Format number to Indian currency with symbol (₹)
     *
     * @example
     * CurrencyService::formatINR(1520000); // ₹15,20,000
     */
    public static function formatINR($amount, $withSymbol = true): string
    {
        if (!is_numeric($amount)) return '0';

        $formatted = number_format($amount, 2, '.', ',');
        $formatted = preg_replace('/(\d)(?=(\d\d)+\d(\.\d+)?$)/', '$1,', $amount);
        return $withSymbol ? '₹' . $formatted : $formatted;
    }

    /**
     * Convert number into short form (e.g., 1.2K, 3.5L, 2.1Cr)
     *
     * @example
     * CurrencyService::shorten(1500); // 1.5K
     * CurrencyService::shorten(125000); // 1.25L
     * CurrencyService::shorten(12000000); // 1.2Cr
     */
    public static function shorten($amount): string
    {
        if (!is_numeric($amount)) return '0';

        if ($amount >= 10000000) {
            return round($amount / 10000000, 2) . 'Cr';
        } elseif ($amount >= 100000) {
            return round($amount / 100000, 2) . 'L';
        } elseif ($amount >= 1000) {
            return round($amount / 1000, 2) . 'K';
        }

        return (string) $amount;
    }

    /**
     * Convert number to words (Indian format)
     *
     * @example
     * CurrencyService::inWords(1520000);
     * // One Lakh Fifty-Two Thousand Only
     */
    public static function inWords($amount): string
    {
        $amount = floor($amount);
        if ($amount == 0) return "Zero Rupees Only";

        $formatter = new NumberFormatter("en_IN", NumberFormatter::SPELLOUT);
        $words = ucfirst($formatter->format($amount));
        return $words . " Rupees Only";
    }

    /**
     * Parse human-friendly currency (like '1.5L' or '2Cr') back to numeric value
     *
     * @example
     * CurrencyService::parseShort('1.5L'); // 150000
     * CurrencyService::parseShort('2Cr');  // 20000000
     */
    public static function parseShort(string $value): float
    {
        $value = strtoupper(trim($value));

        if (str_ends_with($value, 'CR')) {
            return (float) $value * 10000000;
        } elseif (str_ends_with($value, 'L')) {
            return (float) $value * 100000;
        } elseif (str_ends_with($value, 'K')) {
            return (float) $value * 1000;
        }

        return (float) $value;
    }

    /**
     * Add currency symbol if missing
     *
     * @example
     * CurrencyService::addSymbol(1200); // ₹1200
     * CurrencyService::addSymbol('₹1200'); // ₹1200
     */
    public static function addSymbol($value): string
    {
        $value = trim((string) $value);
        return str_starts_with($value, '₹') ? $value : '₹' . $value;
    }

    /**
     * Remove currency symbol and commas, return pure number
     *
     * @example
     * CurrencyService::strip('₹1,20,000.50'); // 120000.50
     */
    public static function strip($value): float
    {
        $value = preg_replace('/[^0-9.]/', '', (string) $value);
        return (float) $value;
    }

    /**
     * Convert INR to another currency (basic static converter)
     *
     * @example
     * CurrencyService::convert(100, 'USD', 83); // 1.2
     */
    public static function convert($amount, $toCurrency = 'USD', $exchangeRate = null): float
    {
        if (!is_numeric($amount)) return 0;

        // You can hook this up with a live API later
        $defaultRates = [
            'USD' => 83.0,
            'EUR' => 89.0,
            'GBP' => 102.0,
        ];

        $rate = $exchangeRate ?: ($defaultRates[$toCurrency] ?? 83.0);
        return round($amount / $rate, 2);
    }

    /**
     * Determine Indian digit grouping with commas
     *
     * @example
     * CurrencyService::indianFormat(1234567); // 12,34,567
     */
    public static function indianFormat($amount): string
    {
        $amount = number_format($amount, 2, '.', ',');
        $parts = explode('.', $amount);
        $intPart = $parts[0];
        $lastThree = substr($intPart, -3);
        $rest = substr($intPart, 0, -3);
        if ($rest != '')
            $lastThree = ',' . $lastThree;
        $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
        return $rest . $lastThree . (isset($parts[1]) ? '.' . $parts[1] : '');
    }
}
