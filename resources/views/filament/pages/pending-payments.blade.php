<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">🚨 รายการค้างชำระทั้งหมด</h2>
            </div>
            <p class="text-gray-600 mb-6">
                รายการตั๋วประกันที่ยังไม่ได้รับการชำระเงิน หรือชำระไม่ครบจำนวน
            </p>
        </div>
        
        {{ $this->table }}
    </div>
</x-filament-panels::page>
