<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VNPayQRTest extends TestCase
{
    use RefreshDatabase;

    public function test_vnpay_qr_checkout_and_callback()
    {
        // 1. Create a temporary user and product (or grab existing ones)
        $user = User::factory()->create();
        $category = \App\Models\Category::first() ?: \App\Models\Category::create(['name' => 'Stationery', 'slug' => 'stationery']);

        $product = Product::first() ?: Product::create([
            'name' => 'Atelier Notebook',
            'slug' => 'atelier-notebook',
            'category_id' => $category->id,
            'price' => 150000,
            'cost_price' => 100000,
            'stock' => 10,
            'description' => 'Premium atelier notebook',
            'image' => null,
            'sold_count' => 0
        ]);

        // 2. Set cart session
        $cart = [
            $product->id => [
                'name' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
                'image' => $product->image
            ]
        ];

        // 3. Post checkout request as the user
        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('checkout'), [
                'customer_name' => 'Nguyen Van A',
                'customer_phone' => '0987654321',
                'shipping_address' => '123 Atelier Street, District 1, HCMC',
                'payment_method' => 'vnpay_qr', // The new method we added!
            ]);

        // 4. Assert response is redirect
        $response->assertStatus(302);
        $redirectUrl = $response->headers->get('Location');
        
        $this->assertStringContainsString('sandbox.vnpayment.vn', $redirectUrl);
        $this->assertStringContainsString('vnp_BankCode=VNPAYQR', $redirectUrl);
        $this->assertStringContainsString('vnp_SecureHash=', $redirectUrl);

        // 5. Extract order ID and txnRef from redirect URL to simulate callback
        $parsedUrl = parse_url($redirectUrl);
        parse_str($parsedUrl['query'], $queryParams);
        
        $txnRef = $queryParams['vnp_TxnRef'];
        $orderId = substr($txnRef, 0, -10);
        
        $order = Order::find($orderId);
        $this->assertNotNull($order);
        $this->assertEquals('vnpay_qr', $order->payment_method);
        $this->assertEquals('pending', $order->payment_status);

        // 6. Simulate successful return callback from VNPay
        // Build mock callback params
        $callbackParams = [
            'vnp_Amount' => $queryParams['vnp_Amount'],
            'vnp_BankCode' => $queryParams['vnp_BankCode'],
            'vnp_BankTranNo' => 'FT12345678',
            'vnp_CardType' => 'QR',
            'vnp_OrderInfo' => $queryParams['vnp_OrderInfo'],
            'vnp_PayDate' => date('YmdHis'),
            'vnp_ResponseCode' => '00',
            'vnp_TmnCode' => $queryParams['vnp_TmnCode'],
            'vnp_TransactionNo' => '14285790',
            'vnp_TransactionStatus' => '00',
            'vnp_TxnRef' => $queryParams['vnp_TxnRef'],
            'vnp_SecureHashType' => 'SHA512'
        ];

        // Compute valid signature for the mock callback using the sandbox secret key
        $vnp_HashSecret = 'XNBQQMACUEKLOUOUVDPMHOHVEABHXXEI';
        $paramsForHash = $callbackParams;
        unset($paramsForHash['vnp_SecureHashType']);
        unset($paramsForHash['vnp_SecureHash']);

        ksort($paramsForHash);
        $hashData = "";
        $i = 0;
        foreach ($paramsForHash as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . rawurlencode($key) . "=" . rawurlencode($value);
            } else {
                $hashData .= rawurlencode($key) . "=" . rawurlencode($value);
                $i = 1;
            }
        }
        $callbackParams['vnp_SecureHash'] = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Call the return route
        $callbackResponse = $this->actingAs($user)->get(route('vnpay.return', $callbackParams));
        
        // Assert it redirects to home with success message
        $callbackResponse->assertRedirect(route('home'));
        $callbackResponse->assertSessionHas('success');

        // 7. Verify order status in DB was updated correctly
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('Processing', $order->status);
        $this->assertEquals('14285790', $order->transaction_id);

        // Clean up temporary objects
        $order->orderItems()->delete();
        $order->delete();
        $user->delete();
    }
}
