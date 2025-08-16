<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบข้อมูลคำขอประกันภัย #{{ $ticket->ticket_number }}</title>
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
                <div class="mx-auto h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                    ตรวจสอบข้อมูลคำขอประกันภัย
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    หมายเลขคำขอ: <span class="font-semibold text-blue-600">{{ $ticket->ticket_number }}</span>
                </p>
                <p class="mt-1 text-center text-sm text-gray-600">
                    กรุณากรอกรหัสเข้าถึงเพื่อดูข้อมูลรายละเอียด
                </p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md">
                <form method="POST" action="{{ route('public.ticket.verify', $ticket->ticket_number) }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="access_code" class="block text-sm font-medium text-gray-700 mb-2">
                            รหัสเข้าถึง
                        </label>
                        <input id="access_code" 
                               name="access_code" 
                               type="text" 
                               required 
                               placeholder="กรอกรหัสเข้าถึง 6 หลักขึ้นไป"
                               value="{{ old('access_code') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-lg font-mono tracking-widest focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('access_code') border-red-300 @enderror">
                        
                        @error('access_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v-2l-4.257-4.257A6 6 0 0117 9zm-5 4v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 012-2h5a2 2 0 012 2z"></path>
                        </svg>
                        ตรวจสอบข้อมูล
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        รหัสเข้าถึงได้รับจากบริษัทฯ หรือตัวแทนขาย<br>
                        หากไม่มีรหัสเข้าถึง กรุณาติดต่อ: <a href="tel:02-123-4567" class="text-blue-600 hover:text-blue-500">02-123-4567</a>
                    </p>
                </div>
            </div>

            <!-- ข้อมูลพื้นฐาน -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลเบื้องต้น</h3>
                <div class="space-y-3">
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
                </div>
            </div>
        </div>
    </div>
</body>
</html>
