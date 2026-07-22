<!-- resources/views/livewire/settings/password.blade.php -->
<section class="w-full">
    <x-settings.layout :heading="__('settings.update_password')" :subheading="__('settings.update_password_description')">
        <form wire:submit="updatePassword" class="bx-settings-form">
            <!-- Current Password -->
            <div class="bx-form-group">
                <label class="bx-form-label required">{{ __('settings.current_password') }}</label>
                <div class="bx-password-input">
                    <input type="password"
                           wire:model="current_password"
                           class="bx-input @error('current_password') bx-input-error @enderror"
                           required
                           autocomplete="current-password" />
                    <button type="button" class="bx-password-toggle" onclick="togglePasswordVisibility(this)" title="Toggle password visibility">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <span class="bx-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- New Password -->
            <div class="bx-form-group">
                <label class="bx-form-label required">{{ __('settings.new_password') }}</label>
                <div class="bx-password-input">
                    <input type="password"
                           wire:model="password"
                           class="bx-input @error('password') bx-input-error @enderror"
                           required
                           autocomplete="new-password" />
                    <button type="button" class="bx-password-toggle" onclick="togglePasswordVisibility(this)" title="Toggle password visibility">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="bx-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="bx-form-group">
                <label class="bx-form-label required">{{ __('settings.confirm_new_password') }}</label>
                <div class="bx-password-input">
                    <input type="password"
                           wire:model="password_confirmation"
                           class="bx-input @error('password_confirmation') bx-input-error @enderror"
                           required
                           autocomplete="new-password" />
                    <button type="button" class="bx-password-toggle" onclick="togglePasswordVisibility(this)" title="Toggle password visibility">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <span class="bx-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Requirements -->
            <div class="bx-password-requirements">
                <h4>Password Requirements</h4>
                <ul>
                    <li class="bx-requirement">✓ At least 8 characters</li>
                    <li class="bx-requirement">✓ Contains uppercase and lowercase letters</li>
                    <li class="bx-requirement">✓ Contains at least one number</li>
                    <li class="bx-requirement">✓ Contains at least one special character</li>
                </ul>
            </div>

            <!-- Submit -->
            <div class="bx-form-actions">
                <button type="submit" class="bx-btn bx-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    {{ __('global.save') }}
                </button>
                <div class="bx-form-actions-message">
                    <x-action-message on="password-updated">
                        <span class="bx-saved-message">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('global.saved') }}
                        </span>
                    </x-action-message>
                </div>
            </div>
        </form>
    </x-settings.layout>
</section>

<script>
    function togglePasswordVisibility(button) {
        const input = button.closest('.bx-password-input').querySelector('input');
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);

        const svg = button.querySelector('svg');
        if (type === 'text') {
            svg.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            `;
        } else {
            svg.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            `;
        }
    }
</script>
