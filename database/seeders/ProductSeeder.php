<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_IN');

        //Sample Products
        $sampleProducts = [
            'Colgate Toothpaste MaxFresh',
            'Parle G Biscuits Family Pack',
            'Tata Salt Iodized',
            'Amul Gold Milk Pouch 1 Litre',
            'Surf Excel Detergent Powder 1KG',
            'Maggi 2-Minute Noodles Masala',
            'Britannia Good Day Butter Cookies',
            'Red Label Tea Powder 500g',
            'Godrej Cinthol Soap Lime',
            'Dove Shampoo Damage Repair',
            'Liril Lemon Soap 125g',
            'Ariel Matic Liquid Detergent',
            'Nivea Soft Moisturising Cream 100ml',
            'Saffola Gold Edible Oil 5 Litre',
            'Lay\'s Potato Chips American Style',
            'Haldiram\'s Bhujia Sev 400g',
            'Kellogg\'s Corn Flakes Original',
            'Clinic Plus Shampoo 650ml',
            'TRESemmé Keratin Smooth Conditioner',
            'Dabur Chyawanprash 500g',
            'Himalaya Baby Powder 400g',
            'Vim Dishwash Bar',
            'Lux Soap International',
            'Patanjali Honey 500g',
            'Fortune Besan 1KG',
            'MDH Kitchen King Masala',
            'Lifebuoy Hand Sanitizer 100ml',
            'Close-up Toothpaste Everfresh',
            'Sunfeast Dark Fantasy Choco Fills',
            'Whisper Ultra Clean Pads',
            'Brooke Bond Taj Mahal Tea 1KG',
            'Amul Butter 500g',
            'Kwality Walls Cornetto Ice Cream',
            'Cadbury Dairy Milk Chocolate',
            'Bru Instant Coffee 200g',
            'Everest Garam Masala 100g',
            'Kissan Fresh Tomato Ketchup',
            'Ponds White Beauty Face Wash',
            'Dettol Antiseptic Liquid 550ml',
            'Rin Detergent Bar',
            'Head & Shoulders Anti-Dandruff Shampoo',
            'Colgate Salt Active Toothpaste',
            'Santoor Sandal Soap 125g',
            'Bingo Mad Angles Masala',
            'Yippee Noodles Tricolor',
            'India Gate Basmati Rice 5KG',
            'Aashirvaad Atta Whole Wheat 10KG',
            'Pepsodent Germicheck Toothpaste',
            'Parachute Coconut Oil 500ml',
            'Nescafe Classic Coffee Powder',
            'Patanjali Ghee 500ml',
            'Horlicks Classic Malt 1KG',
            'Boost Health Drink 500g',
            'Harpic Toilet Cleaner Lemon',
            'Vaseline Healthy White Lotion',
            'Fair & Lovely Advanced Multi-Vitamin',
            'Pantene Pro-V Hair Fall Control',
            'Garnier Men Face Wash',
            'Kurkure Masala Munch',
            'Parle Monaco Biscuits',
            'Glucon-D Glucose Powder Orange',
        ];

        //Tax slabs
        $gstRates = [5, 12, 18, 28];

        foreach ($sampleProducts as $index => $productName) {
            $initials = preg_replace('/[^A-Z]/',
                '',
                strtoupper(preg_replace('/\s+/',
                '',
                ucwords(strtolower($productName)))));
            $prefix = substr($initials, 0, 3);
            $sequentialNumber = $index + 1;
            $paddedNumber = str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
            $productCode = $prefix . $paddedNumber;

            $price = $faker->randomFloat(2, 50, 500);
            $taxPercentage = $faker->randomElement($gstRates);
            $availableStock = $faker->randomFloat(3, 50, 500);

            DB::table('products')->insert([
                'name' => $productName,
                'product_code' => $productCode,
                'price' => $price,
                'tax_percentage' => $taxPercentage,
                'available_stock' => $availableStock,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
