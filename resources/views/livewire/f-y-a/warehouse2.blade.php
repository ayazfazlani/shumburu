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
        <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-accent">New Movement</h2>
                        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Type *</span></label>
                                <select class="select select-bordered @error('movement_type') select-error @enderror"
                                    wire:model="movement_type">
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
                                    wire:model="finished_good_id">
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
                                    wire:model="batch_number" placeholder="Batch Number" />
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
                                    wire:model="purpose">
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
                                    wire:model="customer_id">
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
                                <button type="submit" class="btn btn-accent">Save Movement</button>
                                <button type="reset" wire:click="$refresh" class="btn btn-outline">Reset</button>
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
                            <input type="date" class="input input-bordered" wire:model.live="filter_date_from" />
                            <input type="date" class="input input-bordered" wire:model.live="filter_date_to" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body overflow-x-auto">
                <h3 class="card-title">Movements</h3>
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
                        </tr>
                    </thead>
                    <tbody>
                        {{-- {{ dd($movements) }} --}}
                        @foreach ($movements as $move)

                        <tr>
                            <td>{{ $move->movement_date }}</td>
                            <td class="uppercase">{{ $move->movement_type }}</td>
                            <td>{{ $move->finishedGood->product->name ?? '-' }}</td>
                            <td>{{ $move->batch_number }}</td>
                            <td>{{ $move->quantity }}</td>
                            <td>{{ $move->purpose }}</td>
                            <td>
                                @if($move->customer)
                                {{ $move->customer->name ?? '-' }}
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ $move->createdBy->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $movements->links() }}</div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body overflow-x-auto">
                <h3 class="card-title">Current Stock in FYA</h3>
                <table class="table table-compact w-full text-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Purpose</th>
                            <th>Customer</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockBalance as $row)
                        <tr>
                            <td>{{ $row->finishedGood->product->name ?? '-' }}</td>
                            <td>{{ $row->batch_number }}</td>
                            <td>{{ $row->purpose }}</td>
                            <td>
                                @if($row->customer_id && $row->customer)
                                {{ $row->customer->name ?? '-' }}
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ number_format($row->balance, 3) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>