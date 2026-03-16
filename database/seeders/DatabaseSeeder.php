<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\HeroSlide;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Site Settings ─────────────────────────────────────────
        $settings = [
            'site_name'           => 'Luma',
            'site_tagline'        => 'Light. Warm. Yours.',
            'site_email'          => 'hello@luma.com',
            'site_phone'          => '+1 (555) 012-3456',
            'site_address'        => '24 Amber Lane, Brooklyn, NY 11201',
            'footer_about'        => 'Luma curates warm, considered objects for everyday living.',
            'currency_symbol'     => '$',
            'shipping_cost'       => '9.00',
            'free_shipping_over'  => '120.00',
            'social_instagram'    => 'https://instagram.com',
            'social_facebook'     => 'https://facebook.com',
            'social_twitter'      => 'https://twitter.com',
            'announcement_text'   => 'Free shipping on orders over $120 · Use code WELCOME10 for 10% off',
            'announcement_link'   => '',
            'announcement_active' => '1',
        ];
        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // ── Hero Slides ───────────────────────────────────────────
        HeroSlide::insert([
            [
                'image'        => null,
                'headline'     => 'Objects That Last',
                'subheadline'  => 'Curated goods for people who care about what they bring home.',
                'button_text'  => 'Shop the Collection',
                'button_url'   => '/shop',
                'is_active'    => true,
                'sort_order'   => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'image'        => null,
                'headline'     => 'New Arrivals',
                'subheadline'  => 'Fresh pieces added every week. Be the first to find yours.',
                'button_text'  => 'Explore New In',
                'button_url'   => '/shop?filter=new',
                'is_active'    => true,
                'sort_order'   => 2,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

       // ── Categories ────────────────────────────────────────────
$categories = [
    ['name' => 'T-Shirts', 'slug' => 't-shirts', 'sort_order' => 1],
    ['name' => 'Hoodies',  'slug' => 'hoodies',  'sort_order' => 2],
    ['name' => 'Caps',     'slug' => 'caps',     'sort_order' => 3],
    ['name' => 'Mugs',     'slug' => 'mugs',     'sort_order' => 4],
    ['name' => 'Jackets',  'slug' => 'jackets',  'sort_order' => 5],
];
        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }

        $tshirts = Category::where('slug', 't-shirts')->first();
$hoodies = Category::where('slug', 'hoodies')->first();
$caps    = Category::where('slug', 'caps')->first();
$mugs    = Category::where('slug', 'mugs')->first();
$jackets = Category::where('slug', 'jackets')->first();

        // ── Supplier ──────────────────────────────────────────────
        $supplier = Supplier::create([
            'name'           => 'Amber Goods Co.',
            'contact_person' => 'Mia Holt',
            'email'          => 'mia@ambergoods.com',
            'phone'          => '+1 555 987 6543',
            'is_active'      => true,
        ]);

       // ── Products ──────────────────────────────────────────────
$products = [
    [
        'category_id'  => $hoodies->id,
        'name'         => 'Charcoal Logo Hoodie',
        'slug'         => 'charcoal-logo-hoodie',
        'sku'          => 'BRND-001',
        'short_description' => 'Premium charcoal hoodie with oversized b. front logo print.',
        'description'  => 'A heavyweight everyday hoodie in charcoal grey featuring the signature b. logo with orange square detail. Soft brushed interior, kangaroo pocket, and adjustable drawstrings for a clean streetwear fit.',
        'price'        => 79.00,
        'cost_price'   => 28.00,
        'stock'        => 30,
        'is_featured'  => true,
        'is_new'       => true,
        'variants'     => ['Size' => ['S', 'M', 'L', 'XL', 'XXL']],
        'review_count' => 0,
        'review_avg'   => 0,
    ],
    [
        'category_id'  => $caps->id,
        'name'         => 'Embroidered Logo Cap',
        'slug'         => 'embroidered-logo-cap',
        'sku'          => 'BRND-002',
        'short_description' => 'Structured grey cap with embroidered b. logo.',
        'description'  => 'Minimal six-panel cap in grey with an embroidered black b. logo and orange accent square. Adjustable back closure and curved brim make it an easy daily essential.',
        'price'        => 29.00,
        'cost_price'   => 9.00,
        'stock'        => 50,
        'is_featured'  => true,
        'variants'     => ['Size' => ['One Size']],
        'review_count' => 0,
        'review_avg'   => 0,
    ],
    [
        'category_id'  => $mugs->id,
        'name'         => 'Ceramic Logo Mug',
        'slug'         => 'ceramic-logo-mug',
        'sku'          => 'BRND-003',
        'short_description' => 'White ceramic mug with bold b. logo print.',
        'description'  => 'A clean 11oz ceramic mug featuring the brndng. b. mark in black with orange square detail. Perfect for coffee, tea, and desk setups that need a sharp branded touch.',
        'price'        => 18.00,
        'cost_price'   => 5.50,
        'stock'        => 80,
        'is_featured'  => true,
        'is_new'       => true,
        'review_count' => 0,
        'review_avg'   => 0,
    ],
    [
        'category_id'  => $hoodies->id,
        'name'         => 'Heather Grey Logo Hoodie',
        'slug'         => 'heather-grey-logo-hoodie',
        'sku'          => 'BRND-004',
        'short_description' => 'Classic heather grey hoodie with large front b. print.',
        'description'  => 'A versatile heather grey hoodie designed with a large front b. logo and orange square accent. Comfortable midweight fleece with ribbed cuffs and hem, made for casual everyday wear.',
        'price'        => 75.00,
        'cost_price'   => 27.00,
        'stock'        => 28,
        'is_featured'  => true,
        'variants'     => ['Size' => ['S', 'M', 'L', 'XL']],
        'review_count' => 0,
        'review_avg'   => 0,
    ],
    [
        'category_id'  => $tshirts->id,
        'name'         => 'Heather Grey Logo T-Shirt',
        'slug'         => 'heather-grey-logo-t-shirt',
        'sku'          => 'BRND-005',
        'short_description' => 'Soft heather grey tee with oversized b. chest graphic.',
        'description'  => 'A lightweight cotton t-shirt in heather grey featuring the bold b. logo across the front. Easy to style, breathable, and ideal for everyday branded merchandise collections.',
        'price'        => 32.00,
        'cost_price'   => 10.00,
        'stock'        => 60,
        'is_new'       => true,
        'variants'     => ['Size' => ['S', 'M', 'L', 'XL', 'XXL']],
        'review_count' => 0,
        'review_avg'   => 0,
    ],
    [
        'category_id'  => $jackets->id,
        'name'         => 'Charcoal Logo Bomber Jacket',
        'slug'         => 'charcoal-logo-bomber-jacket',
        'sku'          => 'BRND-006',
        'short_description' => 'Minimal bomber jacket with front b. logo print.',
        'description'  => 'A sleek charcoal bomber jacket with ribbed collar, cuffs, and hem, finished with a front b. logo and orange square accent. Lightweight outerwear with a sharp, modern branded aesthetic.',
        'price'        => 110.00,
        'cost_price'   => 42.00,
        'stock'        => 18,
        'is_featured'  => true,
        'review_count' => 0,
        'review_avg'   => 0,
    ],
    [
        'category_id'  => $tshirts->id,
        'name'         => 'Charcoal Chest Logo T-Shirt',
        'slug'         => 'charcoal-chest-logo-t-shirt',
        'sku'          => 'BRND-007',
        'short_description' => 'Dark charcoal tee with small left chest b. logo.',
        'description'  => 'A minimalist charcoal t-shirt featuring a smaller b. logo placement on the left chest. Clean, understated, and ideal for customers who prefer subtle branded pieces.',
        'price'        => 30.00,
        'cost_price'   => 9.00,
        'stock'        => 55,
        'is_on_sale'   => true,
        'sale_price'   => 24.00,
        'variants'     => ['Size' => ['S', 'M', 'L', 'XL']],
        'review_count' => 0,
        'review_avg'   => 0,
    ],
];

        $productModels = [];
        foreach ($products as $data) {
            $productModels[] = Product::create(array_merge($data, ['is_active' => true]));
        }

        // ── Purchase Order (received) ─────────────────────────────
        $po = PurchaseOrder::create([
            'supplier_id'      => $supplier->id,
            'reference_number' => 'PO-2024-0001',
            'order_date'       => now()->subDays(10)->toDateString(),
            'expected_date'    => now()->subDays(3)->toDateString(),
            'received_date'    => now()->subDays(2)->toDateString(),
            'total_cost'       => 0,
            'status'           => 'received',
            'notes'            => 'Initial stock order.',
        ]);
        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id'        => $productModels[0]->id,
            'product_name'      => $productModels[0]->name,
            'quantity_ordered'  => 30,
            'quantity_received' => 30,
            'cost_per_unit'     => 34.00,
            'total_cost'        => 1020.00,
        ]);
        $po->update(['total_cost' => 1020.00]);

        // ── Coupons ───────────────────────────────────────────────
        Coupon::insert([
            [
                'code'                  => 'WELCOME10',
                'description'           => '10% off for new customers',
                'type'                  => 'percentage',
                'value'                 => 10.00,
                'min_order_amount'      => 0,
                'max_uses'              => null,
                'used_count'            => 3,
                'max_uses_per_customer' => 1,
                'is_active'             => true,
                'expires_at'            => null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'code'                  => 'SAVE15',
                'description'           => '$15 off orders over $80',
                'type'                  => 'fixed',
                'value'                 => 15.00,
                'min_order_amount'      => 80.00,
                'max_uses'              => 100,
                'used_count'            => 7,
                'max_uses_per_customer' => 1,
                'is_active'             => true,
                'expires_at'            => now()->addMonths(3),
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'code'                  => 'FREESHIP',
                'description'           => 'Free shipping — no minimum',
                'type'                  => 'free_shipping',
                'value'                 => 0,
                'min_order_amount'      => 0,
                'max_uses'              => 50,
                'used_count'            => 2,
                'max_uses_per_customer' => 1,
                'is_active'             => true,
                'expires_at'            => now()->addMonths(1),
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'code'                  => 'EXPIRED20',
                'description'           => 'Expired test coupon',
                'type'                  => 'percentage',
                'value'                 => 20.00,
                'min_order_amount'      => 0,
                'max_uses'              => null,
                'used_count'            => 0,
                'max_uses_per_customer' => 1,
                'is_active'             => true,
                'expires_at'            => now()->subDay(),
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ]);

        // ── Demo Customers ────────────────────────────────────────
        $customer1 = Customer::create([
            'first_name' => 'Emma',
            'last_name'  => 'Clarke',
            'email'      => 'emma@example.com',
            'phone'      => '+1 555 100 2000',
            'password'   => Hash::make('password'),
        ]);
        CustomerAddress::create([
            'customer_id'   => $customer1->id,
            'label'         => 'Home',
            'full_name'     => 'Emma Clarke',
            'phone'         => '+1 555 100 2000',
            'address_line1' => '14 Maple Street',
            'city'          => 'Brooklyn',
            'state'         => 'NY',
            'postal_code'   => '11201',
            'country'       => 'USA',
            'is_default'    => true,
        ]);

        $customer2 = Customer::create([
            'first_name' => 'James',
            'last_name'  => 'Park',
            'email'      => 'james@example.com',
            'phone'      => '+1 555 300 4000',
            'password'   => Hash::make('password'),
        ]);
        CustomerAddress::create([
            'customer_id'   => $customer2->id,
            'label'         => 'Home',
            'full_name'     => 'James Park',
            'address_line1' => '88 Oak Avenue',
            'city'          => 'Manhattan',
            'state'         => 'NY',
            'postal_code'   => '10001',
            'country'       => 'USA',
            'is_default'    => true,
        ]);

        // ── Sample Orders ─────────────────────────────────────────
        $welcomeCoupon = Coupon::where('code', 'WELCOME10')->first();

       // ── Demo Orders ───────────────────────────────────────────

// Order 1 — Emma
$order1 = Order::create([
    'order_number'         => 'ORD-BRND00001',
    'customer_id'          => $customer1->id,
    'customer_name'        => 'Emma Clarke',
    'customer_email'       => 'emma@example.com',
    'customer_phone'       => '+1 555 100 2000',
    'shipping_address'     => '14 Maple Street',
    'shipping_city'        => 'Brooklyn',
    'shipping_state'       => 'NY',
    'shipping_postal_code' => '11201',
    'shipping_country'     => 'USA',
    'subtotal'             => 97.00, // 79 + 18
    'shipping_cost'        => 0.00,
    'coupon_id'            => $welcomeCoupon->id,
    'coupon_code'          => 'WELCOME10',
    'coupon_discount'      => 9.70,
    'discount'             => 9.70,
    'total'                => 87.30,
    'cost_total'           => 33.50,
    'status'               => 'delivered',
    'payment_status'       => 'paid',
    'payment_method'       => 'card',
]);

OrderItem::create([
    'order_id'      => $order1->id,
    'product_id'    => $productModels[0]->id,
    'product_name'  => 'Charcoal Logo Hoodie',
    'product_price' => 79.00,
    'product_cost'  => 28.00,
    'quantity'      => 1,
    'variant'       => ['Size' => 'L'],
    'line_total'    => 79.00,
    'line_cost'     => 28.00,
    'line_profit'   => 51.00,
]);

OrderItem::create([
    'order_id'      => $order1->id,
    'product_id'    => $productModels[2]->id,
    'product_name'  => 'Ceramic Logo Mug',
    'product_price' => 18.00,
    'product_cost'  => 5.50,
    'quantity'      => 1,
    'line_total'    => 18.00,
    'line_cost'     => 5.50,
    'line_profit'   => 12.50,
]);


// Order 2 — James
$order2 = Order::create([
    'order_number'         => 'ORD-BRND00002',
    'customer_id'          => $customer2->id,
    'customer_name'        => 'James Park',
    'customer_email'       => 'james@example.com',
    'shipping_address'     => '88 Oak Avenue',
    'shipping_city'        => 'Manhattan',
    'shipping_state'       => 'NY',
    'shipping_postal_code' => '10001',
    'shipping_country'     => 'USA',
    'subtotal'             => 110.00,
    'shipping_cost'        => 10.00,
    'discount'             => 0,
    'total'                => 120.00,
    'cost_total'           => 42.00,
    'status'               => 'shipped',
    'payment_status'       => 'paid',
    'payment_method'       => 'card',
]);

OrderItem::create([
    'order_id'      => $order2->id,
    'product_id'    => $productModels[5]->id,
    'product_name'  => 'Charcoal Logo Bomber Jacket',
    'product_price' => 110.00,
    'product_cost'  => 42.00,
    'quantity'      => 1,
    'variant'       => ['Size' => 'M'],
    'line_total'    => 110.00,
    'line_cost'     => 42.00,
    'line_profit'   => 68.00,
]);


// Order 3 — guest
$order3 = Order::create([
    'order_number'         => 'ORD-BRND00003',
    'customer_id'          => null,
    'customer_name'        => 'Sophie Renard',
    'customer_email'       => 'sophie@example.com',
    'customer_phone'       => '+33 6 12 34 56 78',
    'shipping_address'     => '3 Rue de Rivoli',
    'shipping_city'        => 'Paris',
    'shipping_state'       => null,
    'shipping_postal_code' => '75001',
    'shipping_country'     => 'France',
    'subtotal'             => 54.00, // 30 + 24 sale price
    'shipping_cost'        => 12.00,
    'discount'             => 0,
    'total'                => 66.00,
    'cost_total'           => 19.00,
    'status'               => 'pending',
    'payment_status'       => 'unpaid',
    'payment_method'       => 'card',
]);

OrderItem::create([
    'order_id'      => $order3->id,
    'product_id'    => $productModels[6]->id,
    'product_name'  => 'Charcoal Chest Logo T-Shirt',
    'product_price' => 24.00,
    'product_cost'  => 9.00,
    'quantity'      => 1,
    'variant'       => ['Size' => 'M'],
    'line_total'    => 24.00,
    'line_cost'     => 9.00,
    'line_profit'   => 15.00,
]);

OrderItem::create([
    'order_id'      => $order3->id,
    'product_id'    => $productModels[1]->id,
    'product_name'  => 'Embroidered Logo Cap',
    'product_price' => 29.00,
    'product_cost'  => 9.00,
    'quantity'      => 1,
    'variant'       => ['Size' => 'One Size'],
    'line_total'    => 29.00,
    'line_cost'     => 9.00,
    'line_profit'   => 20.00,
]);

       // ── Demo Reviews ──────────────────────────────────────────
Review::create([
    'product_id'   => $productModels[0]->id, // Charcoal Logo Hoodie
    'customer_id'  => $customer1->id,
    'order_id'     => $order1->id,
    'author_name'  => 'Emma Clarke',
    'author_email' => 'emma@example.com',
    'rating'       => 5,
    'title'        => 'Super clean and comfortable',
    'body'         => 'The fit is excellent and the logo looks even better in person. The fabric feels premium and the charcoal colour works with everything.',
    'status'       => 'approved',
    'approved_at'  => now()->subDays(2),
]);

Review::create([
    'product_id'   => $productModels[2]->id, // Ceramic Logo Mug
    'customer_id'  => $customer1->id,
    'order_id'     => $order1->id,
    'author_name'  => 'Emma Clarke',
    'author_email' => 'emma@example.com',
    'rating'       => 4,
    'title'        => 'Nice everyday mug',
    'body'         => 'Love the simple branding and the print quality is solid. Would have liked it slightly bigger, but overall it looks great on my desk.',
    'status'       => 'approved',
    'approved_at'  => now()->subDays(2),
]);

Review::create([
    'product_id'   => $productModels[5]->id, // Charcoal Logo Bomber Jacket
    'customer_id'  => $customer2->id,
    'order_id'     => $order2->id,
    'author_name'  => 'James Park',
    'author_email' => 'james@example.com',
    'rating'       => 5,
    'title'        => 'Sharp minimal jacket',
    'body'         => 'Really impressed with the quality. The design is subtle but strong, and the jacket feels lightweight without looking cheap.',
    'status'       => 'pending',
]);

        // Refresh aggregates for products that got reviews
        $productModels[0]->refreshReviewAggregates();
        $productModels[1]->refreshReviewAggregates();
        $productModels[5]->refreshReviewAggregates();

        // ── Contact Messages ──────────────────────────────────────
        ContactMessage::insert([
            [
                'name'       => 'Aya Mansour',
                'email'      => 'aya@example.com',
                'subject'    => 'Wholesale inquiry',
                'message'    => 'Hi, I run a small boutique and would love to stock a selection of your products. Could we discuss wholesale pricing?',
                'is_read'    => false,
                'read_at'    => null,
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ],
            [
                'name'       => 'Tom Green',
                'email'      => 'tom@example.com',
                'subject'    => 'Order question',
                'message'    => 'I placed order ORD-LMA00003 a few days ago and haven\'t received a confirmation email. Could you check?',
                'is_read'    => true,
                'read_at'    => now()->subHour(),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHour(),
            ],
        ]);
    }
}