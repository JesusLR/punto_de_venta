@extends('maestra')
@section("titulo")
    Iniciar Sesión
@endsection
@section('contenido')
<style>
    .login-container {
        min-height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0f0f0f 0%, #2a2a2a 100%);
        padding: 2rem;
    }

    .login-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
        animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        border-top: 3px solid #D4AF37;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header {
        background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
        padding: 2.5rem 2rem;
        text-align: center;
        color: white;
        border-bottom: 1px solid #D4AF37;
    }

    .login-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        margin-bottom: 0.5rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #D4AF37;
    }

    .login-header p {
        font-size: 0.9rem;
        color: #b0b0b0;
        margin: 0;
        letter-spacing: 1px;
        font-weight: 500;
    }

    .login-header i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
        color: #D4AF37;
        opacity: 0.9;
    }

    .login-body {
        padding: 2.5rem 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #2a2a2a;
        margin-bottom: 0.7rem;
        display: block;
        font-size: 0.9rem;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .form-control {
        border: 1px solid #d0d0d0;
        border-radius: 6px;
        padding: 0.9rem 1rem;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fafafa;
        color: #333;
    }

    .form-control::placeholder {
        color: #999;
        font-weight: 400;
    }

    .form-control:focus {
        background: white;
        border-color: #D4AF37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1);
        outline: none;
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        background: #fdf5f5;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.1);
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 0.8rem;
        margin-top: 0.4rem;
        display: block;
        font-weight: 500;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        cursor: pointer;
        border: 1px solid #d0d0d0;
        border-radius: 4px;
        transition: all 0.3s ease-in-out;
        background: white;
    }

    .form-check-input:checked {
        background: #D4AF37;
        border-color: #D4AF37;
    }

    .form-check-label {
        cursor: pointer;
        color: #555;
        font-weight: 500;
        margin: 0;
        font-size: 0.9rem;
    }

    .login-actions {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-login {
        flex: 1;
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        color: #D4AF37;
        border: 1px solid #D4AF37;
        border-radius: 6px;
        padding: 1rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-transform: uppercase;
        min-height: 48px;
    }

    .btn-login:hover {
        background: #D4AF37;
        color: white;
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        transform: translateY(-2px);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .btn-forgot {
        background: transparent;
        color: #2a2a2a;
        border: 1px solid #2a2a2a;
        border-radius: 6px;
        padding: 0.9rem 1.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        letter-spacing: 0.5px;
    }

    .btn-forgot:hover {
        background: #f5f5f5;
        color: #D4AF37;
        border-color: #D4AF37;
    }

    .login-info {
        background: #f9f9f9;
        border-left: 3px solid #D4AF37;
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1.5rem;
    }

    .login-info p {
        margin: 0;
        color: #555;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .login-info i {
        color: #D4AF37;
        margin-right: 0.5rem;
    }

    @media (max-width: 600px) {
        .login-container {
            padding: 1rem;
        }

        .login-header {
            padding: 2rem 1.5rem;
        }

        .login-header h1 {
            font-size: 1.5rem;
        }

        .login-body {
            padding: 2rem 1.5rem;
        }

        .login-actions {
            flex-direction: column;
        }

        .btn-login,
        .btn-forgot {
            width: 100%;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fa fa-gem"></i>
            <h1>{{env("APP_NAME")}}</h1>
            <p>Sistema de Gestión</p>
        </div>

        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Cuenta</label>
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
                            <i class="fa fa-exclamation-circle mr-2"></i>{{ $message }}
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
                            <i class="fa fa-exclamation-circle mr-2"></i>{{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            name="remember"
                            id="remember" 
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remember">
                            Mantener sesión activa
                        </label>
                    </div>
                </div> --}}

                <div class="login-actions">
                    <button type="submit" class="btn-login">
                        <i class="fa fa-sign-in-alt mr-2"></i>Ingresar
                    </button>

                    @if (Route::has('password.request'))
                        <a class="btn-forgot" href="{{ route('password.request') }}">
                            <i class="fa fa-key mr-2"></i>Recuperar Acceso
                        </a>
                    @endif
                </div>
            </form>

            {{-- <div class="login-info">
                <p>
                    <i class="fa fa-lock"></i>
                    <strong>Acceso Seguro:</strong> Tus datos están protegidos con encriptación SSL.
                </p>
            </div> --}}
        </div>
    </div>
</div>
@endsection
