<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-accent">📦 Finished Goods Record</h1>
                    <p class="text-base-content/70 mt-1">Record daily finished goods production</p>
                </div>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-accent">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Warehouse
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Success/Error Messages -->
        @if (session('message'))
        <div class="alert alert-success mb-6">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('message') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Finished Goods Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-accent mb-6">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($isEditing)
                            Edit Finished Goods Record
                            @else
                            New Finished Goods Record
                            @endif
                        </h2>

                        <form wire:submit.prevent="save" class="space-y-6" id="finishedGoodsForm">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Product *</span>
                                </label>
                                <select wire:model="product_id"
                                    class="select select-bordered w-full @error('product_id') select-error @enderror">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Type *</span>
                                    </label>
                                    <select wire:model="type"
                                        class="select select-bordered w-full @error('type') select-error @enderror">
                                        <option value="roll">Roll</option>
                                        <option value="cut">Cut</option>
                                    </select>
                                    @error('type')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Length (m) *</span>
                                    </label>
                                    <input type="number" wire:model="length_m" min="0.01" step="0.01"
                                        class="input input-bordered w-full @error('length_m') input-error @enderror"
                                        placeholder="Length in meters" />
                                    @error('length_m')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Surface</span>
                                    </label>
                                    <select wire:model="Surface"
                                        class="select select-bordered w-full @error('Surface') select-error @enderror">
                                        <option value="">Select Surface</option>
                                        <option value="smooth">Smooth</option>
                                        <option value="rough">Rough</option>
                                    </select>
                                    @error('Surface')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Outer Diameter</span>
                                    </label>
                                    <input type="number" wire:model="outerDiameter" min="0.01" step="0.01"
                                        class="input input-bordered w-full @error('outerDiameter') input-error @enderror"
                                        placeholder="Outer diameter" />
                                    @error('outerDiameter')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Quantity *</span>
                                    </label>
                                    <input type="number" wire:model="quantity" min="0.01" step="0.01"
                                        class="input input-bordered w-full @error('quantity') input-error @enderror"
                                        placeholder="Quantity" />
                                    @error('quantity')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Batch Number *</span>
                                    </label>
                                    <input type="text" wire:model="batch_number"
                                        class="input input-bordered w-full @error('batch_number') input-error @enderror"
                                        placeholder="Batch Number" />
                                    @error('batch_number')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Waste Quantity (kg)</span>
                                    </label>
                                    <input type="number" wire:model="waste_quantity" min="0" step="0.01"
                                        class="input input-bordered w-full @error('waste_quantity') input-error @enderror"
                                        placeholder="Waste quantity in kg" />
                                    @error('waste_quantity')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Thickness</span>
                                    </label>
                                    <input type="text" wire:model="thickness"
                                        class="input input-bordered w-full @error('thickness') input-error @enderror"
                                        placeholder="Thickness" />
                                    @error('thickness')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Start Ovality</span>
                                    </label>
                                    <input type="text" wire:model="startOvality"
                                        class="input input-bordered w-full @error('startOvality') input-error @enderror"
                                        placeholder="Enter start ovality" />
                                    @error('startOvality')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">End Ovality</span>
                                    </label>
                                    <input type="text" wire:model="endOvality"
                                        class="input input-bordered w-full @error('endOvality') input-error @enderror"
                                        placeholder="Enter end ovality" />
                                    @error('endOvality')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Stripe Color</span>
                                    </label>
                                    <input type="text" wire:model="stripeColor"
                                        class="input input-bordered w-full @error('stripeColor') input-error @enderror"
                                        placeholder="Enter stripe color" />
                                    @error('stripeColor')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Size</span>
                                    </label>
                                    <input type="number" wire:model="size" min="0.01" step="0.01"
                                        class="input input-bordered w-full @error('size') input-error @enderror"
                                        placeholder="Size" />
                                    @error('size')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Production Date *</span>
                                </label>
                                <input type="date" wire:model="production_date"
                                    class="input input-bordered w-full @error('production_date') input-error @enderror" />
                                @error('production_date')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            {{-- weight per meter --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Weight per Meter (kg/m) *</span>
                                </label>
                                <input type="number" wire:model="weightPerMeter" min="0.01" step="0.01"
                                    class="input input-bordered w-full @error('weightPerMeter') input-error @enderror"
                                    placeholder="Weight per meter in kg/m" />
                                @error('weightPerMeter')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Purpose *</span>
                                </label>
                                <select wire:model="purpose"
                                    class="select select-bordered w-full @error('purpose') select-error @enderror">
                                    <option value="for_stock">For Stock</option>
                                    <option value="for_customer_order">For Customer Order</option>
                                </select>
                                @error('purpose')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            @if ($purpose === 'for_customer_order')
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Customer *</span>
                                </label>
                                <select wire:model="customer_id"
                                    class="select select-bordered w-full @error('customer_id') select-error @enderror">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>
                            @endif

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Notes</span>
                                </label>
                                <textarea wire:model="notes"
                                    class="textarea textarea-bordered w-full @error('notes') textarea-error @enderror"
                                    placeholder="Additional notes"></textarea>
                                @error('notes')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <div class="form-control pt-4 flex gap-2">
                                @if($isEditing)
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Update Record
                                </button>
                                <button type="button" wire:click="cancelEdit" class="btn btn-outline">
                                    Cancel
                                </button>
                                @else
                                <button type="submit" class="btn btn-accent btn-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Record Finished Goods
                                </button>
                                <button type="reset" wire:click="$refresh" class="btn btn-outline">Reset</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Information Panel -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-info">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Finished Goods Guidelines
                        </h3>
                        <div class="space-y-4">
                            <div class="alert alert-accent">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Daily Production</h4>
                                    <p class="text-sm">Record all finished goods at the end of each shift</p>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Traceability</h4>
                                    <p class="text-sm">Link each batch to stock out and production line for full
                                        traceability</p>
                                </div>
                            </div>
                            <div class="alert alert-success">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Quality Control</h4>
                                    <p class="text-sm">Ensure all finished goods meet quality standards</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finished Goods List -->
        <div class="mt-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-accent mb-6">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Finished Goods Records
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Length</th>
                                    <th>Surface</th>
                                    <th>Outer Diameter</th>
                                    <th>Quantity</th>
                                    <th>Total Weight</th>
                                    <th>Waste</th>
                                    <th>Batch No</th>
                                    <th>Ovality</th>
                                    <th>Stripe Color</th>
                                    <th>Weight/Meter</th>
                                    <th>Production Date</th>
                                    <th>Purpose</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($finishedGoods as $item)
                                <tr>
                                    <td class="font-semibold">{{ $item->product->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->type == 'roll' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($item->length_m, 2) }}m</td>
                                    <td>
                                        <span class="badge badge-outline">{{ ucfirst($item->surface) }}</span>
                                    </td>
                                    <td>{{ $item->outer_diameter ? number_format($item->outer_diameter, 2) : 'N/A' }}
                                    </td>
                                    <td>{{ number_format($item->quantity, 2) }} pcs</td>
                                    <td>{{ number_format($item->weight_per_meter, 2) *
                                        number_format($item->waste_quantity, 2) }} kg</td>
                                    <td>
                                        <span class="badge badge-{{ $item->waste_quantity > 0 ? 'error' : 'success' }}">
                                            {{ number_format($item->waste_quantity, 2) }} kg
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $item->batch_number }}</span>
                                    </td>
                                    <td>
                                        @if($item->start_ovality && $item->end_ovality)
                                        {{ number_format($item->start_ovality, 2) }} - {{
                                        number_format($item->end_ovality, 2) }}
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->stripe_color)
                                        <span class="badge"
                                            style="background-color: {{ $item->stripe_color }}; color: white;">
                                            {{ $item->stripe_color }}
                                        </span>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->weight_per_meter, 2) }} kg/m</td>
                                    <td>{{ $item->production_date }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $item->purpose == 'for_stock' ? 'success' : 'warning' }}">
                                            {{ str_replace('_', ' ', ucfirst($item->purpose)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <button wire:click="edit({{ $item->id }})" class="btn btn-warning btn-xs"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $item->id }})" class="btn btn-error btn-xs"
                                                onclick="return confirm('Are you sure you want to delete this record?')"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $finishedGoods->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Scroll to form when editing
    $wire.on('scroll-to-form', () => {
        document.getElementById('finishedGoodsForm').scrollIntoView({ 
            behavior: 'smooth' 
        });
    });
</script>
@endscript