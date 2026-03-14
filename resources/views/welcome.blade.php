<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Welcome Page</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

</head>

<body
    class="bg-linear-[35deg,#243059_25%,#4d4677_70%,#775d94_90%,#a475af]  text-[#a9aabc] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col text-[#ebeeff]">
    <header class="w-full lg:max-w-5xl max-w-[335px] text-lg mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] bg-linear-[#243059_5%,#775d94_100%] border border-transparent hover:border-[#a9aabc] dark:hover:border-[#3E3E3A] rounded-sm leading-normal">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] bg-linear-[#243059_5%,#775d94_100%] border border-transparent hover:border-[#a9aabc] dark:hover:border-[#3E3E3A] rounded-sm leading-normal">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] bg-linear-[#243059_5%,#775d94_100%]  hover:border-[#a9aabc] border border-transparent dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm leading-normal">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>
    <div
        class="flex dark:bg-[#1D0002] items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex flex-col-reverse lg:max-w-750px lg:flex-row">
            <div class="flex-1 p-8 place-content-center">
                <h1
                    class="text-6xl md:text-5xl max-sm:text-4xl text-[#d1a617] text-shadow-lg/30 text-shadow-green-800 font-bold">
                    Welcome Aboard!</h1>
                <p class="text-[#d1a617] text-shadow-lg/30 text-shadow-green-800 mb-6">Let's start Chatting!</p>

                <div class="mt-8 p-4 border border-[#d1a617]/30 bg-black/20 rounded-lg backdrop-blur-sm max-w-xs">
                    <p class="text-xs uppercase tracking-widest text-[#d1a617] mb-2 font-semibold">Test Account</p>
                    <div class="flex flex-col gap-1">
                        <p class="text-sm">Email: <span class="text-[#d1a617]">test@example.com</span></p>
                        <p class="text-sm">Password: <span class="text-[#d1a617]">testtest123</span></p>
                    </div>
                </div>
            </div>
            <div
                class="flex dark:bg-[#1D0002] place-content-center  relative lg:-ms-px -mb-px lg:mb-0 aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                <object data="your.svg" type="image/svg+xml" class="sm:max-w-[450px] max-sm:max-w-[300px]">
                    <img src="/mail-icon.svg" />
                </object>
                <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none"></div>

            </div>
        </main>
    </div>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>

</html>