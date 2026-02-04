<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    @vite('resources/css/app.css')
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
</head>

<body class="">
    <div class="h-screen w-full flex flex-col justify-center items-center bg-[#1A2238]">
        <h1 class="text-9xl font-extrabold text-white tracking-widest">@yield('code') </h1>
        <div class="bg-[#FF6A3D] px-2 text-sm rounded rotate-12 absolute">
            @yield('message')
        </div>
        <button class="mt-5">
            <a href="{{ url()->previous() }}"
                class="relative inline-block text-sm font-medium text-[#FF6A3D] group active:text-orange-500 focus:outline-hidden focus:ring-3">
                <span
                    class="absolute inset-0 transition-transform translate-x-0.5 translate-y-0.5 bg-[#FF6A3D] group-hover:translate-y-0 group-hover:translate-x-0"></span>

                <span class="relative block px-8 py-3 bg-[#1A2238] border border-current">
                    Kembali
                </span>
            </a>
        </button>
    </div>
</body>

</html>
