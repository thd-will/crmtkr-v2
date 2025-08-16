<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไม่พบข้อมูลคำขอประกันภัย</title>
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
                <div class="mx-auto h-24 w-24 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">ไม่พบข้อมูลคำขอประกันภัย</h2>
                <p class="mt-2 text-sm text-gray-600">
                    ไม่พบข้อมูลคำขอประกันภัยหมายเลข <span class="font-medium text-gray-900">{{ $ticket_number }}</span>
                </p>
                <p class="mt-2 text-sm text-gray-600">
                    กรุณาตรวจสอบหมายเลขคำขอหรือติดต่อบริษัทฯ เพื่อขอข้อมูลเพิ่มเติม
                </p>
            </div>

            <div class="mt-8 space-y-4">
                <a href="{{ route('public.ticket.access-form') }}" 
                   class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    ค้นหาด้วยรหัสเข้าถึง
                </a>
                
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        ติดต่อบริษัทฯ: 
                        <a href="tel:02-123-4567" class="text-blue-600 hover:text-blue-500">02-123-4567</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
