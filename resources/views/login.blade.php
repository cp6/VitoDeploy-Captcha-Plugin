<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">

        <title>{{ config('vito-captcha.heading') }} - {{ config('app.name', 'VitoDeploy') }}</title>

        <link rel="icon" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('favicon/apple-icon.png') }}">

        <script>
            (() => {
                const saved = localStorage.getItem('appearance') || 'system';
                const dark = saved === 'dark' || (saved === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            })();
        </script>

        <style>
            :root {
                color-scheme: light;
                --background: #ffffff;
                --foreground: #18181b;
                --muted: #71717a;
                --border: #e4e4e7;
                --input: #ffffff;
                --primary: #4f46e5;
                --primary-hover: #4338ca;
                --danger: #dc2626;
                --focus: rgba(79, 70, 229, 0.22);
            }

            html.dark {
                color-scheme: dark;
                --background: #171717;
                --foreground: #f4f4f5;
                --muted: #a1a1aa;
                --border: #3f3f46;
                --input: #202020;
                --primary: #6366f1;
                --primary-hover: #818cf8;
                --danger: #f87171;
                --focus: rgba(99, 102, 241, 0.3);
            }

            * {
                box-sizing: border-box;
            }

            html,
            body {
                min-height: 100%;
            }

            body {
                margin: 0;
                background: var(--background);
                color: var(--foreground);
                font-family: "Instrument Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                font-size: 14px;
                line-height: 1.5;
                -webkit-font-smoothing: antialiased;
            }

            button,
            input {
                font: inherit;
            }

            .page {
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
                padding: 40px 24px;
            }

            .auth {
                width: 100%;
                max-width: 384px;
            }

            .header {
                margin-bottom: 32px;
                text-align: center;
            }

            .logo {
                display: inline-flex;
                width: 36px;
                height: 36px;
                margin-bottom: 16px;
                border-radius: 4px;
                overflow: hidden;
            }

            .logo img {
                width: 100%;
                height: 100%;
            }

            h1 {
                margin: 0;
                font-size: 20px;
                font-weight: 500;
                line-height: 1.4;
            }

            .description {
                margin: 8px 0 0;
                color: var(--muted);
            }

            .form {
                display: grid;
                gap: 24px;
            }

            .field {
                display: grid;
                gap: 8px;
            }

            .label-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }

            label {
                font-weight: 500;
            }

            a {
                color: var(--foreground);
                text-underline-offset: 4px;
            }

            a:hover {
                color: var(--primary);
            }

            .input {
                width: 100%;
                height: 40px;
                padding: 8px 12px;
                border: 1px solid var(--border);
                border-radius: 6px;
                outline: none;
                background: var(--input);
                color: var(--foreground);
                transition: border-color 120ms ease, box-shadow 120ms ease;
            }

            .input::placeholder {
                color: var(--muted);
            }

            .input:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px var(--focus);
            }

            .captcha-frame {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 42px;
                min-height: 58px;
                overflow: hidden;
                border: 1px solid var(--border);
                border-radius: 6px;
                background: #f4f4f5;
            }

            .captcha-image {
                display: block;
                width: 100%;
                height: 56px;
                object-fit: cover;
            }

            .refresh {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                border-left: 1px solid var(--border);
                background: var(--input);
                color: var(--muted);
                cursor: pointer;
            }

            .refresh:hover {
                color: var(--foreground);
                background: color-mix(in srgb, var(--input), var(--foreground) 5%);
            }

            .refresh:focus-visible {
                position: relative;
                outline: 2px solid var(--primary);
                outline-offset: -3px;
            }

            .refresh svg {
                width: 17px;
                height: 17px;
            }

            .error {
                margin: 0;
                color: var(--danger);
                font-size: 13px;
            }

            .remember {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .remember input {
                width: 16px;
                height: 16px;
                margin: 0;
                accent-color: var(--primary);
            }

            .submit {
                display: inline-flex;
                width: 100%;
                height: 40px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 6px;
                background: var(--primary);
                color: #ffffff;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 120ms ease;
            }

            .submit:hover {
                background: var(--primary-hover);
            }

            .submit:focus-visible {
                outline: 3px solid var(--focus);
                outline-offset: 2px;
            }

            .submit:disabled {
                cursor: wait;
                opacity: 0.7;
            }

            .footer {
                margin-top: 32px;
                color: color-mix(in srgb, var(--muted), transparent 35%);
                font-size: 12px;
                text-align: center;
            }

            .footer a {
                color: inherit;
            }

            @media (max-width: 480px) {
                .page {
                    align-items: flex-start;
                    padding-top: 32px;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
            <section class="auth" aria-labelledby="login-heading">
                <header class="header">
                    <a class="logo" href="{{ url('/') }}" aria-label="{{ config('app.name', 'VitoDeploy') }} home">
                        <img src="{{ asset('favicon/favicon-96x96.png') }}" alt="">
                    </a>
                    <h1 id="login-heading">{{ config('vito-captcha.heading') }}</h1>
                    <p class="description">{{ config('vito-captcha.description') }}</p>
                </header>

                <form class="form" method="POST" action="{{ route('login.store') }}" data-login-form>
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <input
                            class="input"
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', config('app.demo') ? 'demo@vitodeploy.com' : '') }}"
                            autocomplete="email"
                            required
                            autofocus
                        >
                        @error('email')
                            <p class="error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <div class="label-row">
                            <label for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>
                        <input
                            class="input"
                            id="password"
                            name="password"
                            type="password"
                            value="{{ config('app.demo') ? 'password' : '' }}"
                            autocomplete="current-password"
                            required
                        >
                        @error('password')
                            <p class="error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="captcha">{{ config('vito-captcha.label') }}</label>
                        <div class="captcha-frame">
                            <img
                                class="captcha-image"
                                src="{{ route('vito-captcha.image', ['refresh' => now()->timestamp]) }}"
                                alt="Security code. Refresh the image if it is difficult to read."
                                width="{{ config('vito-captcha.image.width') }}"
                                height="{{ config('vito-captcha.image.height') }}"
                                data-captcha-image
                            >
                            <button class="refresh" type="button" aria-label="Generate a new security code" title="Generate a new code" data-refresh-captcha>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"></path>
                                </svg>
                            </button>
                        </div>
                        <input
                            class="input"
                            id="captcha"
                            name="captcha"
                            type="text"
                            inputmode="text"
                            autocomplete="off"
                            autocapitalize="characters"
                            spellcheck="false"
                            placeholder="{{ config('vito-captcha.placeholder') }}"
                            aria-describedby="captcha-error"
                            required
                        >
                        @error('captcha')
                            <p class="error" id="captcha-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="remember" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                        <span>Remember me</span>
                    </label>

                    <button class="submit" type="submit" data-submit>Log in</button>
                </form>

                <footer class="footer">
                    VitoDeploy
                    @if (config('app.version'))
                        <a href="https://github.com/vitodeploy/vito/releases/tag/{{ config('app.version') }}" target="_blank" rel="noopener noreferrer">
                            {{ config('app.version') }}
                        </a>
                    @endif
                </footer>
            </section>
        </main>

        <script>
            (() => {
                const image = document.querySelector('[data-captcha-image]');
                const refresh = document.querySelector('[data-refresh-captcha]');
                const form = document.querySelector('[data-login-form]');
                const submit = document.querySelector('[data-submit]');

                refresh?.addEventListener('click', () => {
                    const url = new URL(image.src);
                    url.searchParams.set('refresh', Date.now().toString());
                    image.src = url.toString();
                    document.getElementById('captcha')?.focus();
                });

                form?.addEventListener('submit', () => {
                    submit.disabled = true;
                    submit.textContent = 'Logging in…';
                });
            })();
        </script>
    </body>
</html>
