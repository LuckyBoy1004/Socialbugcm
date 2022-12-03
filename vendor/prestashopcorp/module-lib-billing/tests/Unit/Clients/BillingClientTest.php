<?php

namespace PrestaShopCorp\Billing\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PrestaShopCorp\Billing\Clients\BillingClient;

class BillingClientTest extends TestCase
{
    protected $customer;
    protected $subscription;
    protected $plans;
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->customer = [
            'id' => 'b2581e4b-0030-4fc8-9bf2-7f01c550a946',
            'email' => 'takeshi.daveau@prestashop.com',
            'auto_collection' => 'on',
            'created_at' => 1646842866,
            'billing_address' => [
                'first_name' => 'Takeshi',
                'last_name' => 'Daveau',
                'company' => 'TDA',
                'line1' => 'Rue des rue',
                'city' => 'Lilas',
                'country' => 'FR',
                'zip' => '93333',
            ],
            'card_status' => 'valid',
            'primary_payment_source_id' => 'pm_AzqMGNSzhOTDa1BEP',
            'payment_method' => [
                'type' => 'card',
                'gateway' => 'stripe',
                'gateway_account_id' => 'gw_Azqe1TSLVjdNhdI',
                'status' => 'valid',
                'reference_id' => 'cus_LIQGgPFSj2r39T/card_1KbpQHGp5Dc2lo8uEdDJv8ac',
            ],
            'cf_shop_id' => 'b2581e4b-0030-4fc8-9bf2-7f01c550a946',
            'cf_consent' => 'False',
        ];
        $this->subscription = [
            'id' => '169lnASzhOWay1EQN',
            'plan_id' => 'rbm-advanced',
            'customer_id' => 'b2581e4b-0030-4fc8-9bf2-7f01c550a946',
            'status' => 'in_trial',
            'currency_code' => 'EUR',
            'has_scheduled_changes' => false,
            'billing_period' => 1,
            'billing_period_unit' => 'month',
            'due_invoices_count' => 0,
            'meta_data' => [
                'module' => 'rbm_example',
            ],
            'plan_amount' => 2000,
            'plan_quantity' => 1,
            'plan_unit_price' => 2000,
            'subscription_items' => [
                [
                    'item_price_id' => 'rbm-advanced',
                    'amount' => 2000,
                    'item_type' => 'plan',
                    'quantity' => 1,
                    'unit_price' => 2000,
                ],
            ],
            'created_at' => 1646931926,
            'cancelled_at' => 1648335600,
            'started_at' => 1646866800,
            'updated_at' => 1646934561,
            'trial_end' => 1648335599,
            'coupon' => [
                'coupon_id' => 'TDATEST20PERCENT',
                'applied_count' => 1,
                'coupon_code' => 'tda6359-20',
                'apply_till' => 1654811999,
            ],
            'is_free_trial_used' => true,
        ];
        $this->plans = [
            'limit' => 100,
            'offset' => null,
            'results' => [
                [
                    'id' => 'rbm-free',
                    'name' => 'rbm free',
                    'details_plan' => [
                        'title' => 'rbm free',
                        'features' => [
                            'Fonctionnalité 1 du rbm free',
                            'Fonctionnalité 2 du rbm free',
                            'Fonctionnalité 3 du rbm free',
                            'Fonctionnalité 4 du rbm free',
                        ],
                    ],
                    'price' => 100,
                    'period' => 1,
                    'currency_code' => 'EUR',
                    'period_unit' => 'month',
                    'trial_period' => 7,
                    'trial_period_unit' => 'day',
                    'pricing_model' => 'flat_fee',
                    'meta_data' => [
                        'module' => 'rbm_example',
                    ],
                ],
                [
                    'id' => 'rbm-advanced',
                    'name' => 'rbm advanced',
                    'details_plan' => [
                        'title' => 'rbm advanced',
                        'features' => [
                            'Fonctionnalité 1 du rbm advanced',
                            'Fonctionnalité 2 du rbm advanced',
                            'Fonctionnalité 3 du rbm advanced',
                            'Fonctionnalité 4 du rbm advanced',
                        ],
                    ],
                    'price' => 2000,
                    'period' => 1,
                    'currency_code' => 'EUR',
                    'period_unit' => 'month',
                    'trial_period' => 7,
                    'trial_period_unit' => 'day',
                    'pricing_model' => 'flat_fee',
                    'meta_data' => [
                        'module' => 'rbm_example',
                    ],
                ],
                [
                    'id' => 'rbm-ultimate',
                    'name' => 'rbm ultimate',
                    'details_plan' => [
                        'title' => 'rbm ultimate',
                        'features' => [
                            'Fonctionnalité 1 du rbm ultimate',
                            'Fonctionnalité 2 du rbm ultimate',
                            'Fonctionnalité 3 du rbm ultimate',
                            'Fonctionnalité 4 du rbm ultimate',
                        ],
                    ],
                    'price' => 10000,
                    'period' => 1,
                    'currency_code' => 'EUR',
                    'period_unit' => 'month',
                    'trial_period' => 7,
                    'trial_period_unit' => 'day',
                    'pricing_model' => 'flat_fee',
                    'meta_data' => [
                        'module' => 'rbm_example',
                    ],
                ],
                [
                    'id' => 'rbm-exempl-test',
                    'name' => 'rbm exempl test',
                    'details_plan' => null,
                    'price' => 999900,
                    'period' => 1,
                    'currency_code' => 'EUR',
                    'period_unit' => 'month',
                    'pricing_model' => 'flat_fee',
                    'meta_data' => [
                        'module' => 'rbm_example',
                    ],
                ],
            ],
        ];
    }

    public function testRetrieveCustomerById()
    {
        $billingClient = $this->getBillingClient(new Response(200, [], json_encode($this->customer)));
        $result = $billingClient->retrieveCustomerById('b2581e4b-0030-4fc8-9bf2-7f01c550a946');

        // Test the call made by the methods
        $this->assertEquals(count($this->container), 1);
        $request = $this->container[0]['request'];
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), 'v1/customers/b2581e4b-0030-4fc8-9bf2-7f01c550a946');

        // Test the format and the content
        $this->assertEquals($result['success'], true);
        $this->assertEquals($result['httpStatus'], 200);
        $this->assertEquals($result['body'], $this->customer);

        // Test with all params
        $result = $billingClient->retrieveCustomerById('b2581e4b-0030-4fc8-9bf2-7f01c550a946', 'v2');
        $request = $this->container[1]['request'];
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), 'v2/customers/b2581e4b-0030-4fc8-9bf2-7f01c550a946');
    }

    public function testRetrieveSubscriptionByCustomerId()
    {
        $billingClient = $this->getBillingClient(new Response(200, [], json_encode($this->subscription)));
        $result = $billingClient->retrieveSubscriptionByCustomerId('b2581e4b-0030-4fc8-9bf2-7f01c550a946');

        // Test the call made by the methods
        $this->assertEquals(count($this->container), 1);
        $request = $this->container[0]['request'];
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), 'v1/customers/b2581e4b-0030-4fc8-9bf2-7f01c550a946/subscriptions/rbm_example');

        // Test the format and the content
        $this->assertEquals($result['success'], true);
        $this->assertEquals($result['httpStatus'], 200);
        $this->assertEquals($result['body'], $this->subscription);

        // Test with all params
        $result = $billingClient->retrieveSubscriptionByCustomerId('b2581e4b-0030-4fc8-9bf2-7f01c550a946', 'v2');
        $request = $this->container[1]['request'];
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), 'v2/customers/b2581e4b-0030-4fc8-9bf2-7f01c550a946/subscriptions/rbm_example');
    }

    public function testRetrievePlansShouldCallTheProperRoute()
    {
        $billingClient = $this->getBillingClient(new Response(200, [], json_encode($this->plans)));
        $result = $billingClient->retrievePlans('fr');

        // Test the call made by the methods
        $this->assertEquals(count($this->container), 1);
        $request = $this->container[0]['request'];
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), 'v1/products/rbm_example/plans?status=active&lang_iso_code=fr&limit=10');

        // Test the format and the content
        $this->assertEquals($result['success'], true);
        $this->assertEquals($result['httpStatus'], 200);
        $this->assertEquals($result['body'], $this->plans);

        // Test with all params
        $result = $billingClient->retrievePlans('fr', 'v2', 'archived', 100, '["1234567","4567890"]');
        $request = $this->container[1]['request'];
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), 'v2/products/rbm_example/plans?status=archived&lang_iso_code=fr&limit=100&offset=%5B%221234567%22,%224567890%22%5D');
    }

    /**
     * getBillingClientAndContainer
     *
     * @param Response $response
     *
     * @return BillingClient
     */
    private function getBillingClient(Response $response)
    {
        $this->container = [];
        $history = Middleware::history($this->container);
        $mock = new MockHandler([
            $response,
            $response,
            $response,
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
            'base_url' => 'https://billing.distribution-integration.prestashop.net/',
            'defaults' => [
                'timeout' => 20,
                'exceptions' => true,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjM1MDM0MmIwMjU1MDAyYWI3NWUwNTM0YzU4MmVjYzY2Y2YwZTE3ZDIiLCJ0eXAiOiJKV1QifQ.eyJuYW1lIjoiUHJlc3RhU2hvcCIsImlzcyI6Imh0dHBzOi8vc2VjdXJldG9rZW4uZ29vZ2xlLmNvbS9wcmVzdGFzaG9wLXJlYWR5LXByb2QiLCJhdWQiOiJwcmVzdGFzaG9wLXJlYWR5LXByb2QiLCJhdXRoX3RpbWUiOjE2MzMxMDIzNzgsInVzZXJfaWQiOiJNbjZvdTg2dUFUUkJydFlqRlVua1pmNkZjNWUyIiwic3ViIjoiTW42b3U4NnVBVFJCcnRZakZVbmtaZjZGYzVlMiIsImlhdCI6MTYzMzcwNDcxNywiZXhwIjoxNjMzNzA4MzE3LCJlbWFpbCI6InRha2VzaGlfZGVtby1uaWFrX3ByZXN0YXNob3BfbmV0LTJhNjVhNDVhZUBwc2FjY291bnRzLnBzZXNzZW50aWFscy5uZXQiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImZpcmViYXNlIjp7ImlkZW50aXRpZXMiOnsiZW1haWwiOlsidGFrZXNoaV9kZW1vLW5pYWtfcHJlc3Rhc2hvcF9uZXQtMmE2NWE0NWFlQHBzYWNjb3VudHMucHNlc3NlbnRpYWxzLm5ldCJdfSwic2lnbl9pbl9wcm92aWRlciI6ImN1c3RvbSJ9fQ.WIqbDpoC_6o4eVfcr2RzJCQPz-IOFh9mtlOdhNOaNEu4cKJGPe7ARl_Sp36LsW0cuVePIijbWZiLubLXoycQ6W07KnBvR6SQ_3KpfxE5GUIFeGPsrNMPJ1qkvPDGOO_YEYp17oFQ5LYswq9-JeMWR3YbM4oENI6WD1jM5_iWaOY3BrdH5BRRraIwCVfiWsKuknTH-qEWU1AP2DNqtQstll8WOo01QAA-yocgS9zjoSJSBlqikdUoE3pYmH2C-fj5ZALEN4Qg27qchXW3L2wIc-16BQpqdnh2hst6kAB0pOcMi-G3UaXa569heoSBpf7Tu2gxdTgmNcbzubKrGMFLTg',
                ],
            ],
        ]);

        return new BillingClient('rbm_example', $client);
    }
}
