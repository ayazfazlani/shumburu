<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Create Production Order</h1>
    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif
    <form wire:submit.prevent="save" class="space-y-4">
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
            <label class="label">Product</label>
            <select wire:model="product_id" class="select select-bordered w-full">
                <option value="">Select Product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            @error('product_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="label">Quantity</label>
            <input type="number" wire:model="quantity" class="input input-bordered w-full" min="1"
                placeholder="Quantity" />
            @error('quantity')
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
            <button type="submit" class="btn btn-primary">Create Order</button>
            <button type="reset" wire:click="$refresh" class="btn btn-outline">Reset</button>
        </div>
    </form>
</div>
