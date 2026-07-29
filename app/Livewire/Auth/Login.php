<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

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

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->addError('email', 'Invalid email credentials provided.');
    }

    public function loginAs(string $email): void
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user, true);
            session()->regenerate();
            $this->redirect(route('home'), navigate: true);
        }
    }

    public function render()
    {
        $demoUsers = User::all();

        return view('livewire.auth.login', [
            'demoUsers' => $demoUsers,
        ])->layout('layouts.app');
    }
}
