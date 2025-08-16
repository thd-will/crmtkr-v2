<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ฟอร์มเจ้าหน้าที่ - {{ $ticket->ticket_number }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Thai', sans-serif; }
        .file-drop-zone {
            border: 2px dashed #e5e7eb;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-drop-zone:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .file-drop-zone.dragover {
            border-color: #3b82f6;
            background-color: #dbeafe;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">ฟอร์มเจ้าหน้าที่</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            คำขอประกันภัยหมายเลข: <span class="font-semibold text-blue-600">{{ $ticket->ticket_number }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($ticket->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($ticket->status == 'approved') bg-green-100 text-green-800
                            @elseif($ticket->status == 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $ticket->status == 'pending' ? 'รอดำเนินการ' : 
                               ($ticket->status == 'approved' ? 'อนุมัติแล้ว' : 
                               ($ticket->status == 'rejected' ? 'ถูกปฏิเสธ' : $ticket->status)) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลลูกค้า</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">ชื่อลูกค้า:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $ticket->customer->name ?? 'ไม่ระบุ' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">เบอร์โทร:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $ticket->customer->phone ?? 'ไม่ระบุ' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">อีเมล:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $ticket->customer->email ?? 'ไม่ระบุ' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">วันที่สร้าง:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium">บันทึกสำเร็จ!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                        <button onclick="hideSuccessMessage()" class="ml-auto text-green-500 hover:text-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Staff Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">ข้อมูลเจ้าหน้าที่และการดำเนินการ</h3>
                
                <form method="POST" action="{{ route('public.ticket.staff-update', $ticket->ticket_number) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Staff Name -->
                    <div>
                        <label for="staff_name" class="block text-sm font-medium text-gray-700 mb-2">
                            ชื่อเจ้าหน้าที่ <span class="text-red-500">*</span>
                        </label>
                        <input id="staff_name" 
                               name="staff_name" 
                               type="text" 
                               required 
                               placeholder="กรอกชื่อ-นามสกุล เจ้าหน้าที่"
                               value="{{ old('staff_name', $ticket->staff_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('staff_name') border-red-300 @enderror">
                        
                        @error('staff_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Notes -->
                    <div>
                        <label for="staff_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            หมายเหตุจากเจ้าหน้าที่
                        </label>
                        <textarea id="staff_notes" 
                                  name="staff_notes" 
                                  rows="5"
                                  placeholder="บันทึกรายละเอียดการดำเนินการ, สถานะการติดตาม, หรือหมายเหตุพิเศษ..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('staff_notes') border-red-300 @enderror">{{ old('staff_notes', $ticket->staff_notes) }}</textarea>
                        
                        <p class="mt-1 text-sm text-gray-500">สามารถพิมพ์ได้สูงสุด 2,000 ตัวอักษร</p>
                        
                        @error('staff_notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="staff_file" class="block text-sm font-medium text-gray-700 mb-2">
                            ไฟล์แนบ (ZIP เท่านั้น)
                        </label>
                        
                        <div class="file-drop-zone" id="fileDropZone" onclick="document.getElementById('staff_file').click();">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    <span class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">คลิกเพื่อเลือกไฟล์</span>
                                    หรือลากไฟล์มาวางที่นี่
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    รองรับไฟล์ ZIP ขนาดสูงสุด 100MB
                                </p>
                            </div>
                        </div>
                        
                        <input id="staff_file" 
                               name="staff_file" 
                               type="file" 
                               accept=".zip"
                               class="hidden"
                               onchange="updateFileName(this)">
                        
                        <div id="selectedFile" class="mt-2 hidden">
                            <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 00-2 2v1.586l8 8 8-8V5a2 2 0 00-2-2H4zm16 4.414L12.586 15H16a2 2 0 002-2V7.414zM2 9.414V15a2 2 0 002 2h3.586L2 9.414z"/>
                                </svg>
                                <span id="fileName" class="text-sm text-blue-800 font-medium"></span>
                                <button type="button" onclick="clearFile()" class="ml-auto text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @if($ticket->staff_file_path)
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v1.586l8 8 8-8V5a2 2 0 00-2-2H4zm16 4.414L12.586 15H16a2 2 0 002-2V7.414zM2 9.414V15a2 2 0 002 2h3.586L2 9.414z"/>
                                    </svg>
                                    <span class="text-sm text-green-800">มีไฟล์แนบเรียบร้อยแล้ว:</span>
                                    <a href="{{ asset('storage/' . $ticket->staff_file_path) }}" 
                                       class="ml-2 text-sm text-green-600 hover:text-green-800 underline"
                                       download>
                                        ดาวน์โหลดไฟล์
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                        @error('staff_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Staff Info -->
                    @if($ticket->staff_updated_at)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">ข้อมูลการอัพเดทล่าสุด</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>เจ้าหน้าที่:</strong> {{ $ticket->staff_name }}</p>
                                <p><strong>อัพเดทเมื่อ:</strong> {{ $ticket->staff_updated_at ? $ticket->staff_updated_at->format('d/m/Y H:i') : 'ไม่ระบุ' }}</p>
                                @if($ticket->staff_notes)
                                    <p><strong>หมายเหตุ:</strong></p>
                                    <p class="bg-white p-2 rounded border text-gray-800">{{ $ticket->staff_notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center pt-6 border-t">
                        <a href="{{ route('public.ticket.check', $ticket->ticket_number) }}" 
                           class="text-sm text-gray-600 hover:text-gray-800">
                            ← กลับไปหน้าข้อมูลคำขอ
                        </a>
                        
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // File drop functionality
        const fileDropZone = document.getElementById('fileDropZone');
        const fileInput = document.getElementById('staff_file');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, unhighlight, false);
        });

        fileDropZone.addEventListener('drop', handleDrop, false);

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            fileDropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            fileDropZone.classList.remove('dragover');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                updateFileName(fileInput);
            }
        }

        function updateFileName(input) {
            const selectedFileDiv = document.getElementById('selectedFile');
            const fileNameSpan = document.getElementById('fileName');
            
            if (input.files.length > 0) {
                const file = input.files[0];
                fileNameSpan.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                selectedFileDiv.classList.remove('hidden');
            } else {
                selectedFileDiv.classList.add('hidden');
            }
        }

        function clearFile() {
            document.getElementById('staff_file').value = '';
            document.getElementById('selectedFile').classList.add('hidden');
        }

        function hideSuccessMessage() {
            document.getElementById('successMessage').style.display = 'none';
        }

        // Auto hide success message after 5 seconds
        @if(session('success'))
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.opacity = '0';
                successMessage.style.transition = 'opacity 0.5s ease-out';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
        @endif
    </script>
</body>
</html>
