<?php

namespace App\Http\Controllers;

use App\Models\PolicyTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PublicPolicyTicketController extends Controller
{
    /**
     * แสดงหน้าสำหรับกรอกรหัส ticket
     */
    public function showAccessForm(): View
    {
        return view('public.policy-ticket.access-form');
    }

    /**
     * ตรวจสอบรหัสและแสดงข้อมูล ticket
     */
    public function showTicket(Request $request, $access_code = null): View|JsonResponse
    {
        // ถ้ามาจาก URL parameter ให้ใช้ค่านั้น
        $code = $access_code ?: $request->input('access_code');
        
        // Validate code
        if (!$code || strlen($code) < 6) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'รหัสเข้าถึงไม่ถูกต้องหรือต้องมีอย่างน้อย 6 หลัก'
                ], 400);
            }
            
            return back()->withErrors([
                'access_code' => 'กรุณากรอกรหัสเข้าถึงที่ถูกต้อง (อย่างน้อย 6 หลัก)'
            ])->withInput();
        }

        $ticket = PolicyTicket::with(['customer', 'payments'])
            ->where('access_code', $code)
            ->first();

        if (!$ticket) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'ไม่พบข้อมูลคำขอประกันภัยหรือรหัสเข้าถึงไม่ถูกต้อง'
                ], 404);
            }
            
            return back()->withErrors([
                'access_code' => 'ไม่พบข้อมูลคำขอประกันภัยหรือรหัสเข้าถึงไม่ถูกต้อง'
            ])->withInput();
        }

        return view('public.policy-ticket.show', compact('ticket'));
    }

    /**
     * แสดง ticket จาก ticket number
     */
    public function showTicketByNumber($ticket_number): View|JsonResponse
    {
        $ticket = PolicyTicket::with(['customer', 'payments'])
            ->where('ticket_number', $ticket_number)
            ->first();

        if (!$ticket) {
            return response()->view('public.policy-ticket.not-found', [
                'ticket_number' => $ticket_number
            ], 404);
        }

        // แสดงฟอร์มใส่ access code ก่อน
        return view('public.policy-ticket.verify-access', compact('ticket'));
    }

    /**
     * ตรวจสอบ access code และแสดงข้อมูล ticket
     */
    public function verifyAndShowTicket(Request $request, $ticket_number): View|JsonResponse
    {
        $request->validate([
            'access_code' => 'required|string|min:6',
        ], [
            'access_code.required' => 'กรุณากรอกรหัสเข้าถึง',
            'access_code.min' => 'รหัสเข้าถึงต้องมีอย่างน้อย 6 หลัก',
        ]);

        $ticket = PolicyTicket::with(['customer', 'payments'])
            ->where('ticket_number', $ticket_number)
            ->where('access_code', $request->access_code)
            ->first();

        if (!$ticket) {
            return back()->withErrors([
                'access_code' => 'รหัสเข้าถึงไม่ถูกต้องหรือไม่ตรงกับหมายเลขคำขอประกันภัย'
            ])->withInput();
        }

        return view('public.policy-ticket.show', compact('ticket'));
    }

    /**
     * API endpoint สำหรับตรวจสอบสถานะ
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'access_code' => 'required|string',
        ]);

        $ticket = PolicyTicket::where('access_code', $request->access_code)->first();

        if (!$ticket) {
            return response()->json([
                'error' => 'ไม่พบข้อมูลตั้งตาราง'
            ], 404);
        }

        return response()->json([
            'ticket_number' => $ticket->ticket_number,
            'status' => $ticket->status,
            'status_text' => $this->getStatusText($ticket->status),
            'payment_status' => $ticket->payment_status,
            'payment_status_text' => $this->getPaymentStatusText($ticket->payment_status),
            'total_amount' => number_format($ticket->total_amount, 2),
            'paid_amount' => number_format($ticket->paid_amount ?? 0, 2),
            'created_at' => $ticket->created_at->format('d/m/Y H:i'),
            'updated_at' => $ticket->updated_at->format('d/m/Y H:i'),
        ]);
    }

    private function getStatusText(string $status): string
    {
        return match ($status) {
            'draft' => 'ร่าง',
            'submitted' => 'ส่งแล้ว',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };
    }

    private function getPaymentStatusText(string $status): string
    {
        return match ($status) {
            'unpaid' => 'ยังไม่ชำระ',
            'partial' => 'ชำระบางส่วน',
            'paid' => 'ชำระแล้ว',
            'refunded' => 'คืนเงินแล้ว',
            default => $status,
        };
    }

    /**
     * แสดงหน้าสำหรับเจ้าหน้าที่
     */
    public function showStaffForm($ticket_number): View|JsonResponse
    {
        $ticket = PolicyTicket::with(['customer', 'payments'])
            ->where('ticket_number', $ticket_number)
            ->first();

        if (!$ticket) {
            return response()->view('public.policy-ticket.not-found', [
                'ticket_number' => $ticket_number
            ], 404);
        }

        // แสดงฟอร์มใส่ access code ก่อน
        return view('public.policy-ticket.staff-verify-access', compact('ticket'));
    }

    /**
     * Redirect GET requests to staff form
     */
    public function redirectToStaffForm($ticket_number)
    {
        return redirect()->route('public.ticket.staff-form', $ticket_number);
    }

    /**
     * ตรวจสอบ access code สำหรับเจ้าหน้าที่
     */
    public function verifyStaffAccess(Request $request, $ticket_number): View|JsonResponse
    {
        $request->validate([
            'access_code' => 'required|string|min:6',
        ], [
            'access_code.required' => 'กรุณากรอกรหัสเข้าถึง',
            'access_code.min' => 'รหัสเข้าถึงต้องมีอย่างน้อย 6 หลัก',
        ]);

        $ticket = PolicyTicket::with(['customer', 'payments'])
            ->where('ticket_number', $ticket_number)
            ->where('access_code', $request->access_code)
            ->first();

        if (!$ticket) {
            return back()->withErrors([
                'access_code' => 'รหัสเข้าถึงไม่ถูกต้องหรือไม่ตรงกับหมายเลขคำขอประกันภัย'
            ])->withInput();
        }

        return view('public.policy-ticket.staff-form', compact('ticket'));
    }

    /**
     * อัพเดทข้อมูลเจ้าหน้าที่
     */
    public function updateStaffInfo(Request $request, $ticket_number)
    {
        $request->validate([
            'staff_name' => 'required|string|max:255',
            'staff_notes' => 'nullable|string|max:2000',
            'staff_file' => 'nullable|file|mimes:zip|max:102400', // 100MB = 102400KB
        ], [
            'staff_name.required' => 'กรุณากรอกชื่อเจ้าหน้าที่',
            'staff_name.max' => 'ชื่อเจ้าหน้าที่ต้องไม่เกิน 255 ตัวอักษร',
            'staff_notes.max' => 'หมายเหตุต้องไม่เกิน 2000 ตัวอักษร',
            'staff_file.mimes' => 'ไฟล์แนบต้องเป็นไฟล์ .zip เท่านั้น',
            'staff_file.max' => 'ไฟล์แนบต้องมีขนาดไม่เกิน 100MB',
        ]);

        $ticket = PolicyTicket::where('ticket_number', $ticket_number)->first();

        if (!$ticket) {
            return back()->withErrors(['error' => 'ไม่พบข้อมูลคำขอประกันภัย'])->withInput();
        }

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('staff_file')) {
            $file = $request->file('staff_file');
            $fileName = $ticket_number . '_staff_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('staff-files', $fileName, 'public');
        }

        // Update ticket with staff information
        $ticket->update([
            'staff_name' => $request->staff_name,
            'staff_notes' => $request->staff_notes,
            'staff_file_path' => $filePath,
            'staff_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'บันทึกข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว ระบบได้อัพเดทข้อมูลเมื่อ ' . now()->format('d/m/Y H:i:s'));
    }

    /**
     * Staff URL with access code - redirect to staff form with pre-filled access code
     */
    public function staffVerifyWithCode($ticketNumber, $accessCode)
    {
        // ตรวจสอบว่า ticket และ access code ตรงกัน
        $ticket = PolicyTicket::where('ticket_number', $ticketNumber)
            ->where('access_code', $accessCode)
            ->first();

        if (!$ticket) {
            return view('public.policy-ticket.error', [
                'message' => 'ไม่พบข้อมูลคำขอประกันภัยหรือรหัสเข้าถึงไม่ถูกต้อง'
            ]);
        }

        // Redirect ไปยังหน้า staff form พร้อมกับ access code
        return redirect()->route('public.ticket.staff-form', $ticketNumber)
            ->with('pre_filled_access_code', $accessCode)
            ->with('success', 'ยืนยันรหัสเข้าถึงเรียบร้อย กรุณากรอกข้อมูลเจ้าหน้าที่');
    }
}
