<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title') - TKR CRM</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Styles -->
        <style>
            html, body {
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
                color: white;
            }

            .title {
                font-size: 84px;
                margin-bottom: 30px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            }

            .code {
                font-size: 36px;
                margin-bottom: 20px;
                opacity: 0.8;
            }

            .message {
                font-size: 18px;
                opacity: 0.9;
                margin-bottom: 30px;
            }

            .logo {
                width: 80px;
                height: 80px;
                margin-bottom: 30px;
                filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
            }

            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: rgba(255,255,255,0.2);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                border: 2px solid rgba(255,255,255,0.3);
                transition: all 0.3s ease;
                font-weight: 600;
            }

            .btn:hover {
                background: rgba(255,255,255,0.3);
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <!-- TKR Logo -->
                <svg class="logo" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="16" cy="16" r="14" fill="url(#gradient)" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                    
                    <!-- TKR Letters -->
                    <text x="16" y="20" font-family="Arial, sans-serif" font-weight="bold" font-size="8" fill="white" text-anchor="middle">TKR</text>
                    
                    <!-- Gradient Definition -->
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                </svg>

                <div class="code">@yield('code')</div>
                <div class="title">@yield('title')</div>
                <div class="message">@yield('message')</div>
                
                <a href="{{ url('/') }}" class="btn">กลับหน้าหลัก</a>
            </div>
        </div>
    </body>
</html>
