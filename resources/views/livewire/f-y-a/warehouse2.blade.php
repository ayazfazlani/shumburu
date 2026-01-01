{{-- {{ dd($movements->toJson()) }} --}}
<div class="min-h-screen bg-base-200">
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-accent">🏷️ FYA Warehouse Movements</h1>
                    <p class="text-base-content/70 mt-1">Record and track finished goods movements in FYA storage</p>
                </div>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-accent">
                    Back to Warehouse
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 space-y-6">
        @if (session('message'))
        <div class="alert alert-success shadow-lg">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-error shadow-lg">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="movement-form">
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="card-title text-accent">
                                @if($is_editing)
                                ✏️ Edit Movement
                                @else
                                New Movement
                                @endif
                            </h2>
                            @if($is_editing)
                            <button wire:click="cancelEdit" class="btn btn-sm btn-outline btn-error">Cancel
                                Edit</button>
                            @endif
                        </div>
                        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Type *</span></label>
                                <select class="select select-bordered @error('movement_type') select-error @enderror"
                                    wire:model="movement_type" @if($is_editing) disabled @endif>
                                    <option value="in">IN</option>
                                    <option value="out">OUT</option>
                                </select>
                                @error('movement_type')<label class="label"><span class="label-text-alt text-error">{{
                                        $message }}</span></label>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Finished Good
                                        *</span></label>
                                <select class="select select-bordered @error('finished_good_id') select-error @enderror"
                                    wire:model="finished_good_id" @if($is_editing) disabled @endif>
                                    <option value="">Select finished good</option>
                                    @foreach ($finishedGoods as $fg)
                                    <option value="{{ $fg->id }}">{{ $fg->product->name }} — Batch: {{ $fg->batch_number
                                        }}</option>
                                    @endforeach
                                </select>
                                @error('finished_good_id')<label class="label"><span
                                        class="label-text-alt text-error">{{ $message }}</span></label>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Batch Number
                                        *</span></label>
                                <input type="text"
                                    class="input input-bordered @error('batch_number') input-error @enderror"
                                    wire:model="batch_number" placeholder="Batch Number" @if($is_editing) readonly
                                    @endif />
                                @error('batch_number')<label class="label"><span class="label-text-alt text-error">{{
                                        $message }}</span></label>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Quantity *</span></label>
                                <input type="number" step="0.001" min="0.001"
                                    class="input input-bordered @error('quantity') input-error @enderror"
                                    wire:model="quantity" placeholder="Quantity" />
                                @error('quantity')<label class="label"><span class="label-text-alt text-error">{{
                                        $message }}</span></label>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Purpose *</span></label>
                                <select class="select select-bordered @error('purpose') select-error @enderror"
                                    wire:model="purpose" @if($is_editing) disabled @endif>
                                    <option value="for_stock">For Stock</option>
                                    <option value="for_customer_order">For Customer Order</option>
                                </select>
                                @error('purpose')<label class="label"><span class="label-text-alt text-error">{{
                                        $message }}</span></label>@enderror
                            </div>
                            @if ($purpose === 'for_customer_order')
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Customer *</span></label>
                                <select class="select select-bordered @error('customer_id') select-error @enderror"
                                    wire:model="customer_id" @if($is_editing) disabled @endif>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        @can('can see customer name')
                                        {{ $customer->name }}
                                        @endcan code {{$customer->code}}
                                    </option>
                                    @endforeach
                                </select>
                                @error('customer_id')<label class="label"><span class="label-text-alt text-error">{{
                                        $message }}</span></label>@enderror
                            </div>
                            @endif
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Movement Date
                                        *</span></label>
                                <input type="date"
                                    class="input input-bordered @error('movement_date') input-error @enderror"
                                    wire:model="movement_date" />
                                @error('movement_date')<label class="label"><span class="label-text-alt text-error">{{
                                        $message }}</span></label>@enderror
                            </div>
                            <div class="form-control md:col-span-2">
                                <label class="label"><span class="label-text font-semibold">Notes</span></label>
                                <textarea class="textarea textarea-bordered @error('notes') textarea-error @enderror"
                                    wire:model="notes" placeholder="Additional notes"></textarea>
                                @error('notes')<label class="label"><span class="label-text-alt text-error">{{ $message
                                        }}</span></label>@enderror
                            </div>
                            <div class="md:col-span-2 flex gap-2">
                                <button type="submit" class="btn btn-accent">
                                    @if($is_editing)
                                    Update Movement
                                    @else
                                    Save Movement
                                    @endif
                                </button>
                                <button type="button" wire:click="$refresh" class="btn btn-outline">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-info">Filters</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <select class="select select-bordered" wire:model.live="filter_type">
                                <option value="">All Types</option>
                                <option value="in">IN</option>
                                <option value="out">OUT</option>
                            </select>
                            <select class="select select-bordered" wire:model.live="filter_product_id">
                                <option value="">All Products</option>
                                @foreach ($finishedGoods as $fg)
                                <option value="{{ $fg->id }}">{{ $fg->product->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" class="input input-bordered" wire:model.live="filter_batch"
                                placeholder="Batch" />
                            <select class="select select-bordered" wire:model.live="filter_purpose">
                                <option value="">All Purposes</option>
                                <option value="for_stock">For Stock</option>
                                <option value="for_customer_order">For Customer Order</option>
                            </select>
                            <select class="select select-bordered" wire:model.live="filter_customer_id">
                                <option value="">All Customers</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    @can('can see customer name')
                                    {{ $customer->name }}
                                    @endcan
                                    customer code {{ $customer->code }}
                                </option>
                                @endforeach
                            </select>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" class="input input-bordered" wire:model.live="filter_date_from"
                                    placeholder="From" />
                                <input type="date" class="input input-bordered" wire:model.live="filter_date_to"
                                    placeholder="To" />
                            </div>
                            <button
                                wire:click="$set('filter_type', '') && $set('filter_product_id', '') && $set('filter_batch', '') && $set('filter_purpose', '') && $set('filter_customer_id', '') && $set('filter_date_from', '') && $set('filter_date_to', '')"
                                class="btn btn-outline btn-sm">Clear Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body overflow-x-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="card-title">Movements</h3>
                    <div class="text-sm text-base-content/70">
                        {{ $movements->total() }} total records
                    </div>
                </div>
                <table class="table table-zebra w-full text-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Quantity</th>
                            <th>Purpose</th>
                            <th>Customer</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $move)
                        <tr>
                            <td>{{ $move->movement_date }}</td>
                            <td>
                                <span
                                    class="badge {{ $move->movement_type === 'in' ? 'badge-success' : 'badge-error' }}">
                                    {{ strtoupper($move->movement_type) }}
                                </span>
                            </td>
                            <td>{{ $move->finishedGood->product->name ?? '-' }}</td>
                            <td><code class="text-xs">{{ $move->batch_number }}</code></td>
                            <td class="font-mono">{{ number_format($move->quantity, 3) }}</td>
                            <td>
                                <span class="badge badge-outline">
                                    {{ str_replace('_', ' ', $move->purpose) }}
                                </span>
                            </td>
                            <td>
                                @if($move->customer)
                                {{ $move->customer->name ?? '-' }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-xs">{{ $move->createdBy->name ?? '-' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <button wire:click="edit({{ $move->id }})"
                                        class="btn btn-xs btn-outline btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                    <!-- Open the modal using ID.showModal() method -->
                                    <button onclick="deleteModal{{ $move->id }}.showModal()"
                                        class="btn btn-xs btn-outline btn-error">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Delete
                                    </button>

                                    <!-- DaisyUI Modal -->
                                    <dialog id="deleteModal{{ $move->id }}" class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">Confirm Delete</h3>
                                            <p class="py-4">Are you sure you want to delete movement for batch: <span
                                                    class="font-semibold">{{ $move->batch_number }}</span>?</p>
                                            <p class="text-sm text-warning mb-4">Note: This action cannot be undone and
                                                may affect stock balance.</p>
                                            <div class="modal-action">
                                                <form method="dialog">
                                                    <button class="btn btn-outline">Cancel</button>
                                                    <button class="btn btn-error" wire:click="delete({{ $move->id }})"
                                                        onclick="deleteModal{{ $move->id }}.close()">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-base-content/50">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <span>No movements found.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $movements->links() }}</div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body overflow-x-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="card-title">Current Stock in FYA</h3>
                    <div class="badge badge-accent">{{ $stockBalance->count() }} items</div>
                </div>
                <table class="table table-compact w-full text-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Quantity</th>
                            <th>Purpose</th>
                            <th>Customer</th>
                            <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockBalance as $row)
                        {{-- @dd($stockBalance) --}}
                        <tr>
                            <td>{{ $row->finishedGood->product->name ?? '-' }}</td>
                            <td><code class="text-xs">{{ $row->batch_number }}</code></td>
                            <td>{{ number_format($row->quantity, 3) }}</td>
                            <td>{{ str_replace('_', ' ', $row->purpose) }}</td>
                            <td>
                                @if($row->customer_id && $row->customer)
                                {{ $row->customer->name ?? '-' }}
                                @else
                                -
                                @endif
                            </td>
                            <td
                                class="text-right font-mono font-semibold {{ $row->balance > 0 ? 'text-success' : '' }}">
                                {{ number_format($row->balance, 3) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-base-content/50">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>No stock available.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('scroll-to-form', () => {
        const form = document.getElementById('movement-form');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>
@endscript