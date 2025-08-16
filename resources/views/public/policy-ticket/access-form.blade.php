<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบสถานะคำขอประกันภัย</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-6 text-center">
                <h1 class="text-2xl font-bold">ตรวจสอบสถานะคำขอประกันภัย</h1>
                <p class="mt-2 text-blue-100">กรุณากรอกรหัสเข้าถึงที่ได้รับ</p>
            </div>

            <!-- Form -->
            <div class="p-6">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('public.ticket.access') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="access_code" class="block text-sm font-medium text-gray-700 mb-2">
                            รหัสเข้าถึง (Access Code)
                        </label>
                        <input 
                            type="text" 
                            id="access_code" 
                            name="access_code" 
                            value="{{ old('access_code') }}"
                            placeholder="กรอกรหัสเข้าถึง 6 หลักขึ้นไป"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-center text-lg font-mono"
                            required
                            minlength="6"
                        >
                        <p class="mt-1 text-sm text-gray-500">
                            รหัสเข้าถึงจะแสดงในเอกสารที่ได้รับจากเรา
                        </p>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
                    >
                        ตรวจสอบสถานะ
                    </button>
                </form>

                <!-- Quick Check via API -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">ตรวจสอบด่วน</h3>
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            id="quick_access_code" 
                            placeholder="รหัสเข้าถึง"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                        >
                        <button 
                            onclick="quickCheck()" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 text-sm"
                        >
                            ตรวจสอบ
                        </button>
                    </div>
                    
                    <!-- Quick Results -->
                    <div id="quick-results" class="mt-3 hidden">
                        <div class="bg-gray-50 border border-gray-200 rounded p-3 text-sm">
                            <div id="quick-status"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3 text-center text-sm text-gray-600">
                <p>หากมีปัญหาโปรดติดต่อ: 
                    <a href="tel:02-XXX-XXXX" class="text-blue-600 hover:text-blue-800">02-XXX-XXXX</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        async function quickCheck() {
            const accessCode = document.getElementById('quick_access_code').value;
            const resultsDiv = document.getElementById('quick-results');
            const statusDiv = document.getElementById('quick-status');

            if (!accessCode) {
                alert('กรุณากรอกรหัสเข้าถึง');
                return;
            }

            try {
                const response = await fetch('{{ route("public.ticket.api.check-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ access_code: accessCode })
                });

                const data = await response.json();

                if (response.ok) {
                    statusDiv.innerHTML = `
                        <div class="space-y-1">
                            <div><strong>หมายเลข:</strong> ${data.ticket_number}</div>
                            <div><strong>สถานะ:</strong> ${data.status_text}</div>
                            <div><strong>การชำระ:</strong> ${data.payment_status_text}</div>
                            <div><strong>จำนวนเงิน:</strong> ${data.total_amount} บาท</div>
                            <div><strong>อัปเดต:</strong> ${data.updated_at}</div>
                        </div>
                    `;
                    resultsDiv.classList.remove('hidden');
                } else {
                    statusDiv.innerHTML = `<div class="text-red-600">${data.error}</div>`;
                    resultsDiv.classList.remove('hidden');
                }
            } catch (error) {
                statusDiv.innerHTML = `<div class="text-red-600">เกิดข้อผิดพลาดในการตรวจสอบ</div>`;
                resultsDiv.classList.remove('hidden');
            }
        }

        // Allow Enter key for quick check
        document.getElementById('quick_access_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                quickCheck();
            }
        });
    </script>
</body>
</html>
