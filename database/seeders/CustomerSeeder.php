<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('th_TH'); // ใช้ Faker ภาษาไทย

        $customers = [
            // ลูกค้าใหญ่ - ส่วนลดเยอะ
            [
                'name' => 'บริษัท เอบีซี จำกัด',
                'phone' => '02-123-4567',
                'line_id' => 'abc_company',
                'purchase_type' => 'MOU',
                'contact_channels' => ['phone', 'line'],
                'contact_from_customer' => 'ลูกค้าติดต่อผ่าน Line เพื่อสอบถามราคาประกันสำหรับพนักงาน 50 คน',
                'current_credit' => 150000.00,
                'days_missing' => 0,
                'is_active' => true,
                'discounts' => [
                    'MOU' => [
                        '3_months' => 80,   // ส่วนลด 80 บาท/คน
                        '6_months' => 150,
                        '12_months' => 280,
                        '15_months' => 350
                    ],
                    'มติ24' => [
                        '3_months' => 70,
                        '6_months' => 130,
                        '12_months' => 250,
                        '15_months' => 320
                    ]
                ]
            ],

            // ลูกค้าปานกลาง
            [
                'name' => 'ห้างหุ้นส่วน สมชาย',
                'phone' => '089-123-4567',
                'line_id' => 'somchai_shop',
                'purchase_type' => 'มติ24',
                'contact_channels' => ['phone', 'line', 'email'],
                'contact_from_customer' => 'โทรสอบถามประกันสำหรับลูกจ้าง 20 คน ต้องการราคาดี',
                'current_credit' => 80000.00,
                'days_missing' => 2,
                'is_active' => true,
                'discounts' => [
                    'MOU' => [
                        '3_months' => 50,
                        '6_months' => 90,
                        '12_months' => 170,
                        '15_months' => 220
                    ],
                    'มติ24' => [
                        '3_months' => 45,
                        '6_months' => 85,
                        '12_months' => 160,
                        '15_months' => 210
                    ]
                ]
            ],

            // ลูกค้ารายย่อย
            [
                'name' => 'นายประยุทธ์ ใจดี',
                'phone' => '086-789-0123',
                'line_id' => 'prayut123',
                'purchase_type' => 'MOU',
                'contact_channels' => ['phone'],
                'contact_from_customer' => 'ต้องการซื้อประกันสำหรับคนงาน 5 คน',
                'current_credit' => 25000.00,
                'days_missing' => 0,
                'is_active' => true,
                'discounts' => [
                    'MOU' => [
                        '3_months' => 30,
                        '6_months' => 50,
                        '12_months' => 100,
                        '15_months' => 130
                    ],
                    'มติ24' => [
                        '3_months' => 25,
                        '6_months' => 45,
                        '12_months' => 90,
                        '15_months' => 120
                    ]
                ]
            ],

            [
                'name' => 'บริษัท ก่อสร้างไทย จำกัด',
                'phone' => '02-555-6789',
                'line_id' => 'construction_thai',
                'purchase_type' => 'MOU',
                'contact_channels' => ['phone', 'line'],
                'contact_from_customer' => 'บริษัทก่อสร้าง มีคนงานต่างด้าวจำนวนมาก ต้องการประกันประจำ',
                'current_credit' => 200000.00,
                'days_missing' => 1,
                'is_active' => true,
                'discounts' => [
                    'MOU' => [
                        '3_months' => 100,
                        '6_months' => 180,
                        '12_months' => 320,
                        '15_months' => 400
                    ],
                    'มติ24' => [
                        '3_months' => 90,
                        '6_months' => 160,
                        '12_months' => 300,
                        '15_months' => 380
                    ]
                ]
            ],

            [
                'name' => 'นางสาวมานี เจริญสุข',
                'phone' => '081-234-5678',
                'line_id' => 'manee_happy',
                'purchase_type' => 'มติ24',
                'contact_channels' => ['line'],
                'contact_from_customer' => 'เป็นนายหน้า ต้องการซื้อประกันให้ลูกค้า',
                'current_credit' => 45000.00,
                'days_missing' => 0,
                'is_active' => true,
                'discounts' => [
                    'MOU' => [
                        '3_months' => 40,
                        '6_months' => 70,
                        '12_months' => 130,
                        '15_months' => 170
                    ],
                    'มติ24' => [
                        '3_months' => 35,
                        '6_months' => 65,
                        '12_months' => 125,
                        '15_months' => 160
                    ]
                ]
            ],

            [
                'name' => 'โรงงานผลิตเสื้อผ้า เอเชีย',
                'phone' => '02-999-8888',
                'line_id' => 'asia_textile',
                'purchase_type' => 'MOU',
                'contact_channels' => ['phone', 'email'],
                'contact_from_customer' => 'โรงงานผลิตเสื้อผ้า มีแรงงานต่างด้าวมากมาย',
                'current_credit' => 180000.00,
                'days_missing' => 3,
                'is_active' => true,
                'discounts' => [
                    'MOU' => [
                        '3_months' => 90,
                        '6_months' => 160,
                        '12_months' => 300,
                        '15_months' => 380
                    ],
                    'มติ24' => [
                        '3_months' => 80,
                        '6_months' => 150,
                        '12_months' => 280,
                        '15_months' => 360
                    ]
                ]
            ]
        ];

        foreach ($customers as $customerData) {
            // Convert arrays to JSON for database storage
            $customerData['contact_channels'] = json_encode($customerData['contact_channels']);
            $customerData['discounts'] = json_encode($customerData['discounts']);
            Customer::create($customerData);
        }

        // สร้างลูกค้าเพิ่มด้วย Faker
        for ($i = 1; $i <= 10; $i++) {
            $purchaseType = $faker->randomElement(['MOU', 'มติ24']);
            $contactChannels = $faker->randomElements(['phone', 'line', 'email'], $faker->numberBetween(1, 3));
            
            // ส่วนลดแบบสุ่ม
            $baseMouDiscounts = [
                '3_months' => $faker->numberBetween(20, 80),
                '6_months' => $faker->numberBetween(40, 150),
                '12_months' => $faker->numberBetween(80, 300),
                '15_months' => $faker->numberBetween(100, 400)
            ];
            
            $baseMoti24Discounts = [
                '3_months' => $baseMouDiscounts['3_months'] - 5,
                '6_months' => $baseMouDiscounts['6_months'] - 10,
                '12_months' => $baseMouDiscounts['12_months'] - 20,
                '15_months' => $baseMouDiscounts['15_months'] - 30
            ];

            Customer::create([
                'name' => $this->generateThaiCompanyName($faker),
                'phone' => $faker->phoneNumber(),
                'line_id' => $faker->userName(),
                'purchase_type' => $purchaseType,
                'contact_channels' => json_encode($contactChannels),
                'contact_from_customer' => 'ลูกค้าติดต่อมาสอบถามเรื่องประกันแรงงานต่างด้าว',
                'current_credit' => $faker->randomFloat(2, 10000, 100000),
                'days_missing' => $faker->numberBetween(0, 5),
                'is_active' => $faker->boolean(90), // 90% จะ active
                'discounts' => json_encode([
                    'MOU' => $baseMouDiscounts,
                    'มติ24' => $baseMoti24Discounts
                ])
            ]);
        }
    }

    private function generateThaiCompanyName($faker): string
    {
        $prefixes = ['บริษัท', 'ห้างหุ้นส่วน', 'โรงงาน', 'ร้าน', 'หจก.'];
        $names = [
            'สยามสแควร์', 'ไทยแลนด์', 'เอเชีย', 'โกลด์เดน', 'ดาวดวงใจ',
            'สุขสวัสดิ์', 'เจริญกรุง', 'มั่งมี', 'รุ่งเรือง', 'เจริญพัฒนา',
            'ก้าวหน้า', 'พัฒนา', 'โพธิ์ทอง', 'ธนาคาร', 'อุตสาหกรรม'
        ];
        $suffixes = ['จำกัด', 'จำกัด (มหาชน)', 'กรุ๊ป', 'อินเตอร์เนชั่นแนล', 'เทรดดิ้ง'];
        
        $prefix = $faker->randomElement($prefixes);
        $name = $faker->randomElement($names);
        $suffix = $faker->randomElement($suffixes);
        
        return $prefix . ' ' . $name . ' ' . $suffix;
    }
}
