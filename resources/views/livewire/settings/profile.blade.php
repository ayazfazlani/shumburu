<!-- resources/views/livewire/settings/profile.blade.php -->
<section class="w-full">
    <x-settings.layout :heading="__('settings.profile')" :subheading="__('settings.profile_description')">
        <form wire:submit="updateProfileInformation" class="bx-settings-form">
            <!-- Name -->
            <div class="bx-form-group">
                <label class="bx-form-label required">{{ __('users.name') }}</label>
                <input type="text"
                       wire:model="name"
                       class="bx-input @error('name') bx-input-error @enderror"
                       required
                       autofocus
                       autocomplete="name" />
                @error('name')
                    <span class="bx-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="bx-form-group">
                <label class="bx-form-label required">{{ __('users.email') }}</label>
                <input type="email"
                       wire:model="email"
                       class="bx-input @error('email') bx-input-error @enderror"
                       required
                       autocomplete="email" />
                @error('email')
                    <span class="bx-error">{{ $message }}</span>
                @enderror

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div class="bx-verification-notice">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="bx-verification-text">
                                {{ __('settings.your_email_is_unverified') }}
                                <button type="button"
                                        wire:click.prevent="resendVerificationNotification"
                                        class="bx-link">
                                    {{ __('settings.click_here_to_request_another') }}
                                </button>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                                <p class="bx-verification-success">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('settings.verification_link_sent') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
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
                    <x-action-message on="profile-updated">
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

        <!-- Delete Account -->
        <div class="bx-settings-delete">
            <div class="bx-settings-delete-header">
                <div class="bx-danger-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-red-600 dark:text-red-400">{{ __('settings.delete_account_title') }}</h3>
                    <p class="bx-settings-delete-subtitle">{{ __('settings.delete_account_subtitle') }}</p>
                </div>
            </div>
            <div class="bx-settings-delete-body">
                <button type="button"
                        class="bx-btn bx-btn-danger"
                        onclick="document.getElementById('delete-account-modal').classList.add('open')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('settings.delete_account_title') }}
                </button>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete-account-modal" class="bx-modal-overlay bx-modal-overlay-danger" onclick="this.classList.remove('open')">
            <div class="bx-modal bx-modal-sm" onclick="event.stopPropagation()">
                <div class="bx-modal-header bx-modal-header-danger">
                    <h3 class="text-red-600 dark:text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        {{ __('settings.delete_are_you_sure') }}
                    </h3>
                    <button type="button" onclick="document.getElementById('delete-account-modal').classList.remove('open')" class="bx-modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="bx-modal-body">
                    <p class="bx-modal-text">{{ __('settings.delete_are_you_sure_text') }}</p>
                    <div class="bx-form-group">
                        <label class="bx-form-label required">{{ __('settings.password') }}</label>
                        <input type="password"
                               wire:model="password"
                               class="bx-input @error('password') bx-input-error @enderror"
                               id="password" />
                        @error('password')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="bx-modal-footer">
                    <button type="button" onclick="document.getElementById('delete-account-modal').classList.remove('open')" class="bx-btn bx-btn-secondary">
                        {{ __('global.cancel') }}
                    </button>
                    <button type="button" wire:click="deleteUser" class="bx-btn bx-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('settings.delete_account_title') }}
                    </button>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>
