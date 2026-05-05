<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Product::whereNull('cost_price')->orWhere('cost_price', 0)->get()->each(function($p) {
    $p->cost_price = $p->price * 0.7;
    $p->save();
});

App\Models\OrderItem::whereNull('cost_price')->orWhere('cost_price', 0)->get()->each(function($oi) {
    if($oi->product) {
        $oi->cost_price = $oi->product->cost_price;
        $oi->save();
    }
});
echo "UPDATED DATA\n";
