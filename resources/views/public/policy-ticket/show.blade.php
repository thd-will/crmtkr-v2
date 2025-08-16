<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลคำขอประกันภัย #{{ $ticket->ticket_number }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('public.ticket.access-form') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    ← กลับไปหน้าตรวจสอบ
                </a>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Header -->
                <div class="bg-blue-600 text-white p-6">
                    <h1 class="text-2xl font-bold">ข้อมูลคำขอประกันภัย</h1>
                    <p class="mt-1 text-blue-100">หมายเลข: {{ $ticket->ticket_number }}</p>
                </div>

                <!-- Status Badges -->
                <div class="p-6 bg-gray-50 border-b">
                    <div class="flex flex-wrap gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($ticket->status === 'completed') bg-green-100 text-green-800
                            @elseif($ticket->status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($ticket->status === 'submitted') bg-blue-100 text-blue-800
                            @elseif($ticket->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            สถานะ: 
                            @switch($ticket->status)
                                @case('draft') ร่าง @break
                                @case('submitted') ส่งแล้ว @break
                                @case('processing') กำลังดำเนินการ @break
                                @case('completed') เสร็จสิ้น @break
                                @case('cancelled') ยกเลิก @break
                                @default {{ $ticket->status }}
                            @endswitch
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($ticket->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($ticket->payment_status === 'partial') bg-yellow-100 text-yellow-800
                            @elseif($ticket->payment_status === 'unpaid') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            การชำระ: 
                            @switch($ticket->payment_status)
                                @case('unpaid') ยังไม่ชำระ @break
                                @case('partial') ชำระบางส่วน @break
                                @case('paid') ชำระแล้ว @break
                                @case('refunded') คืนเงินแล้ว @break
                                @default {{ $ticket->payment_status }}
                            @endswitch
                        </span>
                    </div>
                </div>

                <!-- Details -->
                <div class="p-6 space-y-6">
                    <!-- Customer Info -->
                    @if($ticket->customer)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">ข้อมูลลูกค้า</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">ชื่อ:</span>
                                <span class="ml-2">{{ $ticket->customer->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">เบอร์โทร:</span>
                                <span class="ml-2">{{ $ticket->customer->phone }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Insurance Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">รายละเอียดประกัน</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">ประเภท:</span>
                                <span class="ml-2">{{ $ticket->insurance_type }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">ระยะเวลา:</span>
                                <span class="ml-2">{{ $ticket->duration }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">จำนวนคน:</span>
                                <span class="ml-2">{{ number_format($ticket->person_count) }} คน</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">จำนวนกรมธรรม์:</span>
                                <span class="ml-2">{{ number_format($ticket->policy_count) }} ฉบับ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">ราคา</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                <div class="flex justify-between">
                                    <span>ราคาต่อคน:</span>
                                    <span>{{ number_format($ticket->base_price_per_person, 2) }} บาท</span>
                                </div>
                                @if($ticket->discount_per_person > 0)
                                <div class="flex justify-between text-red-600">
                                    <span>ส่วนลดต่อคน:</span>
                                    <span>-{{ number_format($ticket->discount_per_person, 2) }} บาท</span>
                                </div>
                                @endif
                                <div class="flex justify-between font-semibold text-lg border-t pt-2">
                                    <span>รวมทั้งสิ้น:</span>
                                    <span>{{ number_format($ticket->total_amount, 2) }} บาท</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    @if($ticket->payments->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">ประวัติการชำระเงิน</h3>
                        <div class="space-y-2">
                            @foreach($ticket->payments as $payment)
                            <div class="bg-gray-50 rounded p-3 flex justify-between items-center text-sm">
                                <div>
                                    <div class="font-medium">{{ number_format($payment->amount, 2) }} บาท</div>
                                    <div class="text-gray-600">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                                    {{ $payment->status }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($ticket->tipaya_notes)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">หมายเหตุ</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">{{ $ticket->tipaya_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">ข้อมูลเวลา</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">วันที่สร้าง:</span>
                                <span class="ml-2">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">อัปเดตล่าสุด:</span>
                                <span class="ml-2">{{ $ticket->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($ticket->submitted_at)
                            <div>
                                <span class="font-medium">วันที่ส่ง:</span>
                                <span class="ml-2">{{ $ticket->submitted_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                            @if($ticket->completed_at)
                            <div>
                                <span class="font-medium">วันที่เสร็จ:</span>
                                <span class="ml-2">{{ $ticket->completed_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 text-center text-sm text-gray-600">
                    <p>หากมีคำถามโปรดติดต่อ: 
                        <a href="tel:02-XXX-XXXX" class="text-blue-600 hover:text-blue-800">02-XXX-XXXX</a>
                    </p>
                </div>
            </div>

            <!-- Refresh Button -->
            <div class="mt-4 text-center">
                <button 
                    onclick="location.reload()" 
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 text-sm"
                >
                    รีเฟรชข้อมูล
                </button>
            </div>
        </div>
    </div>
</body>
</html>
