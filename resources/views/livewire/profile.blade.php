<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-display font-bold text-white">Profile</h1>
            <p class="text-dark-400 mt-1">Manage your account settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Personal Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-dark-400 mb-1">First Name</label>
                        <input type="text" wire:model="firstName" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-dark-400 mb-1">Last Name</label>
                        <input type="text" wire:model="lastName" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-dark-400 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm">
                    </div>
                    <div class="flex justify-end">
                        <button wire:click="updateProfile" class="px-6 py-2.5 btn-primary text-white rounded-lg text-sm font-medium">Save Changes</button>
                    </div>
                </div>
            </div>

            <div class="glass rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Change Password</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-dark-400 mb-1">Current Password</label>
                        <input type="password" wire:model="currentPassword" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm">
                        @error('currentPassword') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-dark-400 mb-1">New Password</label>
                        <input type="password" wire:model="password" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-dark-400 mb-1">Confirm Password</label>
                        <input type="password" wire:model="passwordConfirmation" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm">
                    </div>
                    <div class="flex justify-end">
                        <button wire:click="updatePassword" class="px-6 py-2.5 btn-primary text-white rounded-lg text-sm font-medium">Update Password</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
