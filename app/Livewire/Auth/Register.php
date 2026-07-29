<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Register extends Component
{
    #[Rule('required|min:2|max:255')]
    public string $name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    #[Rule('required|min:2|max:100')]
    public string $role = 'Software Engineer';

    public function register(): void
    {
        $this->validate();

        $avatar = "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=6E63D9&color=fff";

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'avatar' => $avatar,
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.app');
    }
}
