<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ProfileModal extends Component
{
    public bool $isOpen = true;

    #[Rule('required|min:2|max:255')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:2|max:100')]
    public string $role = '';

    public string $avatar = '';
    public string $new_password = '';

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role ?? 'Team Member';
            $this->avatar = $user->avatar ?? '';
        }
    }

    public function saveProfile(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->validate([
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|min:2|max:100',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (!empty($this->avatar)) {
            $data['avatar'] = $this->avatar;
        }

        if (!empty($this->new_password)) {
            if (strlen($this->new_password) < 6) {
                $this->addError('new_password', 'Password must be at least 6 characters.');
                return;
            }
            $data['password'] = Hash::make($this->new_password);
        }

        $user->update($data);

        $this->dispatch('profile-updated', message: 'Profile settings updated successfully!');
        $this->dispatch('close-profile-modal');
    }

    public function render()
    {
        return view('livewire.profile-modal');
    }
}
