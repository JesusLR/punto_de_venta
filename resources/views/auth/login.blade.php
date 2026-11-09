@extends('maestra_api')
@section("titulo", "Iniciar Sesión")
@section('contenido')

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <img class="login-colibri" src="{{ asset('img/colibri-dorado.svg') }}" alt="Colibrí dorado" width="130" height="130">
            <span class="boutique-eyebrow">{{ config('app.name') }}</span>
            <h1>Bienvenido de nuevo</h1>
            <p>Inicia sesión para continuar.</p>
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
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                        placeholder="usuario@joyeria.com"
                    >
                    @error('email')
                        <span class="invalid-feedback" id="email-error" role="alert">
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
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    >
                    @error('password')
                        <span class="invalid-feedback" id="password-error" role="alert">
                            <i class="fa fa-exclamation-circle mr-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="login-actions">
                    <button type="submit" class="btn-login">
                        Iniciar sesión <span aria-hidden="true">↗</span>
                    </button>
                </div>
            </form>

            <div class="login-footer-info">
                <p>
                    Acceso exclusivo para el equipo de la joyería.
                </p>
                <a href="{{ route('catalogoProductos.index') }}" class="boutique-text-link">Volver a la colección <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </div>
</div>
@endsection
