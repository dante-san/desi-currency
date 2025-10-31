# Desi Currency - Indian Currency Helper for Laravel

<p align="center">
  <img src="https://img.shields.io/packagist/v/laxmidhar/desi-currency?style=flat-square" alt="Latest Version">
  <img src="https://img.shields.io/packagist/dt/laxmidhar/desi-currency?style=flat-square" alt="Total Downloads">
  <img src="https://img.shields.io/packagist/l/laxmidhar/desi-currency?style=flat-square" alt="License">
  <img src="https://img.shields.io/badge/Laravel-9.x%20%7C%2010.x%20%7C%2011.x%20%7C%2012.x-FF2D20?style=flat-square&logo=laravel" alt="Laravel Version">
</p>

A comprehensive Laravel package for handling Indian currency formatting, conversions, and utilities. Format amounts in Lakhs, Crores, convert to words, parse shorthand notations (1L, 2.5Cr), and much more - all following Indian numbering standards.

---

## ✨ Features

- 🇮🇳 **Indian Numbering System** - Proper comma placement (₹1,23,456.78)
- 💰 **Lakh & Crore Formatting** - Convert to Lakhs, Crores with ease
- 🔤 **Words Conversion** - Amount to Indian words (One Lakh Twenty Three Thousand)
- 📝 **Shorthand Support** - Parse and format 1L, 2.5Cr, 500K notations
- 🔄 **Two-way Conversion** - Format to shorthand and parse back to numbers
- ✅ **Zero Configuration** - Works out of the box
- 🎯 **Production Ready** - Fully tested and optimized
- 📦 **Auto-Discovery** - Automatically registers with Laravel

---

## 📋 Requirements

- PHP 8.0 or higher
- Laravel 9.x, 10.x, 11.x, or 12.x

---

## 🚀 Installation

Install via Composer:

```bash
composer require laxmidhar/desi-currency
```

The service provider will be automatically registered via Laravel's package auto-discovery.

---

## 📖 Usage

### Facade Usage (Recommended)

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

// Format in Indian style
Currency::format(123456.78);
// Output: "₹1,23,456.78"

// Convert to words
Currency::toWords(150000);
// Output: "₹1.5 Lakh"

// Shorthand notation
Currency::toShorthand(2500000);
// Output: "₹25L"

// Parse back to number
Currency::parse('1.5L');
// Output: 150000
```

### Direct Class Usage

```php
use Laxmidhar\DesiCurrency\Support\CurrencyService;

CurrencyService::format(123456.78);
CurrencyService::toWords(150000);
CurrencyService::toShorthand(2500000);
```

### Dependency Injection

```php
use Laxmidhar\DesiCurrency\Support\CurrencyService;

class InvoiceController extends Controller
{
    public function __construct(protected CurrencyService $currency) {}

    public function show($id)
    {
        $invoice = Invoice::find($id);
        $formatted = $this->currency->format($invoice->total);
        $inWords = $this->currency->toIndianWords($invoice->total);

        return view('invoice.show', compact('invoice', 'formatted', 'inWords'));
    }
}
```

---

## 🧩 Available Methods

### 💰 Core Formatting Methods

#### `format(float $amount, bool $showSymbol = true): string`

Format amount using Indian numbering system (₹1,23,456.78).

**Parameters:**

- `$amount` - Amount to format
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::format(1234.56);
// Output: "₹1,234.56"

Currency::format(123456.78);
// Output: "₹1,23,456.78"

Currency::format(12345678.90);
// Output: "₹1,23,45,678.90"

Currency::format(1234.56, false);
// Output: "1,234.56"

Currency::format(-5000);
// Output: "-₹5,000.00"
```

**Key Features:**

- Follows Indian comma placement (last 3 digits, then groups of 2)
- Handles negative amounts
- Always shows 2 decimal places
- Optional currency symbol

---

#### `formatWhole(float $amount, bool $showSymbol = true): string`

Format amount without decimal places.

**Parameters:**

- `$amount` - Amount to format
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::formatWhole(123456.78);
// Output: "₹1,23,457"

Currency::formatWhole(1234.50);
// Output: "₹1,235"

Currency::formatWhole(1000000);
// Output: "₹10,00,000"

Currency::formatWhole(1234.56, false);
// Output: "1,235"
```

**Use Cases:**

- Display whole rupees only
- Invoice totals
- Round number displays
- Reports and summaries

---

#### `formatAccounting(float $amount, bool $showSymbol = true): string`

Format for accounting purposes (negative values in parentheses).

**Parameters:**

- `$amount` - Amount to format
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::formatAccounting(1000);
// Output: "₹1,000.00"

Currency::formatAccounting(-1000);
// Output: "(₹1,000.00)"

Currency::formatAccounting(-25000.50);
// Output: "(₹25,000.50)"
```

**Use Cases:**

- Financial statements
- Balance sheets
- Profit/Loss reports
- Accounting software

---

### 🔤 Words & Shorthand Conversion

#### `toWords(float $amount, bool $showSymbol = true): string`

Convert amount to readable words format (Lakh, Crore, K).

**Parameters:**

- `$amount` - Amount to convert
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::toWords(500);
// Output: "₹500"

Currency::toWords(5000);
// Output: "₹5K"

Currency::toWords(50000);
// Output: "₹50K"

Currency::toWords(150000);
// Output: "₹1.5 Lakh"

Currency::toWords(2500000);
// Output: "₹25 Lakh"

Currency::toWords(15000000);
// Output: "₹1.5 Crore"

Currency::toWords(250000000);
// Output: "₹25 Crore"

Currency::toWords(150000, false);
// Output: "1.5 Lakh"
```

**Conversion Logic:**

- ≥ 1 Crore (10,000,000): Shows in Crores
- ≥ 1 Lakh (100,000): Shows in Lakhs
- ≥ 1 Thousand (1,000): Shows with K suffix
- < 1 Thousand: Shows as is

---

#### `toShorthand(float $amount, bool $showSymbol = true): string`

Convert to compact shorthand notation (1L, 2.5Cr, 500K).

**Parameters:**

- `$amount` - Amount to convert
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::toShorthand(500);
// Output: "₹500"

Currency::toShorthand(5000);
// Output: "₹5K"

Currency::toShorthand(150000);
// Output: "₹1.5L"

Currency::toShorthand(2500000);
// Output: "₹25L"

Currency::toShorthand(15000000);
// Output: "₹1.5Cr"

Currency::toShorthand(250000000);
// Output: "₹25Cr"

Currency::toShorthand(-150000);
// Output: "-₹1.5L"
```

**Use Cases:**

- Dashboard widgets
- Mobile app displays
- Quick summaries
- Data tables
- Charts and graphs

---

#### `toLakhs(float $amount, int $decimals = 2, bool $showSymbol = true): string`

Always format in Lakhs, regardless of amount.

**Parameters:**

- `$amount` - Amount to convert
- `$decimals` - Number of decimal places (default: 2)
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::toLakhs(50000);
// Output: "₹0.50 Lakhs"

Currency::toLakhs(150000);
// Output: "₹1.50 Lakhs"

Currency::toLakhs(2500000);
// Output: "₹25.00 Lakhs"

Currency::toLakhs(15000000);
// Output: "₹150.00 Lakhs"

Currency::toLakhs(150000, 1);
// Output: "₹1.5 Lakhs"

Currency::toLakhs(100000);
// Output: "₹1.00 Lakh"

Currency::toLakhs(150000, 2, false);
// Output: "1.50 Lakhs"
```

**Use Cases:**

- Real estate pricing
- Salary negotiations
- Project budgets
- Property valuations

---

#### `toCrores(float $amount, int $decimals = 2, bool $showSymbol = true): string`

Always format in Crores, regardless of amount.

**Parameters:**

- `$amount` - Amount to convert
- `$decimals` - Number of decimal places (default: 2)
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::toCrores(5000000);
// Output: "₹0.50 Crores"

Currency::toCrores(15000000);
// Output: "₹1.50 Crores"

Currency::toCrores(250000000);
// Output: "₹25.00 Crores"

Currency::toCrores(15000000, 1);
// Output: "₹1.5 Crores"

Currency::toCrores(10000000);
// Output: "₹1.00 Crore"

Currency::toCrores(15000000, 2, false);
// Output: "1.50 Crores"
```

**Use Cases:**

- Company valuations
- Large transactions
- Annual reports
- Government budgets
- Infrastructure projects

---

#### `toIndianWords(float $amount): string`

Convert amount to complete Indian words format.

**Parameters:**

- `$amount` - Amount to convert

**Examples:**

```php
Currency::toIndianWords(123);
// Output: "One Hundred Twenty Three Rupees"

Currency::toIndianWords(1234.50);
// Output: "One Thousand Two Hundred Thirty Four Rupees and Fifty Paise"

Currency::toIndianWords(123456);
// Output: "One Lakh Twenty Three Thousand Four Hundred Fifty Six Rupees"

Currency::toIndianWords(12345678);
// Output: "One Crore Twenty Three Lakh Forty Five Thousand Six Hundred Seventy Eight Rupees"

Currency::toIndianWords(100.25);
// Output: "One Hundred Rupees and Twenty Five Paise"

Currency::toIndianWords(-500);
// Output: "Negative Five Hundred Rupees"
```

**Use Cases:**

- Cheque printing
- Invoice generation
- Legal documents
- Payment receipts
- Bank transactions

---

### 🔄 Parsing & Conversion

#### `parse(string $amount): float`

Parse Indian currency notation back to numeric value.

**Parameters:**

- `$amount` - String amount to parse

**Examples:**

```php
Currency::parse('1L');
// Output: 100000

Currency::parse('1.5L');
// Output: 150000

Currency::parse('2.5Cr');
// Output: 25000000

Currency::parse('500K');
// Output: 500000

Currency::parse('1 Lakh');
// Output: 100000

Currency::parse('2.5 Crore');
// Output: 25000000

Currency::parse('₹1.5L');
// Output: 150000

Currency::parse('Rs 2.5Cr');
// Output: 25000000

Currency::parse('-1.5L');
// Output: -150000

Currency::parse('1,50,000');
// Output: 150000
```

**Supported Formats:**

- Shorthand: 1L, 2.5Cr, 500K
- Words: 1 Lakh, 2.5 Crore
- With symbols: ₹1L, Rs 2Cr
- With commas: 1,50,000
- Negative values: -1.5L

**Use Cases:**

- User input parsing
- Import data processing
- API integrations
- Form submissions

---

### 🔧 Utility Methods

#### `symbol(): string`

Get the rupee currency symbol.

**Examples:**

```php
Currency::symbol();
// Output: "₹"

// Usage in views
echo Currency::symbol() . '1,000';
// Output: "₹1,000"
```

---

#### `splitRupeesPaise(float $amount): array`

Split amount into rupees and paise components.

**Parameters:**

- `$amount` - Amount to split

**Examples:**

```php
Currency::splitRupeesPaise(1234.56);
// Output: ['rupees' => 1234, 'paise' => 56]

Currency::splitRupeesPaise(100.25);
// Output: ['rupees' => 100, 'paise' => 25]

Currency::splitRupeesPaise(500);
// Output: ['rupees' => 500, 'paise' => 0]

Currency::splitRupeesPaise(-1234.56);
// Output: ['rupees' => -1234, 'paise' => 56]

// Usage example
$split = Currency::splitRupeesPaise(1234.56);
echo "Rupees: {$split['rupees']}, Paise: {$split['paise']}";
// Output: "Rupees: 1234, Paise: 56"
```

**Use Cases:**

- Cheque printing
- Detailed invoices
- Payment breakdowns
- Accounting entries

---

#### `formatWithSuffix(float $amount, string $suffix = '', bool $showSymbol = true): string`

Format amount with custom suffix.

**Parameters:**

- `$amount` - Amount to format
- `$suffix` - Custom suffix text
- `$showSymbol` - Whether to include ₹ symbol (default: true)

**Examples:**

```php
Currency::formatWithSuffix(1000, 'per month');
// Output: "₹1,000.00 per month"

Currency::formatWithSuffix(50000, 'annual');
// Output: "₹50,000.00 annual"

Currency::formatWithSuffix(25000, 'onwards');
// Output: "₹25,000.00 onwards"

Currency::formatWithSuffix(100000, 'only');
// Output: "₹1,00,000.00 only"

Currency::formatWithSuffix(1000);
// Output: "₹1,000.00"
```

**Use Cases:**

- Subscription pricing
- Product pricing
- EMI displays
- Offer pricing

---

#### `isLakhsRange(float $amount): bool`

Check if amount is in the Lakhs range (1L to 1Cr).

**Parameters:**

- `$amount` - Amount to check

**Examples:**

```php
Currency::isLakhsRange(50000);
// Output: false (less than 1 Lakh)

Currency::isLakhsRange(150000);
// Output: true (1.5 Lakhs)

Currency::isLakhsRange(2500000);
// Output: true (25 Lakhs)

Currency::isLakhsRange(15000000);
// Output: false (1.5 Crores)

// Usage example
$amount = 250000;
if (Currency::isLakhsRange($amount)) {
    echo Currency::toLakhs($amount);
} else {
    echo Currency::format($amount);
}
```

**Use Cases:**

- Conditional formatting
- Range-based displays
- Report categorization

---

#### `isCroresRange(float $amount): bool`

Check if amount is in the Crores range (≥1Cr).

**Parameters:**

- `$amount` - Amount to check

**Examples:**

```php
Currency::isCroresRange(5000000);
// Output: false (50 Lakhs)

Currency::isCroresRange(10000000);
// Output: true (1 Crore)

Currency::isCroresRange(250000000);
// Output: true (25 Crores)

// Usage example
$amount = 15000000;
if (Currency::isCroresRange($amount)) {
    echo Currency::toCrores($amount);
} else {
    echo Currency::toLakhs($amount);
}
```

**Use Cases:**

- Large transaction handling
- Report filtering
- Display logic

---

## 💡 Practical Examples

### E-commerce Product Display

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

// Product price formatting
$product = Product::find(1);
$price = $product->price; // 149900

// For display
echo Currency::toShorthand($price);
// Output: "₹1.5L"

// For detailed view
echo Currency::format($price);
// Output: "₹1,49,900.00"

// For cards/widgets
echo Currency::toWords($price);
// Output: "₹1.5 Lakh"
```

---

### Invoice Generation

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

$invoice = Invoice::find(1);
$total = $invoice->total; // 125750.50

// Invoice total
echo Currency::format($total);
// Output: "₹1,25,750.50"

// Amount in words
echo Currency::toIndianWords($total);
// Output: "One Lakh Twenty Five Thousand Seven Hundred Fifty Rupees and Fifty Paise"

// Split for detailed view
$split = Currency::splitRupeesPaise($total);
echo "Rupees: {$split['rupees']}, Paise: {$split['paise']}";
// Output: "Rupees: 125750, Paise: 50"
```

---

### Dashboard Statistics

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

// Annual revenue
$revenue = 125000000; // 12.5 Crores

echo Currency::toShorthand($revenue);
// Output: "₹12.5Cr"

// Monthly revenue
$monthly = $revenue / 12;

if (Currency::isCroresRange($monthly)) {
    echo Currency::toCrores($monthly);
} else {
    echo Currency::toLakhs($monthly);
}
// Output: "₹1.04 Crores"
```

---

### Salary Package Display

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

$salary = 1800000; // 18 Lakhs per annum

// Annual
echo Currency::toLakhs($salary) . ' per annum';
// Output: "₹18.00 Lakhs per annum"

// Monthly
$monthly = $salary / 12;
echo Currency::format($monthly) . ' per month';
// Output: "₹1,50,000.00 per month"
```

---

### User Input Processing

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

// User enters "2.5L" in a form
$userInput = request('budget'); // "2.5L"

// Parse to numeric value
$numericValue = Currency::parse($userInput);
// Output: 250000

// Store in database
$property->budget = $numericValue;
$property->save();

// Display back to user
echo Currency::toWords($property->budget);
// Output: "₹2.5 Lakh"
```

---

### Financial Reports

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

$profit = 5500000;
$loss = -1200000;

// Profit
echo Currency::formatAccounting($profit);
// Output: "₹55,00,000.00"

// Loss
echo Currency::formatAccounting($loss);
// Output: "(₹12,00,000.00)"

// Summary
echo Currency::toShorthand($profit);
// Output: "₹55L"
```

---

### Real Estate Listings

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

$properties = Property::all();

foreach ($properties as $property) {
    $price = $property->price;

    // Display appropriate format based on range
    if (Currency::isCroresRange($price)) {
        echo $property->title . ': ' . Currency::toCrores($price);
    } else if (Currency::isLakhsRange($price)) {
        echo $property->title . ': ' . Currency::toLakhs($price);
    } else {
        echo $property->title . ': ' . Currency::format($price);
    }
}

// Output examples:
// "Luxury Villa: ₹2.50 Crores"
// "2BHK Apartment: ₹45.00 Lakhs"
// "Plot: ₹25,00,000.00"
```

---

### Blade Templates

```blade
{{-- Simple formatting --}}
<h3>Price: {{ Currency::format($product->price) }}</h3>

{{-- Shorthand for cards --}}
<div class="price-tag">
    {{ Currency::toShorthand($product->price) }}
</div>

{{-- Invoice display --}}
<div class="invoice-total">
    <strong>Total:</strong> {{ Currency::format($invoice->total) }}<br>
    <small>{{ Currency::toIndianWords($invoice->total) }}</small>
</div>

{{-- Conditional display --}}
@if(Currency::isCroresRange($amount))
    <span class="badge">{{ Currency::toCrores($amount) }}</span>
@else
    <span class="badge">{{ Currency::toLakhs($amount) }}</span>
@endif
```

---

## 🎨 Blade Directives

Desi Currency provides convenient Blade directives for use in your Laravel views, making currency formatting clean and readable.

### Quick Reference Table

| Directive                      | Purpose                     | Input Example | Output Example                      |
| ------------------------------ | --------------------------- | ------------- | ----------------------------------- |
| `@currency($amount)`           | Standard format with symbol | `123456.78`   | `₹1,23,456.78`                      |
| `@currencyWhole($amount)`      | Format without decimals     | `123456.78`   | `₹1,23,457`                         |
| `@currencyPlain($amount)`      | Format without symbol       | `123456.78`   | `1,23,456.78`                       |
| `@currencyAccounting($amount)` | Accounting format           | `-5000`       | `(₹5,000.00)`                       |
| `@inLakhs($amount)`            | Always show in Lakhs        | `1500000`     | `₹15.00 Lakhs`                      |
| `@inCrores($amount)`           | Always show in Crores       | `15000000`    | `₹1.50 Crores`                      |
| `@currencyShort($amount)`      | Compact notation            | `1500000`     | `₹15L`                              |
| `@currencyWords($amount)`      | Readable format             | `1500000`     | `₹15 Lakh`                          |
| `@currencySpell($amount)`      | Full words                  | `123456`      | `One Lakh Twenty Three Thousand...` |
| `@rupeeSymbol`                 | Rupee symbol only           | -             | `₹`                                 |
| `@inLakhRange($amount)`        | Check Lakh range            | `500000`      | `true/false`                        |
| `@inCroreRange($amount)`       | Check Crore range           | `15000000`    | `true/false`                        |

---

### Available Directives

#### Standard Formatting

**@currency($amount)** - Format with Indian numbering and ₹ symbol

```blade
<h2>Total: @currency($invoice->total)</h2>
<!-- Output: Total: ₹1,23,456.78 -->
```

**@currencyWhole($amount)** - Format without decimals

```blade
<p>Price: @currencyWhole($product->price)</p>
<!-- Output: Price: ₹1,23,457 -->
```

**@currencyPlain($amount)** - Format without currency symbol

```blade
<td>@currencyPlain($amount)</td>
<!-- Output: 1,23,456.78 -->
```

**@currencyAccounting($amount)** - Accounting format (negatives in parentheses)

```blade
<span>Balance: @currencyAccounting($balance)</span>
<!-- Positive: Balance: ₹5,000.00 -->
<!-- Negative: Balance: (₹5,000.00) -->
```

---

#### Indian Units (Lakh & Crore)

**@inLakhs($amount)** - Always display in Lakhs

```blade
<div class="salary">@inLakhs($package)</div>
<!-- Output: ₹18.00 Lakhs -->
```

**@inCrores($amount)** - Always display in Crores

```blade
<h3>Revenue: @inCrores($revenue)</h3>
<!-- Output: Revenue: ₹2.50 Crores -->
```

---

#### Shorthand & Words

**@currencyShort($amount)** - Compact notation (L, Cr, K)

```blade
<span class="badge">@currencyShort($value)</span>
<!-- Output: ₹25L or ₹2.5Cr -->
```

**@currencyWords($amount)** - Readable format (Lakh, Crore)

```blade
<p>Budget: @currencyWords($budget)</p>
<!-- Output: Budget: ₹2.5 Crore -->
```

**@currencySpell($amount)** - Complete Indian words

```blade
<p>Amount in words: @currencySpell($total)</p>
<!-- Output: Amount in words: One Lakh Twenty Three Thousand Four Hundred Fifty Six Rupees -->
```

---

#### Utilities

**@rupeeSymbol** - Just the rupee symbol

```blade
<span>Price: @rupeeSymbol @currencyPlain($price)</span>
<!-- Output: Price: ₹ 1,23,456.78 -->
```

---

### Conditional Directives

Use these to conditionally render based on amount range:

**@inLakhRange($amount)** - Check if amount is in Lakh range (1L - 1Cr)

```blade
@inLakhRange($property->price)
    <span class="badge-lakh">@inLakhs($property->price)</span>
@else
    <span class="badge">@currency($property->price)</span>
@endinLakhRange
```

**@inCroreRange($amount)** - Check if amount is in Crore range (≥1Cr)

```blade
@inCroreRange($deal->value)
    <h2 class="premium">@inCrores($deal->value)</h2>
@endinCroreRange
```

---

### Practical Blade Examples

#### Product Card

```blade
<div class="product-card">
    <h3>{{ $product->name }}</h3>

    @inCroreRange($product->price)
        <span class="price premium">@inCrores($product->price)</span>
    @elseInLakhRange($product->price)
        <span class="price standard">@inLakhs($product->price)</span>
    @else
        <span class="price">@currency($product->price)</span>
    @endinCroreRange

    <small>@currencyShort($product->price)</small>
</div>
```

#### Invoice Template

```blade
<div class="invoice">
    <table>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">@currency($item->amount)</td>
            </tr>
        @endforeach

        <tr class="total">
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>@currency($invoice->total)</strong></td>
        </tr>
    </table>

    <div class="amount-words">
        <em>@currencySpell($invoice->total)</em>
    </div>
</div>
```

#### Dashboard Widget

```blade
<div class="stat-card">
    <h4>Monthly Revenue</h4>
    <h2>@currencyShort($revenue)</h2>
    <p class="text-muted">@currencyWords($revenue)</p>
</div>
```

#### Financial Report

```blade
<table class="financial-report">
    <tr>
        <td>Revenue</td>
        <td>@currencyAccounting($revenue)</td>
    </tr>
    <tr>
        <td>Expenses</td>
        <td>@currencyAccounting(-$expenses)</td>
    </tr>
    <tr>
        <td>Profit/Loss</td>
        <td><strong>@currencyAccounting($profit)</strong></td>
    </tr>
</table>
```

#### Salary Package Display

```blade
<div class="salary-breakdown">
    <div class="annual">
        <label>Annual Package:</label>
        <h3>@inLakhs($salary->annual)</h3>
    </div>

    <div class="monthly">
        <label>Monthly:</label>
        <p>@currency($salary->monthly)</p>
    </div>

    <div class="compact">
        <small>(@currencyShort($salary->annual) per year)</small>
    </div>
</div>
```

---

## 🎯 Best Practices

### 1. **Consistent Display**

Use the same format throughout your application for consistency:

```php
// Good: Consistent format
echo Currency::toShorthand($amount); // Throughout app

// Avoid: Mixing formats randomly
echo Currency::format($amount);
echo Currency::toWords($amount);
```

---

### 2. **Store as Numeric, Display as Formatted**

Always store amounts as numeric values in database:

```php
// Storage (numeric)
$product->price = 150000;

// Display (formatted)
echo Currency::toWords($product->price);
```

---

### 3. **Handle User Input Properly**

Parse user input before storing:

```php
$input = request('amount'); // "1.5L"
$numeric = Currency::parse($input);
$model->amount = $numeric; // Store: 150000
```

---

### 4. **Use Appropriate Format for Context**

Choose format based on where it's displayed:

```php
// Dashboards: Shorthand
echo Currency::toShorthand($revenue);

// Reports: Full format
echo Currency::format($revenue);

// Invoices: Words
echo Currency::toIndianWords($total);

// Cards: Words format
echo Currency::toWords($amount);
```

---

### 5. **Handle Negative Amounts**

Always check for negative values:

```php
$balance = -5000;

// Use accounting format for negatives
echo Currency::formatAccounting($balance);
// Output: "(₹5,000.00)"
```

---

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

### Example Test Cases

```php
use Laxmidhar\DesiCurrency\Facades\Currency;

test('formats amount in Indian style', function () {
    expect(Currency::format(123456.78))
        ->toBe('₹1,23,456.78');
});

test('converts to lakhs notation', function () {
    expect(Currency::toWords(150000))
        ->toBe('₹1.5 Lakh');
});

test('parses shorthand notation', function () {
    expect(Currency::parse('1.5L'))
        ->toBe(150000.0);
});

test('converts to Indian words', function () {
    expect(Currency::toIndianWords(1234.50))
        ->toBe('One Thousand Two Hundred Thirty Four Rupees and Fifty Paise');
});
```

---

## 📊 Comparison with Other Solutions

| Feature             | Desi Currency | Manual Formatting | Other Packages  |
| ------------------- | ------------- | ----------------- | --------------- |
| Indian Numbering    | ✅            | ❌                | ⚠️ Limited      |
| Lakh/Crore Support  | ✅            | ❌                | ❌              |
| Shorthand Parsing   | ✅            | ❌                | ❌              |
| Words Conversion    | ✅            | ❌                | ⚠️ English only |
| Zero Config         | ✅            | N/A               | ❌              |
| Laravel Integration | ✅            | N/A               | ⚠️ Limited      |

---

## 🔒 Security

If you discover any security-related issues, please email papu.team7@gmail.com instead of using the issue tracker.

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure:

- All tests pass
- Code follows PSR-12 standards
- New features include tests
- Documentation is updated

---

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

---

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

---

## 👨‍💻 Author

**Laxmidhar Maharana**

Senior Laravel Developer • Open Source Contributor

- 🔗 [GitHub](https://github.com/dante-san)
- 💼 [LinkedIn](https://www.linkedin.com/in/laxmidharmaharana/)
- ✉️ [Email](mailto:papu.team7@gmail.com)

---

## ⭐ Show Your Support

If this package helped you, please give it a ⭐️ on [GitHub](https://github.com/dante-san/desi-currency)!

---

## 🙏 Acknowledgments

Special thanks to:

- The Laravel community for inspiration
- Indian developers who understand the pain of currency formatting

---

## 📚 Related Packages

- [Laravel Helpers](https://github.com/dante-san/laravel-helpers) - 85+ Helper Functions
- [Laravel Money](https://github.com/cknow/laravel-money) - Money handling
- [Laravel Cashier](https://laravel.com/docs/billing) - Subscription billing

---

## 🗺️ Roadmap

### ✅ Completed

- [x] Blade directives for formatting

### 🔜 Upcoming

- [ ] Add support for other Indian regional formats
- [ ] Currency conversion support
- [ ] GST calculations
- [ ] Tax computation helpers
- [ ] Multi-currency support

---

## ❓ FAQ

**Q: Can I use this for non-Indian currencies?**  
A: This package is specifically designed for Indian Rupee and Indian numbering system. For other currencies, consider using Laravel Money or similar packages.

**Q: Does it handle GST calculations?**  
A: Not yet, but it's on the roadmap. Current version focuses on formatting and conversion.

**Q: Can I customize the decimal places?**  
A: Yes, methods like `toLakhs()` and `toCrores()` accept a `$decimals` parameter.

**Q: Does it work with Blade templates?**  
A: Yes! You can use the `Currency` facade directly in Blade templates as shown in the examples above.

**Q: How do I handle very large amounts (Thousands of Crores)?**  
A: The package handles amounts up to PHP's float limit. For extremely large numbers, consider using BC Math functions.

---

<p align="center">Made with ❤️ for Indian Developers</p>

<p align="center">
  <strong>Supporting Digital India • Made in India 🇮🇳</strong>
</p>
