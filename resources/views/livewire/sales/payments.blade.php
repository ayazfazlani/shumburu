<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Record Payment</h1>
    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="label">Delivery</label>
            <select wire:model="delivery_id" class="select select-bordered w-full">
                <option value="">Select Delivery</option>
                @foreach ($deliveries as $delivery)
                    <option value="{{ $delivery->id }}">
                        {{ $delivery->customer->name ?? '' }} - {{ $delivery->product->name ?? '' }}
                        ({{ $delivery->id }})
                    </option>
                @endforeach
            </select>
            @error('delivery_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Customer</label>
            <select wire:model="customer_id" class="select select-bordered w-full">
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('customer_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Amount</label>
            <input type="number" wire:model="amount" class="input input-bordered w-full" min="0" step="0.01"
                placeholder="Amount" />
            @error('amount')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Payment Method</label>
            <input type="text" wire:model="payment_method" class="input input-bordered w-full"
                placeholder="Payment Method" />
            @error('payment_method')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Bank Slip Reference</label>
            <input type="text" wire:model="bank_slip_reference" class="input input-bordered w-full"
                placeholder="Bank Slip Reference" />
            @error('bank_slip_reference')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Proforma Invoice Number</label>
            <input type="text" wire:model="proforma_invoice_number" class="input input-bordered w-full"
                placeholder="Proforma Invoice Number" />
            @error('proforma_invoice_number')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Payment Date</label>
            <input type="date" wire:model="payment_date" class="input input-bordered w-full" />
            @error('payment_date')
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
            <button type="submit" class="btn btn-primary">Record Payment</button>
            <button type="reset" wire:click="$refresh" class="btn btn-outline">Reset</button>
        </div>
    </form>
</div>
