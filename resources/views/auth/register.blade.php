<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Create Account - {{ env('APP_NAME','LaporAE') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #0b63ff;
            --blue-dark: #0650d6;
            --muted: #6b7280;
            --text-color: #020617;
            --bg-light: #f3f7ff;
            --border-color: #e6edf8;
            --input-bg: #fff; 
            --danger: #dc2626;
            --success: #22c55e;
            --placeholder-color: #9ca3af; 
        }

        * {
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-color);
        }

        .container-panel {
            width: 100%;
            max-width: 1100px; 
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr; 
            box-shadow: 0 20px 50px rgba(2, 6, 23, 0.08);
            background: #fff;
        }

        .left-section {
            padding: 48px;
            background: #fff; 
            color: var(--text-color); 
            flex-direction: column;
            gap: 18px;
            align-items: flex-start;
            text-align: left; 
        }

        .left-section h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .left-section p {
            margin: 0;
            color: var(--muted);
            max-width: 400px;
        }

        .illustration-placeholder { 
             display: block;
             margin-bottom: 20px;
             color: var(--muted);
             font-style: italic;
        }


        .link-primary { 
            color: var(--blue);
            text-decoration: none; 
            font-weight: 600;
        }
        .link-primary:hover {
            text-decoration: underline; 
        }


        .right-section {
            padding: 48px;
            background: var(--blue); 
            color: #fff; 
            display: flex;
            flex-direction: column;
            gap: 18px;
            align-items: center;
            justify-content: center;
        }

        .form-wrapper {
            width: 100%;
            max-width: 420px;
        }

        h2 {
            text-align: left; 
            margin-bottom: 24px;
            color: #fff; 
            font-size: 24px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px; 
            border-radius: 10px;
            border: 1px solid transparent; 
            background: var(--input-bg);
            color: var(--text-color);
            font-size: 15px;
        }

        .form-input::placeholder {
            color: var(--placeholder-color);
            opacity: 1;
        }


        .form-input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 2px rgba(11, 99, 255, 0.2);
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 0;
            color: var(--text-color); 
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s ease, opacity 0.2s ease;
            text-align: center;
        }
        .btn-primary { 
            background: #ffcc00; 
        }

        .btn-primary:hover {
            opacity: 0.9; 
        }

        .btn-secondary { 
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.5); 
            margin-top: 10px;
        }

        .btn-secondary:hover {
             background: rgba(255, 255, 255, 0.1); 
        }

        .muted-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8); 
            text-align: center;
            margin-top: 24px;
        }

        .link-light { 
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
        .link-light:hover {
            text-decoration: underline;
        }


        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fee2e2; 
            color: #991b1b; 
            border: 1px solid #fca5a5;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
        }

        .field-error {
            font-size: 13px;
            color: #fef2f2; 
            margin-top: 4px; 
        }
        .form-check {
             display: flex;
             align-items: center;
             gap: 8px;
             margin-bottom: 20px;
             color: rgba(255, 255, 255, 0.9);
             font-size: 14px;
        }
        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #ffcc00; 
        }
        .password-hint {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: -12px;
            margin-bottom: 16px;
            display: block;
        }
        .logo-img {
            display: block;
            width: 80px;          
            height: auto;
            margin-bottom: 50px;  
        }



        @media(max-width:980px){
            .container-panel{
                grid-template-columns:1fr;
            }
            .left-section{
                display: none; 
            }
            .right-section{
                padding: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container-panel" role="main">
        <div class="left-section">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LaporAE" class="logo-img">
            <h1>Selamat Datang di LaporAe!</h1>
            <p>Daftar sekarang untuk mulai melaporkan dan memantau kasus. Kami menjaga data Anda dengan aman.</p>
            <div style="margin-top:12px">
                <a class="link-primary" href="{{ route('login.form') }}">Already have an account? Sign in</a>
            </div>
        </div>


        <div class="right-section" aria-labelledby="register-heading">
            <div class="form-wrapper">
                <h2 id="register-heading">Create account</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.process') }}">
                    @csrf

                    <div class="form-group">
                        <label for="nama_lengkap" class="sr-only">Nama Lengkap</label>
                        <input id="nama_lengkap" type="text" name="nama_lengkap" class="form-input" value="{{ old('nama_lengkap') }}" placeholder="Your name" required autofocus>
                        @error('nama_lengkap') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                         <label for="email" class="sr-only">Email</label>
                        <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="Your e-mail" required>
                        @error('email') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                         <label for="password" class="sr-only">Password</label>
                        <input id="password" type="password" name="password" class="form-input" placeholder="Create password" required>
                        <span class="password-hint">Minimal 8 karakter.</span>
                        @error('password') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="Confirm password" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="showPasswords">
                        <label for="showPasswords">Tampilkan kata sandi</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1">
                        <label for="is_admin">Register as admin</label>
                    </div>
                    
                    @error('is_admin') <div class="field-error" style="margin-top:-16px; margin-bottom: 16px;">{{ $message }}</div> @enderror


                    <button class="btn btn-primary" type="submit">Create Account</button> 

                </form>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pwd = document.getElementById('password');
        const pwdConfirm = document.getElementById('password_confirmation');
        const show = document.getElementById('showPasswords');

        if (!pwd || !pwdConfirm || !show) return;

        show.addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            pwd.type = type;
            pwdConfirm.type = type;
        });
    });
</script>
</body>
</html>
