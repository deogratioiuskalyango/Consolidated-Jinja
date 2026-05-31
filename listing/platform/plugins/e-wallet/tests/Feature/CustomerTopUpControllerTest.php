<?php

namespace Botble\EWallet\Tests\Feature;

use Botble\Base\Supports\BaseTestCase;
use Botble\Ecommerce\Models\Customer;
use Botble\EWallet\Enums\TopUpStatusEnum;
use Botble\EWallet\Models\Wallet;
use Botble\EWallet\Models\WalletTopUp;
use Botble\EWallet\Services\TopUpService;
use Botble\EWallet\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTopUpControllerTest extends BaseTestCase
{
    use RefreshDatabase;

    protected TopUpService $topUpService;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->topUpService = app(TopUpService::class);
        $this->walletService = app(WalletService::class);
        $this->enableEWallet();
    }

    protected function enableEWallet(): void
    {
        setting()->forceSet('e_wallet_enabled', true)->save();
        setting()->forceSet('e_wallet_enable_top_up', true)->save();
        setting()->forceSet('e_wallet_min_top_up', 1000)->save();
        setting()->forceSet('e_wallet_max_top_up', 100000000)->save();
    }

    protected function createCustomer(): Customer
    {
        return Customer::query()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    protected function createWallet(Customer $customer, int $balance = 0): Wallet
    {
        return Wallet::query()->create([
            'customer_id' => $customer->id,
            'balance' => $balance,
            'currency_code' => 'USD',
        ]);
    }

    public function test_topup_service_creates_topup(): void
    {
        $customer = $this->createCustomer();
        $this->createWallet($customer);

        $topup = $this->topUpService->createTopUp($customer->id, 5000);

        $this->assertNotNull($topup);
        $this->assertEquals(5000, $topup->amount);
        $this->assertEquals(TopUpStatusEnum::PENDING, $topup->status->getValue());
        $this->assertStringStartsWith('TU-', $topup->code);
    }

    public function test_topup_service_validates_minimum_amount(): void
    {
        $customer = $this->createCustomer();
        $this->createWallet($customer);

        $this->expectException(\InvalidArgumentException::class);

        $this->topUpService->createTopUp($customer->id, 500);
    }

    public function test_topup_service_validates_maximum_amount(): void
    {
        setting()->forceSet('e_wallet_max_top_up', 100000)->save();

        $customer = $this->createCustomer();
        $this->createWallet($customer);

        $this->expectException(\InvalidArgumentException::class);

        $this->topUpService->createTopUp($customer->id, 500000);
    }

    public function test_topup_completion_credits_wallet(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $topup = $this->topUpService->createTopUp($customer->id, 5000);
        $this->topUpService->completeTopUp($topup, 'payment_123', 'bank_transfer');

        $topup->refresh();
        $this->assertEquals(TopUpStatusEnum::COMPLETED, $topup->status->getValue());

        $wallet->refresh();
        $this->assertEquals(5000, $wallet->balance);
    }

    public function test_topup_completion_is_idempotent(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $topup = $this->topUpService->createTopUp($customer->id, 5000);
        $this->topUpService->completeTopUp($topup, 'payment_123', 'bank_transfer');
        $topup->refresh();
        $this->topUpService->completeTopUp($topup, 'payment_456', 'bank_transfer');

        $wallet->refresh();
        $this->assertEquals(5000, $wallet->balance);
    }

    public function test_topup_failure_does_not_credit_wallet(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $topup = $this->topUpService->createTopUp($customer->id, 5000);
        $this->topUpService->failTopUp($topup, 'Payment declined');

        $topup->refresh();
        $this->assertEquals(TopUpStatusEnum::FAILED, $topup->status->getValue());

        $wallet->refresh();
        $this->assertEquals(0, $wallet->balance);
    }

    public function test_topup_status_methods(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $pendingTopup = WalletTopUp::query()->create([
            'customer_id' => $customer->id,
            'wallet_id' => $wallet->id,
            'code' => 'TU-PENDING1',
            'amount' => 5000,
            'currency_code' => 'USD',
            'converted_amount' => 5000,
            'wallet_currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'status' => TopUpStatusEnum::PENDING,
        ]);

        $completedTopup = WalletTopUp::query()->create([
            'customer_id' => $customer->id,
            'wallet_id' => $wallet->id,
            'code' => 'TU-COMPLETE',
            'amount' => 5000,
            'currency_code' => 'USD',
            'converted_amount' => 5000,
            'wallet_currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'status' => TopUpStatusEnum::COMPLETED,
        ]);

        $this->assertTrue($pendingTopup->isPending());
        $this->assertFalse($pendingTopup->isCompleted());

        $this->assertFalse($completedTopup->isPending());
        $this->assertTrue($completedTopup->isCompleted());
    }

    public function test_topup_model_relationships(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $topup = WalletTopUp::query()->create([
            'customer_id' => $customer->id,
            'wallet_id' => $wallet->id,
            'code' => 'TU-TEST1234',
            'amount' => 5000,
            'currency_code' => 'USD',
            'converted_amount' => 5000,
            'wallet_currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'status' => TopUpStatusEnum::PENDING,
        ]);

        $this->assertNotNull($topup->customer);
        $this->assertEquals($customer->id, $topup->customer->id);

        $this->assertNotNull($topup->wallet);
        $this->assertEquals($wallet->id, $topup->wallet->id);
    }

    public function test_topup_generates_unique_codes(): void
    {
        $customer = $this->createCustomer();
        $this->createWallet($customer);

        $topup1 = $this->topUpService->createTopUp($customer->id, 5000);
        $topup2 = $this->topUpService->createTopUp($customer->id, 6000);
        $topup3 = $this->topUpService->createTopUp($customer->id, 7000);

        $this->assertNotEquals($topup1->code, $topup2->code);
        $this->assertNotEquals($topup2->code, $topup3->code);
        $this->assertNotEquals($topup1->code, $topup3->code);
    }

    public function test_topup_with_currency_conversion(): void
    {
        $customer = $this->createCustomer();
        $this->createWallet($customer);

        $topup = $this->topUpService->createTopUp(
            customerId: $customer->id,
            amountCents: 10000,
            currencyCode: 'EUR',
            exchangeRate: 1.1
        );

        $this->assertEquals(10000, $topup->amount);
        $this->assertEquals('EUR', $topup->currency_code);
        $this->assertEquals(1.1, (float) $topup->exchange_rate);
        $this->assertEquals(9091, $topup->converted_amount);
    }

    public function test_topup_formatted_amount(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $topup = WalletTopUp::query()->create([
            'customer_id' => $customer->id,
            'wallet_id' => $wallet->id,
            'code' => 'TU-FORMAT12',
            'amount' => 12345,
            'currency_code' => 'USD',
            'converted_amount' => 12345,
            'wallet_currency_code' => 'USD',
            'exchange_rate' => 1.0,
            'status' => TopUpStatusEnum::PENDING,
        ]);

        $this->assertNotEmpty($topup->formatted_amount);
    }

    public function test_topup_creates_wallet_if_not_exists(): void
    {
        $customer = $this->createCustomer();

        $this->assertNull(Wallet::query()->where('customer_id', $customer->id)->first());

        $topup = $this->topUpService->createTopUp($customer->id, 5000);

        $wallet = Wallet::query()->where('customer_id', $customer->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals($topup->wallet_id, $wallet->id);
    }

    public function test_topup_failure_stores_reason(): void
    {
        $customer = $this->createCustomer();
        $this->createWallet($customer);

        $topup = $this->topUpService->createTopUp($customer->id, 5000);
        $failedTopup = $this->topUpService->failTopUp($topup, 'Card declined');

        $this->assertEquals('Card declined', $failedTopup->metadata['failure_reason']);
    }

    public function test_multiple_topups_for_same_customer(): void
    {
        $customer = $this->createCustomer();
        $wallet = $this->createWallet($customer);

        $topup1 = $this->topUpService->createTopUp($customer->id, 5000);
        $topup2 = $this->topUpService->createTopUp($customer->id, 7500);

        $this->topUpService->completeTopUp($topup1, 'pay_1', 'card');
        $this->topUpService->completeTopUp($topup2, 'pay_2', 'card');

        $wallet->refresh();
        $this->assertEquals(12500, $wallet->balance);
    }

    public function test_topup_isolation_between_customers(): void
    {
        $customer1 = $this->createCustomer();
        $customer2 = Customer::query()->create([
            'name' => 'Customer 2',
            'email' => 'customer2@example.com',
            'password' => bcrypt('password'),
        ]);

        $wallet1 = $this->createWallet($customer1);
        $wallet2 = $this->createWallet($customer2);

        $topup1 = $this->topUpService->createTopUp($customer1->id, 5000);
        $topup2 = $this->topUpService->createTopUp($customer2->id, 10000);

        $this->topUpService->completeTopUp($topup1, 'pay_1', 'card');
        $this->topUpService->completeTopUp($topup2, 'pay_2', 'card');

        $wallet1->refresh();
        $wallet2->refresh();

        $this->assertEquals(5000, $wallet1->balance);
        $this->assertEquals(10000, $wallet2->balance);
    }
}
