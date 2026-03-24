# VTU Integration Guide (SprintPay) for Reuse

This project connects VTU (airtime, data, cable, electricity) through SprintPay using:

- server-side API calls from Laravel
- wallet debit/refund protection in DB transactions
- frontend catalog + validation UX
- optional SprintPay webhook for wallet funding

Use this doc as a copy blueprint for another Laravel project.

## 1) Environment Variables

Add these to `.env`:

```env
WEBKEY=your_sprintpay_webkey
SPRINTPAY_WEBHOOK_SECRET=your_sprintpay_secret_token
SPRINTPAY_API_BASE=https://web.sprintpay.online/api
```

Then map in `config/services.php`:

```php
'sprintpay' => [
    'webhook_secret' => env('SPRINTPAY_WEBHOOK_SECRET'),
    'webkey' => env('WEBKEY'),
    'api_base' => rtrim(env('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'), '/'),
],
```

## 2) Core Service Class

Create a client like `app/Services/SprintPayVasClient.php` with:

- `configured()` checks key + secret present
- `getPublic($path, $query)` for catalog endpoints
- `getMerchantVas($path, $query)` and `postMerchantVas($path, $body)` for merchant endpoints
- automatic auth:
  - Bearer token = `SPRINTPAY_WEBHOOK_SECRET`
  - `key` query/body = `WEBKEY`
- `responseIndicatesSuccess()` to normalize inconsistent gateway responses
- `extractMessage()` to get meaningful error text

## 3) Routes

Add authenticated web routes (inside auth middleware group):

```php
Route::prefix('vas')->name('vas.')->controller(VtuBillsController::class)->group(function () {
    Route::get('airtime', 'airtime')->name('airtime');
    Route::post('airtime', 'buyAirtime')->name('airtime.buy');
    Route::get('data', 'data')->name('data');
    Route::post('data', 'buyData')->name('data.buy');
    Route::get('cable', 'cable')->name('cable');
    Route::post('cable/validate', 'validateCable')->name('cable.validate');
    Route::post('cable', 'buyCable')->name('cable.buy');
    Route::get('electricity', 'electricity')->name('electricity');
    Route::post('electricity/validate', 'validateElectricity')->name('electricity.validate');
    Route::post('electricity', 'buyElectricity')->name('electricity.buy');

    Route::get('catalog/data-variations', 'catalogDataVariations')->name('catalog.data-variations');
    Route::get('catalog/cable-plans', 'catalogCablePlans')->name('catalog.cable-plans');
    Route::get('catalog/electricity-variations', 'catalogElectricityVariations')->name('catalog.electricity-variations');
});
```

Optional API webhook route for wallet funding:

```php
Route::post('webhooks/sprintpay', SprintPayWebhookController::class);
```

## 4) Controller Flow (Critical)

Use one controller (`VtuBillsController`) with this pattern for each purchase:

1. Validate request
2. Normalize input (phone digits, service id lowercase, etc.)
3. Debit wallet in DB transaction (`tryDebitForVas`)
4. Call SprintPay endpoint
5. If failure -> refund wallet (`refundVas`) and return error
6. If success -> record transaction (`recordVasTransaction`)

### Current endpoints used

- Airtime buy: `merchant/vas/buy-ng-airtime`
- Data buy: `merchant/vas/buy-data`
- Cable validate: `merchant/vas/validate-cable`
- Cable buy: `merchant/vas/buy-cable`
- Electricity validate: `merchant/vas/validate-electricity-meter`
- Electricity buy: `merchant/vas/buy-electricity`
- Data catalog: `get-data-variations`
- Cable catalog: `cable-plan`
- Electricity catalog: `get-electricity-variations`
- Service list fallback (airtime): `get-service`

### Business rules currently enforced

- Airtime + Data phone must be exactly 11 digits
- Airtel airtime max per transaction is NGN 10,000
- On gateway error, wallet is refunded and warning is logged

## 5) Wallet and Transaction Requirements

This integration depends on these models/tables:

- `users` (wallet balance)
- `wallet_checks` (totals tracking)
- `transactions` (audit rows)

The debit/refund logic updates:

- `users.wallet`
- `wallet_checks.total_bought`
- `wallet_checks.wallet_amount`

And saves a `transactions` row with:

- unique `ref_id`
- `type = 4` for VTU
- `status = 2` on success

Important: `WalletCheck` must allow mass assignment for `user_id`:

```php
protected $fillable = ['user_id'];
```

## 6) Frontend Pages

Views in this project:

- `resources/views/vas/airtime.blade.php`
- `resources/views/vas/data.blade.php`
- `resources/views/vas/cable.blade.php`
- `resources/views/vas/electricity.blade.php`
- shared styles: `public/css/vtu-bills.css`

Key UX behaviors implemented:

- submit loaders on buy buttons (`Processing...`)
- disabled state to prevent double submit
- phone input restricted to digits, max 11
- data bundles auto-fetch when network changes
- data bundle picker supports search and category filters

## 7) Logging and Debugging

For failed gateway calls, log structured context:

- `endpoint`
- `request` payload
- HTTP `status`
- response `body`

Example pattern:

```php
Log::warning('SprintPay buy-data failed', [
    'endpoint' => $endpoint,
    'request' => $body,
    'status' => $resp->status(),
    'body' => $resp->body(),
]);
```

This is essential for production troubleshooting.

## 8) Webhook Funding (Optional but recommended)

`SprintPayWebhookController` is used to credit wallet after external payment:

- validates secret token from headers (via `SprintPayWebhookAuth`)
- reads `email`, `amount`, `order_id`
- calls `WalletFundingService::creditFromExternalPayment(...)`
- handles duplicate callbacks safely

If you copy this, keep idempotency behavior.

## 9) Migration Checklist for Another Project

1. Copy service config (`services.php`) and `.env` keys
2. Copy `SprintPayVasClient`
3. Copy/add VTU routes
4. Copy `VtuBillsController`
5. Ensure models/tables exist:
   - `users.wallet`
   - `wallet_checks` fields used in debit/refund
   - `transactions` fields used in `recordVasTransaction`
6. Add VTU views + CSS
7. Add webhook route/controller/auth if wallet funding callback is needed
8. Test in order:
   - catalog fetch
   - validation endpoints
   - airtime buy
   - data buy
   - cable/electricity buy
   - forced failure path (confirm wallet refund)

## 10) Notes

- Keep all SprintPay calls server-side only (never expose secret on frontend).
- If moving to cPanel/shared hosting, ensure:
  - `APP_DEBUG=false`
  - config cache is refreshed after env changes.
