@if($filePath && Storage::disk('public')->exists($filePath))
    <div class="flex items-center space-x-2 p-3 bg-green-50 border border-green-200 rounded-lg">
        <svg class="h-5 w-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path d="M4 3a2 2 0 00-2 2v1.586l8 8 8-8V5a2 2 0 00-2-2H4zm16 4.414L12.586 15H16a2 2 0 002-2V7.414zM2 9.414V15a2 2 0 002 2h3.586L2 9.414z"/>
        </svg>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-green-800">{{ $fileName }}</p>
            <p class="text-xs text-green-600">
                ขนาดไฟล์: {{ number_format(Storage::disk('public')->size($filePath) / 1024, 2) }} KB
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ asset('storage/' . $filePath) }}" 
               class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700"
               download>
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                ดาวน์โหลด
            </a>
            <a href="{{ asset('storage/' . $filePath) }}" 
               class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700"
               target="_blank">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                ดู
            </a>
        </div>
    </div>
@else
    <div class="flex items-center space-x-2 p-3 bg-gray-50 border border-gray-200 rounded-lg">
        <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a3 3 0 003 3h2a3 3 0 003-3V3a2 2 0 012 2v6.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L16 11.586V5a2 2 0 00-2-2h-4a2 2 0 00-2 2z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm text-gray-500">ไม่มีไฟล์แนบ</p>
    </div>
@endif
