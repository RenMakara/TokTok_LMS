<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TokTok Login')</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @keyframes book-float {
            0%, 100% { transform: translateY(0) rotate(-1deg); }
            50% { transform: translateY(-14px) rotate(1deg); }
        }

        @keyframes page-turn {
            0%, 20% { transform: rotateY(0deg); opacity: 0.95; }
            45%, 65% { transform: rotateY(-155deg); opacity: 0.75; }
            100% { transform: rotateY(-180deg); opacity: 0; }
        }

        @keyframes page-rise {
            0% { transform: translate3d(0, 18px, 0) rotate(-8deg); opacity: 0; }
            20% { opacity: 0.85; }
            100% { transform: translate3d(38px, -118px, 0) rotate(14deg); opacity: 0; }
        }

        @keyframes glow-pulse {
            0%, 100% { opacity: 0.45; transform: scale(0.98); }
            50% { opacity: 0.75; transform: scale(1.04); }
        }

        .book-stage {
            perspective: 900px;
        }

        .book-shell {
            animation: book-float 5.5s ease-in-out infinite;
            transform-style: preserve-3d;
        }

        .book-glow {
            animation: glow-pulse 4s ease-in-out infinite;
        }

        .turning-page {
            transform-origin: left center;
            animation: page-turn 3.8s ease-in-out infinite;
        }

        .turning-page:nth-child(2) {
            animation-delay: 0.65s;
        }

        .turning-page:nth-child(3) {
            animation-delay: 1.3s;
        }

        .floating-page {
            animation: page-rise 4.6s ease-in-out infinite;
        }

        .floating-page:nth-child(2) {
            animation-delay: 1.2s;
            animation-duration: 5.2s;
        }

        .floating-page:nth-child(3) {
            animation-delay: 2.1s;
            animation-duration: 4.9s;
        }

        @media (prefers-reduced-motion: reduce) {
            .book-shell,
            .book-glow,
            .turning-page,
            .floating-page {
                animation: none;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#edf3ee] text-[#173b30]">
@include('sweetalert::alert')

<main class="relative isolate min-h-screen overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,#d9eadf_0,#edf3ee_32%,#f7faf5_100%)]"></div>
    <div class="absolute left-0 top-0 -z-10 h-full w-full opacity-[0.06]" style="background-image: linear-gradient(#0d4a36 1px, transparent 1px), linear-gradient(90deg, #0d4a36 1px, transparent 1px); background-size: 44px 44px;"></div>

    <div class="mx-auto grid min-h-screen w-full max-w-6xl grid-cols-1 items-center gap-8 px-6 py-8 lg:grid-cols-[1.05fr_0.95fr] lg:px-10">
        <section class="book-stage relative hidden min-h-[540px] items-center justify-center lg:flex" aria-hidden="true">
            <div class="book-glow absolute h-[360px] w-[360px] rounded-full bg-[#95c8a9] blur-3xl"></div>

            <div class="absolute left-[12%] top-[18%] space-y-8">
                <span class="floating-page block h-20 w-14 rounded-[6px] border border-[#c7d7cc] bg-white/80 shadow-lg"></span>
                <span class="floating-page block h-16 w-12 rounded-[6px] border border-[#c7d7cc] bg-white/70 shadow-lg"></span>
                <span class="floating-page block h-12 w-10 rounded-[6px] border border-[#c7d7cc] bg-white/60 shadow-lg"></span>
            </div>

            <div class="book-shell relative h-[330px] w-[470px]">
                <div class="absolute bottom-5 left-1/2 h-8 w-[390px] -translate-x-1/2 rounded-full bg-[#1f3f35]/20 blur-xl"></div>

                <div class="absolute left-[42px] top-[72px] h-[210px] w-[190px] -rotate-6 rounded-l-[22px] rounded-r-[8px] border border-[#b6cabb] bg-[#fffdf5] shadow-2xl">
                    <div class="absolute inset-y-6 right-4 w-px bg-[#d8ded6]"></div>
                    <div class="mx-7 mt-10 h-2 rounded-full bg-[#c6d8c8]"></div>
                    <div class="mx-7 mt-5 h-2 w-28 rounded-full bg-[#dce6db]"></div>
                    <div class="mx-7 mt-5 h-2 w-24 rounded-full bg-[#dce6db]"></div>
                </div>

                <div class="absolute right-[42px] top-[72px] h-[210px] w-[190px] rotate-6 rounded-l-[8px] rounded-r-[22px] border border-[#b6cabb] bg-[#fffdf5] shadow-2xl">
                    <div class="absolute inset-y-6 left-4 w-px bg-[#d8ded6]"></div>
                    <div class="mx-7 mt-10 h-2 rounded-full bg-[#c6d8c8]"></div>
                    <div class="mx-7 mt-5 h-2 w-28 rounded-full bg-[#dce6db]"></div>
                    <div class="mx-7 mt-5 h-2 w-24 rounded-full bg-[#dce6db]"></div>
                </div>

                <div class="turning-page absolute left-[230px] top-[78px] h-[198px] w-[172px] rounded-r-[20px] border border-[#bfd0c2] bg-white shadow-xl">
                    <div class="mx-7 mt-11 h-2 rounded-full bg-[#c9dacb]"></div>
                    <div class="mx-7 mt-5 h-2 w-24 rounded-full bg-[#e1e9df]"></div>
                    <div class="mx-7 mt-5 h-2 w-28 rounded-full bg-[#e1e9df]"></div>
                </div>
                <div class="turning-page absolute left-[230px] top-[78px] h-[198px] w-[172px] rounded-r-[20px] border border-[#bfd0c2] bg-[#fffdf8] shadow-xl"></div>
                <div class="turning-page absolute left-[230px] top-[78px] h-[198px] w-[172px] rounded-r-[20px] border border-[#bfd0c2] bg-[#f8fbf7] shadow-xl"></div>

                <div class="absolute left-1/2 top-[68px] h-[224px] w-5 -translate-x-1/2 rounded-full bg-[#0d4a36] shadow-lg"></div>
                <div class="absolute left-[52px] top-[70px] h-[220px] w-[370px] rounded-[28px] border-[12px] border-[#0d4a36] opacity-95"></div>
            </div>
        </section>

        <section class="mx-auto flex w-full max-w-[500px] flex-col items-center lg:items-start">
            <div class="mb-8 flex flex-col items-center lg:items-start">
                <div class="mb-5 flex items-center gap-4">
                    <img
                        src="{{ asset('img/logo.png') }}"
                        alt="TokTok Logo"
                        class="h-20 w-20 rounded-[22px] shadow-lg sm:h-24 sm:w-24"
                        onerror="this.src='https://placehold.co/96x96/2f5b4d/ffffff?text=LOGO';"
                    >
                    <img
                        src="{{ asset('img/stylingFont.png') }}"
                        alt="TokTok Library"
                        class="h-auto w-48 sm:w-64"
                        onerror="this.src='https://placehold.co/260x80/2f5b4d/ffffff?text=TOKTOK';"
                    >
                </div>

                <div class="book-stage relative mb-7 flex h-28 w-44 items-center justify-center lg:hidden" aria-hidden="true">
                    <div class="book-glow absolute h-24 w-36 rounded-full bg-[#95c8a9] blur-2xl"></div>
                    <div class="book-shell relative h-24 w-36">
                        <div class="absolute bottom-2 left-1/2 h-4 w-28 -translate-x-1/2 rounded-full bg-[#1f3f35]/20 blur-md"></div>
                        <div class="absolute left-1 top-5 h-14 w-16 -rotate-6 rounded-l-xl border border-[#b6cabb] bg-[#fffdf5] shadow-lg"></div>
                        <div class="absolute right-1 top-5 h-14 w-16 rotate-6 rounded-r-xl border border-[#b6cabb] bg-[#fffdf5] shadow-lg"></div>
                        <div class="turning-page absolute left-[70px] top-6 h-12 w-14 rounded-r-xl border border-[#bfd0c2] bg-white shadow"></div>
                        <div class="absolute left-1/2 top-5 h-16 w-2 -translate-x-1/2 rounded-full bg-[#0d4a36]"></div>
                    </div>
                </div>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="w-full">
                @csrf
                <div class="flex w-full flex-col gap-3 rounded-[32px] border border-white/70 bg-white/80 p-3 shadow-[0_24px_70px_rgba(22,64,49,0.16)] backdrop-blur sm:flex-row sm:rounded-full">
                    <label for="user_id" class="sr-only">លេខសមាជិក</label>
                    <input
                        type="text"
                        id="user_id"
                        name="user_id"
                        value="{{ old('user_id') }}"
                        required
                        autocomplete="username"
                        placeholder="លេខសមាជិក"
                        class="h-16 min-w-0 flex-1 rounded-full border border-[#c9d8ce] bg-white px-6 text-[18px] shadow-sm outline-none transition focus:border-[#2F5B4D] focus:ring-4 focus:ring-[#2f5b4d]/15 siemreap-regular sm:h-20 sm:text-[19px]"
                    >

                    <button
                        type="submit"
                        class="group h-16 rounded-full bg-[#2F5B4D] px-8 text-white shadow-lg shadow-[#2f5b4d]/25 transition hover:-translate-y-0.5 hover:bg-[#24493e] focus:outline-none focus:ring-4 focus:ring-[#2f5b4d]/25 sm:h-20 sm:w-[160px]"
                    >
                        <span class="inline-flex items-center justify-center gap-3 text-[23px] siemreap-regular sm:text-[26px]">
                            បន្ត
                            <i class="fa-solid fa-caret-right text-[30px] transition group-hover:translate-x-1 sm:text-[32px]"></i>
                        </span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

</body>
</html>
