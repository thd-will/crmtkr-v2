<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PolicyTicket;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;

class PolicyTicketSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $users = User::all();
        
        // สถานะต่างๆ
        $statuses = [
            'draft' => ['weight' => 2, 'files' => false],
            'submitted' => ['weight' => 3, 'files' => true], 
            'processing' => ['weight' => 2, 'files' => true],
            'completed' => ['weight' => 2, 'files' => true],
            'rejected' => ['weight' => 1, 'files' => true],
        ];
        
        $insuranceTypes = ['MOU', 'มติ24'];
        $durations = ['3_months', '6_months', '12_months', '15_months'];
        
        $notes = [
            'our_notes' => [
                'ลูกค้าต้องการเร่งด่วน เนื่องจากใกล้หมดอายุ',
                'เอกสารครบถ้วนแล้ว รอดำเนินการ',
                'มีคำถามเพิ่มเติมจากลูกค้าเรื่องความคุ้มครอง',
                'ลูกค้าร้องขอเปลี่ยนแปลงจำนวนคน',
                'กำลังรอเอกสารเพิ่มเติมจากลูกค้า',
                null
            ],
            'insurance_notes' => [
                'ทิพยรับรายการแล้ว กำลังตรวจสอบ',
                'มีข้อมูลไม่ครบถ้วน กรุณาส่งเอกสารเพิ่ม',
                'อนุมัติแล้ว กำลังจัดทำกรมธรรม์',
                'กรมธรรม์พร้อมแล้ว',
                'ไม่สามารถอนุมัติได้ เนื่องจากข้อมูลไม่ถูกต้อง',
                null
            ]
        ];

        foreach ($customers->take(12) as $customer) {
            // สร้าง 1-3 ตั๋วต่อลูกค้า
            $ticketCount = rand(1, 3);
            
            for ($i = 0; $i < $ticketCount; $i++) {
                // เลือกสถานะแบบ weighted random
                $status = $this->getWeightedRandomStatus($statuses);
                $insuranceType = $insuranceTypes[array_rand($insuranceTypes)];
                $duration = $durations[array_rand($durations)];
                $personCount = rand(1, 50);
                
                // คำนวณราคา
                $basePrice = $this->getBasePrice($insuranceType, $duration);
                $discount = $customer->getDiscountFor($insuranceType, $duration);
                $totalAmount = ($basePrice - $discount) * $personCount;
                
                $ticket = PolicyTicket::create([
                    'customer_id' => $customer->id,
                    'ticket_number' => 'TKR-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                    'access_code' => strtoupper(Str::random(8)),
                    'insurance_type' => $insuranceType,
                    'duration' => $duration,
                    'person_count' => $personCount,
                    'base_price_per_person' => $basePrice,
                    'discount_per_person' => $discount,
                    'total_amount' => $totalAmount,
                    'policy_count' => $personCount,
                    'status' => $status,
                    'our_notes' => $notes['our_notes'][array_rand($notes['our_notes'])],
                    'insurance_notes' => in_array($status, ['processing', 'completed', 'rejected']) 
                        ? $notes['insurance_notes'][array_rand($notes['insurance_notes'])] 
                        : null,
                    'created_by' => $users->random()->id,
                    'submitted_by' => in_array($status, ['submitted', 'processing', 'completed', 'rejected']) 
                        ? $users->random()->id 
                        : null,
                    'submitted_at' => in_array($status, ['submitted', 'processing', 'completed', 'rejected']) 
                        ? now()->subDays(rand(1, 30))
                        : null,
                    'processed_by' => in_array($status, ['processing', 'completed']) 
                        ? 'คุณสมชาย ทิพยประกัน'
                        : null,
                    'completed_by' => $status === 'completed' 
                        ? $users->random()->id 
                        : null,
                    'completed_at' => $status === 'completed' 
                        ? now()->subDays(rand(0, 15))
                        : null,
                    'assigned_to' => $users->random()->id,
                    'last_updated_by' => $users->random()->id,
                    'request_file_path' => $statuses[$status]['files'] 
                        ? 'policy-requests/sample-request-' . Str::random(8) . '.pdf'
                        : null,
                    'policy_file_path' => $status === 'completed' 
                        ? 'policies/sample-policy-' . Str::random(8) . '.pdf'
                        : null,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                // สร้าง Activity Logs (ถ้าต้องการ)
                \Log::info("Created PolicyTicket: {$ticket->ticket_number} for {$customer->name}");
            }
        }

        $this->command->info('สร้างตั๋วประกัน ' . PolicyTicket::count() . ' รายการเสร็จเรียบร้อย');
    }

    private function getWeightedRandomStatus(array $statuses): string
    {
        $totalWeight = array_sum(array_column($statuses, 'weight'));
        $random = rand(1, $totalWeight);
        
        foreach ($statuses as $status => $data) {
            $random -= $data['weight'];
            if ($random <= 0) {
                return $status;
            }
        }
        
        return 'draft'; // fallback
    }

    private function getBasePrice(string $insuranceType, string $duration): float
    {
        // ดึงราคาจากฐานข้อมูล Product
        $product = \App\Models\Product::where('type', $insuranceType)
            ->where('duration', $duration)
            ->where('is_active', true)
            ->first();

        return $product ? $product->base_price : 590; // fallback price
    }
}
