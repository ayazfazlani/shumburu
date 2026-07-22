<!-- resources/views/livewire/settings/locale.blade.php -->
<section class="w-full">
    <x-settings.layout :heading="__('settings.locale')" :subheading="__('settings.locale_description')">
        <div class="bx-locale-options">
            <div class="bx-form-group">
                <label class="bx-form-label required">{{ __('settings.select_language') }}</label>
                <select wire:model="locale" class="bx-select" wire:change="updateLocale">
                    <option value="en">English</option>
                    <option value="es">Español</option>
                    <option value="fr">Français</option>
                    <option value="de">Deutsch</option>
                    <option value="zh">中文</option>
                    <option value="ar">العربية</option>
                    <option value="ur">اردو</option>
                </select>
                @error('locale')
                    <span class="bx-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="bx-locale-preview">
                <div class="bx-locale-preview-item">
                    <span class="bx-locale-preview-label">Current Language</span>
                    <span class="bx-locale-preview-value">{{ ucfirst($locale ?? 'English') }}</span>
                </div>
                <div class="bx-locale-preview-item">
                    <span class="bx-locale-preview-label">Language Code</span>
                    <span class="bx-locale-preview-value">{{ $locale ?? 'en' }}</span>
                </div>
            </div>

            <div class="bx-form-actions">
                <button type="button" wire:click="updateLocale" class="bx-btn bx-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    {{ __('global.save') }}
                </button>
                <div class="bx-form-actions-message">
                    @if (session('locale-updated'))
                        <span class="bx-saved-message">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('global.saved') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>
