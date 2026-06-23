@extends('maestra_api')
@section("titulo", "Iniciar Sesión")
@section('contenido')

<!-- Fuente Premium de Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .login-container {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top, #1b1b1d 0%, #0d0d0e 100%);
        padding: 3rem 1.5rem;
        margin-top: -20px; /* Offset para integrarlo más arriba */
        font-family: 'Montserrat', sans-serif;
    }

    .login-card {
        background: rgba(30, 30, 32, 0.65);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        max-width: 440px;
        width: 100%;
        animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(212, 175, 55, 0.18);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header {
        background: linear-gradient(180deg, rgba(26, 26, 28, 0.9) 0%, rgba(20, 20, 22, 0.9) 100%);
        padding: 3rem 2rem 2.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(212, 175, 55, 0.15);
    }

    .login-header i {
        font-size: 2.8rem;
        margin-bottom: 1rem;
        display: block;
        background: linear-gradient(135deg, #F5D782 0%, #D4AF37 50%, #B38F24 100%);
        -webkit-background-clip: text;
        -webkit-background-fill-color: transparent;
        -webkit-text-fill-color: transparent;
        animation: gemPulse 3s infinite ease-in-out;
    }

    @keyframes gemPulse {
        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 2px rgba(212, 175, 55, 0.2)); }
        50% { transform: scale(1.05); filter: drop-shadow(0 0 12px rgba(212, 175, 55, 0.5)); }
    }

    .login-header h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2.4rem;
        font-weight: 600;
        margin: 0;
        margin-bottom: 0.4rem;
        background: linear-gradient(135deg, #F5D782 0%, #D4AF37 50%, #B38F24 100%);
        -webkit-background-clip: text;
        -webkit-background-fill-color: transparent;
        -webkit-text-fill-color: transparent;
    }

    .login-header p {
        font-size: 0.85rem;
        color: #a0a0a5;
        margin: 0;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-weight: 500;
    }

    .login-body {
        padding: 2.5rem 2.2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: #d2c8b7;
        margin-bottom: 0.6rem;
        display: block;
        font-size: 0.8rem;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .form-control {
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 0.9rem 1.1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.03);
        color: #fff;
        height: auto;
    }

    .form-control::placeholder {
        color: #555;
        font-weight: 400;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.06);
        border-color: #D4AF37;
        box-shadow: 0 0 12px rgba(212, 175, 55, 0.15);
        outline: none;
        color: #fff;
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        background: rgba(231, 76, 60, 0.05);
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 10px rgba(231, 76, 60, 0.2);
    }

    .invalid-feedback {
        color: #ff6b6b;
        font-size: 0.8rem;
        margin-top: 0.4rem;
        display: block;
        font-weight: 500;
    }

    .login-actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-login {
        background: linear-gradient(135deg, #D4AF37 0%, #B38F24 100%);
        color: #111 !important;
        border: none;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        letter-spacing: 1.5px;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.15);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 48px;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
        background: linear-gradient(135deg, #F5D782 0%, #D4AF37 100%);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .login-footer-info {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding-top: 1.25rem;
        margin-top: 1.5rem;
        text-align: center;
    }

    .login-footer-info p {
        margin: 0;
        color: #666;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        line-height: 1.5;
    }

    .login-footer-info i {
        color: #D4AF37;
        margin-right: 0.3rem;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fa fa-gem"></i>
            <h1>{{env("APP_NAME")}}</h1>
            <p>Acceso al Sistema</p>
        </div>

        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                        placeholder="usuario@joyeria.com"
                    >
                    @error('email')
                        <span class="invalid-feedback">
                            <i class="fa fa-exclamation-circle mr-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        id="password" 
                        type="password"
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    >
                    @error('password')
                        <span class="invalid-feedback">
                            <i class="fa fa-exclamation-circle mr-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="login-actions">
                    <button type="submit" class="btn-login">
                        <i class="fa fa-sign-in-alt mr-1"></i> Ingresar al Portal
                    </button>
                </div>
            </form>

            <div class="login-footer-info">
                <p>
                    <i class="fa fa-shield-alt"></i> Portal de Seguridad Protegido.<br>
                    Acceso exclusivo para personal autorizado de la joyería.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
