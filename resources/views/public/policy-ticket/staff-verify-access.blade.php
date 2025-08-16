<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าถึงฟอร์มเจ้าหน้าที่ - {{ $ticket->ticket_number }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                    เข้าถึงฟอร์มเจ้าหน้าที่
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    หมายเลขคำขอ: <span class="font-semibold text-green-600">{{ $ticket->ticket_number }}</span>
                </p>
                <p class="mt-1 text-center text-sm text-gray-600">
                    กรุณากรอกรหัสเข้าถึงเพื่อดำเนินการในฐานะเจ้าหน้าที่
                </p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md">
                <form method="POST" action="{{ route('public.ticket.staff-verify', $ticket->ticket_number) }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="access_code" class="block text-sm font-medium text-gray-700 mb-2">
                            รหัสเข้าถึงเจ้าหน้าที่
                        </label>
                        <input id="access_code" 
                               name="access_code" 
                               type="text" 
                               required 
                               placeholder="กรอกรหัสเข้าถึง 6 หลักขึ้นไป"
                               value="{{ old('access_code') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-lg font-mono tracking-widest focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('access_code') border-red-300 @enderror">
                        
                        @error('access_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        เข้าสู่ฟอร์มเจ้าหน้าที่
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        รหัสเข้าถึงสำหรับเจ้าหน้าที่เท่านั้น<br>
                        หากไม่มีรหัส กรุณาติดต่อหัวหน้างาน
                    </p>
                </div>
            </div>

            <!-- ข้อมูลพื้นฐาน -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    ข้อมูลเบื้องต้น
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">ลูกค้า:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ticket->customer->name ?? 'ไม่ระบุ' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">หมายเลขคำขอ:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">สถานะ:</span>
                        <span class="text-sm font-medium 
                            @if($ticket->status == 'pending') text-yellow-600
                            @elseif($ticket->status == 'approved') text-green-600
                            @elseif($ticket->status == 'rejected') text-red-600
                            @else text-gray-600
                            @endif">
                            {{ $ticket->status == 'pending' ? 'รอดำเนินการ' : 
                               ($ticket->status == 'approved' ? 'อนุมัติแล้ว' : 
                               ($ticket->status == 'rejected' ? 'ถูกปฏิเสธ' : $ticket->status)) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">วันที่สร้าง:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($ticket->staff_name)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">เจ้าหน้าที่ล่าสุด:</span>
                        <span class="text-sm font-medium text-green-600">{{ $ticket->staff_name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="text-center space-y-2">
                <a href="{{ route('public.ticket.check', $ticket->ticket_number) }}" 
                   class="text-sm text-blue-600 hover:text-blue-500">
                    หน้าตรวจสอบสำหรับลูกค้า
                </a>
            </div>
        </div>
    </div>
</body>
</html>
