@extends('layouts.auth')

@section('content')
<main class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-7">
    <nav class="flex items-center justify-between gap-4" aria-label="{{ __('auth.recovery_navigation') }}">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-sm font-bold text-[#16734f]"><span class="grid h-9 w-9 place-items-center rounded-xl bg-[#16734f] text-white"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 17 8 11l4 4 5-8 4 10M4 21h16"/></svg></span> TourismCloud AI</a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-[#16734f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> {{ __('auth.home') }}</a>
    </nav>
    <div class="flex flex-1 items-center justify-center py-8 sm:py-12">
        <section class="w-full max-w-[29rem] rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_50px_rgba(16,47,46,.07)] sm:p-9" aria-labelledby="recovery-title">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#16734f] hover:underline"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> {{ __('auth.back_to_login') }}</a>
            <div class="mt-6 flex items-center gap-2 text-xs font-semibold text-slate-500" aria-label="{{ __('auth.step_one_of_two') }}"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#16734f] text-white">1</span><span>{{ __('auth.your_email') }}</span><span class="mx-1 h-px w-6 bg-slate-200"></span><span class="grid h-6 w-6 place-items-center rounded-full bg-slate-100">2</span><span>{{ __('auth.new_password') }}</span></div>
            <h1 id="recovery-title" class="mt-5 font-display text-3xl font-bold tracking-tight text-slate-900">{{ __('auth.recover_access') }}</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('auth.recovery_intro') }}</p>

            @if(session('error'))
                <div role="alert" class="mt-5 rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="recovery-email" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.email') }}</label>
                    <input id="recovery-email" type="email" name="email" autocomplete="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('auth.email_example') }}" class="tc-input h-12 w-full">
                    @error('email') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="tc-btn tc-btn-primary min-h-12 w-full justify-center">{{ __('auth.send_code') }} <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg></button>
            </form>
            <p class="mt-5 text-xs leading-5 text-slate-500">{{ __('auth.check_spam') }}</p>
        </section>
    </div>
</main>
@endsection
