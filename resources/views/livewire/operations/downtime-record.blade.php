<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Downtime Record</h1>
    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif
    <form wire:submit.prevent="save" class="space-y-4 max-w-xl">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Date</label>
                <input type="date" wire:model="downtime_date" class="input input-bordered w-full" />
                @error('downtime_date')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="label">Start Time</label>
                <input type="time" wire:model="start_time" class="input input-bordered w-full" />
                @error('start_time')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="label">End Time</label>
                <input type="time" wire:model="end_time" class="input input-bordered w-full" />
                @error('end_time')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="label">Duration (minutes)</label>
                <input type="number" wire:model="duration_minutes" class="input input-bordered w-full" readonly />
            </div>
        </div>
        <div>
            <label class="label">Reason</label>
            <input type="text" wire:model="reason" class="input input-bordered w-full"
                placeholder="Reason for downtime" />
            @error('reason')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Notes</label>
            <textarea wire:model="notes" class="textarea textarea-bordered w-full" placeholder="Additional notes"></textarea>
            @error('notes')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="reset" wire:click="$refresh" class="btn btn-outline">Reset</button>
        </div>
    </form>
</div>
