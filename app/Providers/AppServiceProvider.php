<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ตั้งค่า Carbon locale เป็นภาษาไทย
        Carbon::setLocale(config('app.locale'));
        
        // ตั้งค่า timezone เป็น Asia/Bangkok
        date_default_timezone_set(config('app.timezone'));
        
        // บังคับใช้ HTTPS ใน production environment
        if (env('FORCE_HTTPS', false) || app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // กำหนดฟอร์แมตวันที่ไทยสำหรับ Filament
        $this->configureDateFormats();
    }
    
    /**
     * กำหนดรูปแบบการแสดงวันที่เวลาแบบไทย
     */
    private function configureDateFormats(): void
    {
        // ตั้งค่าการแสดงเวลาแบบไทย
        \Illuminate\Support\Facades\Blade::directive('thaiDate', function ($expression) {
            return "<?php echo Carbon\Carbon::parse($expression)->locale('th')->translatedFormat('j F Y'); ?>";
        });
        
        \Illuminate\Support\Facades\Blade::directive('thaiDateTime', function ($expression) {
            return "<?php echo Carbon\Carbon::parse($expression)->locale('th')->translatedFormat('j F Y เวลา H:i น.'); ?>";
        });
    }
}
