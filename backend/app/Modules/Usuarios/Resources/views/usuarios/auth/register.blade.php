@extends('layouts.auth')

@section('content')
<main class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-7">
    <nav class="flex items-center justify-between gap-4" aria-label="{{ __('auth.register_navigation') }}">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-sm font-bold text-[#16734f]">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-white border border-slate-200/80 p-1.5 shadow-xs">
                <img src="{{ asset('images/logo_tourism.png') }}" alt="TourismCloud AI" class="h-full w-full object-contain">
            </span>
            <span>TourismCloud AI</span>
        </a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-[#16734f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> {{ __('auth.home') }}</a>
    </nav>

    <div class="flex flex-1 items-center justify-center py-8 sm:py-12">
        <section class="w-full max-w-[32rem] rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_50px_rgba(16,47,46,.07)] sm:p-9" aria-labelledby="register-title">
            <p class="text-xs font-bold uppercase tracking-[.14em] text-primary-500">{{ __('auth.start_journey') }}</p>
            <h1 id="register-title" class="mt-2 font-display text-3xl font-bold tracking-tight text-slate-900">{{ __('auth.create_account') }}</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('auth.register_intro') }}</p>

            @if(session('error'))
                <div role="alert" class="mt-5 rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="mt-6 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="register-nombre" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.first_name') }}</label>
                        <input id="register-nombre" name="nombre" type="text" autocomplete="given-name" value="{{ old('nombre') }}" required autofocus class="tc-input h-12 w-full">
                        @error('nombre') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="register-apellido" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.last_name') }}</label>
                        <input id="register-apellido" name="apellido" type="text" autocomplete="family-name" value="{{ old('apellido') }}" required class="tc-input h-12 w-full">
                        @error('apellido') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="register-email" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.email') }}</label>
                    <input id="register-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required placeholder="{{ __('auth.email_example') }}" class="tc-input h-12 w-full">
                    @error('email') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ show: false }">
                    <label for="register-password" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.password') }}</label>
                    <div class="relative">
                        <input id="register-password" name="password" type="password" x-bind:type="show ? 'text' : 'password'" autocomplete="new-password" required minlength="8" placeholder="{{ __('auth.min_eight_chars') }}" class="tc-input h-12 w-full" style="padding-right: 3rem;">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-xl text-slate-500 hover:text-[#16734f]" x-bind:aria-label="show ? @js(__('auth.hide_password')) : @js(__('auth.show_password'))" x-bind:aria-pressed="show">
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                            <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M9.9 5.7A10.2 10.2 0 0 1 12 5.5C18.5 5.5 22 12 22 12a18.2 18.2 0 0 1-3.4 4.2M6.2 6.2C3.5 8.1 2 12 2 12s3.5 6.5 10 6.5a9.7 9.7 0 0 0 4.1-.9"/></svg>
                        </button>
                    </div>
                    @error('password') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="register-password-confirmation" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.confirm_password') }}</label>
                    <input id="register-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8" placeholder="{{ __('auth.repeat_password') }}" class="tc-input h-12 w-full">
                    @error('password_confirmation') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="tc-btn tc-btn-primary min-h-12 w-full justify-center">{{ __('auth.create_my_account') }} <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg></button>
            </form>

            <a href="{{ route('auth.google') }}" class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#16734f] hover:bg-slate-50">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M21.8 12.2c0-.7-.1-1.4-.2-2.1H12v4h5.5a4.8 4.8 0 0 1-2 3.1v2.6h3.3c1.9-1.8 3-4.4 3-7.6ZM12 22c2.7 0 5-1 6.8-2.2l-3.3-2.6c-.9.6-2 .9-3.5.9a6 6 0 0 1-5.6-4.1H3v2.6A10 10 0 0 0 12 22ZM6.4 14a6 6 0 0 1 0-4V7.4H3a10 10 0 0 0 0 9.2L6.4 14ZM12 6c1.5 0 2.8.5 3.8 1.5l2.9-2.9A9.6 9.6 0 0 0 12 2a10 10 0 0 0-9 5.4L6.4 10A6 6 0 0 1 12 6Z"/></svg>
                {{ __('auth.register_google') }}
            </a>

            <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">{{ __('auth.have_account') }} <a href="{{ route('login') }}" class="font-semibold text-[#16734f] hover:underline">{{ __('auth.sign_in') }}</a></p>
        </section>
    </div>
</main>
@endsection
