<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register a tenant · {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light dark; --bg: #f6f7f9; --card: #fff; --text: #1b1b18; --muted: #6b6b66; --border: #dcdcd6; --accent: #f53003; --error: #c0261b; --ok: #1c7c3c; }
        @media (prefers-color-scheme: dark) { :root { --bg: #0a0a0a; --card: #161615; --text: #ededec; --muted: #a1a09a; --border: #3e3e3a; --accent: #ff4433; --error: #ff6b5e; --ok: #4ade80; } }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 16px; background: var(--bg); color: var(--text); font: 15px/1.5 system-ui, -apple-system, "Segoe UI", sans-serif; }
        main { width: 100%; max-width: 420px; background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 28px; }
        h1 { margin: 0 0 4px; font-size: 20px; }
        p { margin: 0 0 20px; color: var(--muted); }
        label { display: block; font-weight: 600; margin-bottom: 6px; }
        .field { display: flex; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
        .field:focus-within { outline: 2px solid var(--accent); outline-offset: 1px; }
        input { flex: 1; min-width: 0; border: 0; padding: 10px 12px; font: inherit; background: transparent; color: inherit; outline: none; }
        .suffix { padding: 10px 12px; color: var(--muted); border-left: 1px solid var(--border); white-space: nowrap; }
        button { margin-top: 16px; width: 100%; padding: 10px; border: 0; border-radius: 6px; background: var(--accent); color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        .error { color: var(--error); margin-top: 6px; font-size: 14px; }
        .success { border: 1px solid var(--ok); color: var(--ok); border-radius: 6px; padding: 10px 12px; margin-bottom: 20px; overflow-wrap: anywhere; }
        .success a { color: inherit; font-weight: 600; }
    </style>
</head>
<body>
<main>
    <h1>{{ config('app.name') }}</h1>
    <p>Create a new tenant. It gets its own subdomain and its own database.</p>

    @if (session('tenant_url'))
        <div class="success" role="status">
            Tenant created: <a href="{{ session('tenant_url') }}">{{ session('tenant_url') }}</a>
        </div>
    @endif

    <form method="post" action="{{ route('tenants.store') }}" novalidate>
        @csrf

        <label for="subdomain">Subdomain</label>
        <div class="field">
            <input id="subdomain" type="text" name="subdomain" value="{{ old('subdomain') }}"
                   placeholder="acme" autocomplete="off" autocapitalize="none" spellcheck="false"
                   required minlength="3" maxlength="63" pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?"
                   aria-describedby="subdomain-error">
            <span class="suffix">.{{ $centralDomain }}</span>
        </div>
        @error('subdomain')
            <div id="subdomain-error" class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Register</button>
    </form>
</main>
</body>
</html>
