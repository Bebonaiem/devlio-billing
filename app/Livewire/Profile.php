<?php
namespace App\Livewire;

use Livewire\Component;

class Profile extends Component
{
    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->firstName = $user->first_name ?? '';
        $this->lastName = $user->last_name ?? '';
        $this->email = $user->email ?? '';
    }

    public function updateProfile()
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.auth()->id(),
        ]);

        auth()->user()->update([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! \Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The current password is incorrect.');

            return;
        }

        $user->update(['password' => $this->password]);
        $this->currentPassword = '';
        $this->password = '';
        $this->passwordConfirmation = '';

        session()->flash('success', 'Password updated successfully!');
    }

    public function render()
    {
        $user = auth()->user();
        $paymentMethods = $user->paymentMethods;
        $credits = $user->credits;

        return view('livewire.profile', compact('user', 'paymentMethods', 'credits'));
    }
}
