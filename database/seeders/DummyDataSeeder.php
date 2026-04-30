<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users with different roles
        $users = [
            // Admin user
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'address' => 'Damascus, Syria',
                'city' => 'Damascus',
                'phone' => '0933123456',
                'role' => 'admin',
            ],
            // Driver users
            [
                'name' => 'Ahmed Driver',
                'email' => 'ahmed@example.com',
                'password' => Hash::make('password'),
                'address' => 'Aleppo, Syria',
                'city' => 'Aleppo',
                'phone' => '0933123457',
                'role' => 'driver',
            ],
            [
                'name' => 'Mohammed Driver',
                'email' => 'mohammed@example.com',
                'password' => Hash::make('password'),
                'address' => 'Homs, Syria',
                'city' => 'Homs',
                'phone' => '0933123458',
                'role' => 'driver',
            ],
            // Regular customers
            [
                'name' => 'Omar Customer',
                'email' => 'omar@example.com',
                'password' => Hash::make('password'),
                'address' => 'Damascus, Mazzeh',
                'city' => 'Damascus',
                'phone' => '0933123459',
                'role' => 'user',
            ],
            [
                'name' => 'Layla Customer',
                'email' => 'layla@example.com',
                'password' => Hash::make('password'),
                'address' => 'Aleppo, Suleimaniyeh',
                'city' => 'Aleppo',
                'phone' => '0933123460',
                'role' => 'user',
            ],
            [
                'name' => 'Hassan Customer',
                'email' => 'hassan@example.com',
                'password' => Hash::make('password'),
                'address' => 'Latakia, Ramel',
                'city' => 'Latakia',
                'phone' => '0933123461',
                'role' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create stores
        $stores = [
            [
                'name' => 'السوبر ماركت المركزي',
                'image' => 'stores/central-market.jpg',
                'delivery_price' => 5000,
                'type' => 'store',
                'popular' => true,
            ],
            [
                'name' => 'مخبوزات وحلويات دمشق',
                'image' => 'stores/damascus-bakery.jpg',
                'delivery_price' => 4000,
                'type' => 'store',
                'popular' => true,
            ],
            [
                'name' => 'مطعم ومشاوي الشام',
                'image' => 'stores/sham-grill.jpg',
                'delivery_price' => 6000,
                'type' => 'restaurant',
                'popular' => true,
            ],
            [
                'name' => 'صيدلية الصحة',
                'image' => 'stores/health-pharmacy.jpg',
                'delivery_price' => 3000,
                'type' => 'store',
                'popular' => false,
            ],
            [
                'name' => 'سوبر ماركت حلب',
                'image' => 'stores/aleppo-market.jpg',
                'delivery_price' => 4500,
                'type' => 'store',
                'popular' => true,
            ],
            [
                'name' => 'مقهى حلب',
                'image' => 'stores/aleppo-cafe.jpg',
                'delivery_price' => 3500,
                'type' => 'cafe',
                'popular' => false,
            ],
        ];

        foreach ($stores as $storeData) {
            Store::create($storeData);
        }

        // Create products for each store
        $storeIds = Store::pluck('id')->toArray();
        $productNames = [
            'store' => ['رز بسمتي', 'زيت زيتون', 'سكر', 'شاي', 'قهوة', 'حليب', 'زبادي', 'جبنة بيضاء', 'معكرونة', 'عدس'],
            'cafe' => ['خبز عربي', 'كعك', 'كرواسون', 'بسكويت', 'كيك', 'مناقيش', 'بيتزا', 'خبز توست', 'معجنات', 'خبز صاج'],
            'restaurant' => ['شاورما', 'فلافل', 'همبرغر', 'بيتزا', 'مشاوي مشكلة', 'مندي', 'كبسة', 'مقلوبة', 'محشي', 'يبرق'],
        ];

        foreach ($storeIds as $storeId) {
            $store = Store::find($storeId);
            $type = $store->type;
            $names = $productNames[$type] ?? $productNames['supermarket'];

            for ($i = 0; $i < 10; $i++) {
                Product::create([
                    'name' => $names[$i] . ' ' . $store->name,
                    'description' => 'وصف تفصيلي للمنتج ' . $names[$i],
                    'price' => rand(1000, 50000),
                    'image' => 'products/' . $type . '-' . ($i + 1) . '.jpg',
                    'store_id' => $storeId,
                    'quantity' => rand(10, 100),
                ]);
            }
        }

        // Get all users and products
        $users = User::where('role', 'customer')->get();
        $products = Product::all();

        // Create favorites and carts for each customer
        foreach ($users as $user) {
            // Add random favorites (2-5 products)
            $favoriteProducts = $products->random(rand(2, 5));
            $user->favorites()->attach($favoriteProducts->pluck('id')->toArray());

            // Create cart for user
            $cart = Cart::create([
                'user_id' => $user->id,
            ]);

            // Add random items to cart (1-3 products)
            $cartProducts = $products->random(rand(1, 3));
            foreach ($cartProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'price' => $product->price,
                ]);
            }
        }

        // Create some orders with different statuses
        $drivers = User::where('role', 'driver')->get();
        $statuses = ['pending', 'in_progress', 'on_the_way', 'delivered', 'cancelled'];
        $paymentMethods = ['cash', 'card'];

        foreach ($users as $index => $user) {
            // Create 2-4 orders per customer
            $ordersCount = rand(2, 4);

            for ($i = 0; $i < $ordersCount; $i++) {
                $store = Store::inRandomOrder()->first();
                $status = $statuses[array_rand($statuses)];

                $order = Order::create([
                    'user_id' => $user->id,
                    'driver_id' => in_array($status, ['out_for_delivery', 'delivered']) ? $drivers->random()->id : null,
                    'store_id' => $store->id,
                    'total_amount' => 0,
                    'status' => $status,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'payment_status' => rand(0, 1) ? 'paid' : 'unpaid',
                    'delivery_fee' => $store->delivery_price,
                    'discount_amount' => rand(0, 5000),
                    'address' => $user->address,
                    'latitude' => 33.5138 + (rand(-100, 100) / 1000),
                    'longitude' => 36.2765 + (rand(-100, 100) / 1000),
                    'notes' => rand(0, 1) ? 'ملاحظات خاصة للطلب' : null,
                ]);

                // Add 2-4 items to each order
                $orderItemsCount = rand(2, 4);
                $orderTotal = 0;

                for ($j = 0; $j < $orderItemsCount; $j++) {
                    $product = Product::where('store_id', $store->id)->inRandomOrder()->first();
                    $quantity = rand(1, 3);
                    $price = $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);

                    $orderTotal += $price * $quantity;
                }

                // Update order total
                $order->update([
                    'total_amount' => $orderTotal + $order->delivery_fee - $order->discount_amount,
                ]);
            }
        }

        // Create additional products for variety
        $additionalProducts = [
            'مشروبات غازية', 'عصير طبيعي', 'مياه معدنية', 'حلويات شرقية', 'شوكولاتة',
            'آيس كريم', 'خضروات طازجة', 'فواكه', 'لحوم', 'دواجن', 'أسماك', 'بهارات',
            'معلبات', 'منظفات', 'منتجات ورقية', 'أجبان', 'البان', 'عسل', 'مربى', 'حبوب'
        ];

        foreach ($storeIds as $storeId) {
            $randomProducts = array_rand(array_flip($additionalProducts), 5);
            foreach ($randomProducts as $productName) {
                Product::create([
                    'name' => $productName,
                    'description' => 'وصف ' . $productName,
                    'price' => rand(2000, 30000),
                    'image' => 'products/additional-' . rand(1, 20) . '.jpg',
                    'store_id' => $storeId,
                    'quantity' => rand(5, 50),
                ]);
            }
        }
    }
}
