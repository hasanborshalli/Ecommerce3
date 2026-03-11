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
            ['name' => 'Living',      'slug' => 'living',      'sort_order' => 1],
            ['name' => 'Kitchen',     'slug' => 'kitchen',     'sort_order' => 2],
            ['name' => 'Bedroom',     'slug' => 'bedroom',     'sort_order' => 3],
            ['name' => 'Lighting',    'slug' => 'lighting',    'sort_order' => 4],
            ['name' => 'Stationery',  'slug' => 'stationery',  'sort_order' => 5],
            ['name' => 'Outdoor',     'slug' => 'outdoor',     'sort_order' => 6],
        ];
        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }

        $living     = Category::where('slug', 'living')->first();
        $kitchen    = Category::where('slug', 'kitchen')->first();
        $bedroom    = Category::where('slug', 'bedroom')->first();
        $lighting   = Category::where('slug', 'lighting')->first();
        $stationery = Category::where('slug', 'stationery')->first();
        $outdoor    = Category::where('slug', 'outdoor')->first();

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
                'category_id'  => $living->id,
                'name'         => 'Linen Throw Blanket',
                'slug'         => 'linen-throw-blanket',
                'sku'          => 'LMA-001',
                'short_description' => 'Pure linen throw in natural undyed tones.',
                'description'  => 'Woven from 100% European linen, this throw softens with every wash. Toss it over a chair, wrap it around your shoulders on cool evenings, or fold it at the foot of the bed.',
                'price'        => 89.00,
                'cost_price'   => 34.00,
                'stock'        => 24,
                'is_featured'  => true,
                'is_new'       => true,
                'variants'     => ['Color' => ['Natural', 'Stone', 'Slate']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'Matte Ceramic Mug',
                'slug'         => 'matte-ceramic-mug',
                'sku'          => 'LMA-002',
                'short_description' => 'Hand-thrown stoneware mug, dishwasher safe.',
                'description'  => 'Each mug is individually thrown on the wheel and finished in a warm matte glaze. Holds 12oz. Pairs perfectly with slow mornings.',
                'price'        => 32.00,
                'cost_price'   => 11.00,
                'stock'        => 48,
                'is_featured'  => true,
                'variants'     => ['Color' => ['Clay', 'Ash', 'Dune']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $bedroom->id,
                'name'         => 'Beeswax Pillar Candle',
                'slug'         => 'beeswax-pillar-candle',
                'sku'          => 'LMA-003',
                'short_description' => '100% pure beeswax, 40-hour burn time.',
                'description'  => 'Made from locally sourced beeswax with a natural honey scent. Burns cleanly for up to 40 hours. No synthetic fragrances or dyes.',
                'price'        => 28.00,
                'cost_price'   => 9.00,
                'stock'        => 60,
                'is_featured'  => true,
                'is_new'       => true,
                'variants'     => ['Size' => ['Small', 'Medium', 'Large']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $lighting->id,
                'name'         => 'Woven Rattan Pendant',
                'slug'         => 'woven-rattan-pendant',
                'sku'          => 'LMA-004',
                'short_description' => 'Handwoven rattan shade for warm ambient lighting.',
                'description'  => 'Hand-woven by artisans in Java, this pendant casts a warm, dappled glow. Fits standard E26 bulbs. Cord length 1.2m, adjustable.',
                'price'        => 145.00,
                'cost_price'   => 56.00,
                'stock'        => 12,
                'is_featured'  => false,
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $stationery->id,
                'name'         => 'Stitched Notebook A5',
                'slug'         => 'stitched-notebook-a5',
                'sku'          => 'LMA-005',
                'short_description' => 'Lay-flat notebook with 160 ivory pages.',
                'description'  => 'Thread-stitched binding opens completely flat. 80gsm ivory pages with subtle dot grid. Recycled kraft cover.',
                'price'        => 18.00,
                'cost_price'   => 6.00,
                'stock'        => 90,
                'is_new'       => true,
                'variants'     => ['Cover' => ['Kraft', 'Black', 'Terracotta']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'Olive Wood Serving Board',
                'slug'         => 'olive-wood-serving-board',
                'sku'          => 'LMA-006',
                'short_description' => 'Hand-carved olive wood board from Tunisia.',
                'description'  => 'Each board is unique, shaped by the natural grain of Tunisian olive trees. Oil occasionally with food-safe mineral oil to maintain the finish.',
                'price'        => 64.00,
                'cost_price'   => 24.00,
                'stock'        => 18,
                'is_featured'  => true,
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $living->id,
                'name'         => 'Jute Storage Basket',
                'slug'         => 'jute-storage-basket',
                'sku'          => 'LMA-007',
                'short_description' => 'Tightly woven jute basket with leather handles.',
                'description'  => 'Sturdy enough for blankets, magazines, or toy storage. Leather handles age beautifully over time.',
                'price'        => 52.00,
                'cost_price'   => 20.00,
                'stock'        => 30,
                'is_on_sale'   => true,
                'sale_price'   => 42.00,
                'variants'     => ['Size' => ['Small', 'Large']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $outdoor->id,
                'name'         => 'Terracotta Plant Pot',
                'slug'         => 'terracotta-plant-pot',
                'sku'          => 'LMA-008',
                'short_description' => 'Unglazed terracotta pot with drainage hole.',
                'description'  => 'Traditional hand-thrown terracotta. Breathable walls promote healthy root growth. Drainage hole included.',
                'price'        => 24.00,
                'cost_price'   => 8.00,
                'stock'        => 50,
                'variants'     => ['Size' => ['10cm', '15cm', '20cm', '25cm']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $bedroom->id,
                'name'         => 'Linen Pillowcase Set',
                'slug'         => 'linen-pillowcase-set',
                'sku'          => 'LMA-009',
                'short_description' => 'Set of 2 washed linen pillowcases.',
                'description'  => 'Stonewashed for softness from day one. Gets better with each wash. Fits standard and queen pillows. Button closure.',
                'price'        => 58.00,
                'cost_price'   => 22.00,
                'stock'        => 35,
                'is_featured'  => true,
                'variants'     => ['Color' => ['Natural', 'Sage', 'Blush', 'Charcoal']],
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $stationery->id,
                'name'         => 'Brass Desk Pen',
                'slug'         => 'brass-desk-pen',
                'sku'          => 'LMA-010',
                'short_description' => 'Solid brass ballpoint with weighted grip.',
                'description'  => 'Machined from solid brass that develops a rich patina over time. Refillable with standard Parker-style cartridges.',
                'price'        => 42.00,
                'cost_price'   => 16.00,
                'stock'        => 40,
                'is_new'       => true,
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $lighting->id,
                'name'         => 'Concrete Table Lamp',
                'slug'         => 'concrete-table-lamp',
                'sku'          => 'LMA-011',
                'short_description' => 'Micro-concrete base with linen shade.',
                'description'  => 'Cast in micro-concrete with subtle aggregate texture. Pairs with the included natural linen shade. Compatible with E14 LED bulbs.',
                'price'        => 98.00,
                'cost_price'   => 38.00,
                'stock'        => 15,
                'is_featured'  => true,
                'review_count' => 0,
                'review_avg'   => 0,
            ],
            [
                'category_id'  => $outdoor->id,
                'name'         => 'Recycled Glass Lantern',
                'slug'         => 'recycled-glass-lantern',
                'sku'          => 'LMA-012',
                'short_description' => 'Hand-blown recycled glass lantern for outdoor use.',
                'description'  => 'Each lantern is slightly unique due to the hand-blowing process. Weather-resistant iron frame. Fits standard pillar candles.',
                'price'        => 76.00,
                'cost_price'   => 29.00,
                'stock'        => 20,
                'is_on_sale'   => true,
                'sale_price'   => 62.00,
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

        // Order 1 — Emma, with coupon
        $order1 = Order::create([
            'order_number'       => 'ORD-LMA00001',
            'customer_id'        => $customer1->id,
            'customer_name'      => 'Emma Clarke',
            'customer_email'     => 'emma@example.com',
            'customer_phone'     => '+1 555 100 2000',
            'shipping_address'   => '14 Maple Street',
            'shipping_city'      => 'Brooklyn',
            'shipping_state'     => 'NY',
            'shipping_postal_code' => '11201',
            'shipping_country'   => 'USA',
            'subtotal'           => 121.00,
            'shipping_cost'      => 0.00,
            'coupon_id'          => $welcomeCoupon->id,
            'coupon_code'        => 'WELCOME10',
            'coupon_discount'    => 12.10,
            'discount'           => 12.10,
            'total'              => 108.90,
            'cost_total'         => 45.00,
            'status'             => 'delivered',
            'payment_status'     => 'paid',
            'payment_method'     => 'card',
        ]);
        OrderItem::create([
            'order_id'      => $order1->id,
            'product_id'    => $productModels[0]->id,
            'product_name'  => 'Linen Throw Blanket',
            'product_price' => 89.00,
            'product_cost'  => 34.00,
            'quantity'      => 1,
            'variant'       => ['Color' => 'Natural'],
            'line_total'    => 89.00,
            'line_cost'     => 34.00,
            'line_profit'   => 55.00,
        ]);
        OrderItem::create([
            'order_id'      => $order1->id,
            'product_id'    => $productModels[1]->id,
            'product_name'  => 'Matte Ceramic Mug',
            'product_price' => 32.00,
            'product_cost'  => 11.00,
            'quantity'      => 1,
            'variant'       => ['Color' => 'Clay'],
            'line_total'    => 32.00,
            'line_cost'     => 11.00,
            'line_profit'   => 21.00,
        ]);

        // Order 2 — James
        $order2 = Order::create([
            'order_number'     => 'ORD-LMA00002',
            'customer_id'      => $customer2->id,
            'customer_name'    => 'James Park',
            'customer_email'   => 'james@example.com',
            'shipping_address' => '88 Oak Avenue',
            'shipping_city'    => 'Manhattan',
            'subtotal'         => 64.00,
            'shipping_cost'    => 9.00,
            'discount'         => 0,
            'total'            => 73.00,
            'cost_total'       => 24.00,
            'status'           => 'shipped',
            'payment_status'   => 'paid',
            'payment_method'   => 'card',
        ]);
        OrderItem::create([
            'order_id'      => $order2->id,
            'product_id'    => $productModels[5]->id,
            'product_name'  => 'Olive Wood Serving Board',
            'product_price' => 64.00,
            'product_cost'  => 24.00,
            'quantity'      => 1,
            'line_total'    => 64.00,
            'line_cost'     => 24.00,
            'line_profit'   => 40.00,
        ]);

        // Order 3 — guest
        $order3 = Order::create([
            'order_number'     => 'ORD-LMA00003',
            'customer_id'      => null,
            'customer_name'    => 'Sophie Renard',
            'customer_email'   => 'sophie@example.com',
            'shipping_address' => '3 Rue de Rivoli',
            'shipping_city'    => 'Paris',
            'subtotal'         => 56.00,
            'shipping_cost'    => 9.00,
            'discount'         => 0,
            'total'            => 65.00,
            'cost_total'       => 20.00,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
        ]);
        OrderItem::create([
            'order_id'      => $order3->id,
            'product_id'    => $productModels[2]->id,
            'product_name'  => 'Beeswax Pillar Candle',
            'product_price' => 28.00,
            'product_cost'  => 9.00,
            'quantity'      => 2,
            'variant'       => ['Size' => 'Medium'],
            'line_total'    => 56.00,
            'line_cost'     => 18.00,
            'line_profit'   => 38.00,
        ]);

        // ── Demo Reviews (pending admin approval) ─────────────────
        Review::create([
            'product_id'   => $productModels[0]->id,  // Linen Throw
            'customer_id'  => $customer1->id,
            'order_id'     => $order1->id,
            'author_name'  => 'Emma Clarke',
            'author_email' => 'emma@example.com',
            'rating'       => 5,
            'title'        => 'Exactly what I wanted',
            'body'         => 'This blanket is incredibly soft and the natural colour is even more beautiful in person. It\'s become a permanent fixture on my reading chair.',
            'status'      => 'approved',
            'approved_at' => now()->subDays(2),
        ]);

        Review::create([
            'product_id'   => $productModels[1]->id,  // Matte Ceramic Mug
            'customer_id'  => $customer1->id,
            'order_id'     => $order1->id,
            'author_name'  => 'Emma Clarke',
            'author_email' => 'emma@example.com',
            'rating'       => 4,
            'title'        => 'Great mug, slight imperfection',
            'body'         => 'Love the weight and feel of this mug. Mine had a tiny air bubble in the glaze — adds to the handmade charm honestly. Would still buy again.',
            'status'      => 'approved',
            'approved_at' => now()->subDays(2),
        ]);

        Review::create([
            'product_id'   => $productModels[5]->id,  // Olive Wood Board
            'customer_id'  => $customer2->id,
            'order_id'     => $order2->id,
            'author_name'  => 'James Park',
            'author_email' => 'james@example.com',
            'rating'       => 5,
            'title'        => 'Stunning piece',
            'body'         => 'The grain on my board is incredible. I\'ve used it as a cheese board at three dinner parties and people always ask where it\'s from.',
            'status'      => 'pending', // pending approval
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
