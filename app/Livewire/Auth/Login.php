<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

// Componente de login con validación reactiva
#[Layout('components.layouts.guest')]
#[Title('Iniciar Sesión')]
class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        $throttleKey = strtolower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Demasiados intentos de inicio de sesión. Por favor intente de nuevo en {$seconds} segundos.");
            return;
        }

        if (! Auth::attempt([
            'email'  => $this->email,
            'password' => $this->password,
            'is_active' => true,
        ], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('email', 'Credenciales incorrectas o cuenta desactivada.');
            return;
        }

        RateLimiter::clear($throttleKey);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
