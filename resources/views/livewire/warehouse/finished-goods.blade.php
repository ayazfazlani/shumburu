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
            <div class="alert alert-success mb-6">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error mb-6">{{ session('error') }}</div>
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
                            New Finished Goods Record
                        </h2>
                        <form wire:submit.prevent="save" class="space-y-6">
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
                                    <select wire:model="type" class="select select-bordered w-full @error('type') select-error @enderror">
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
                                    <select wire:model="Surface" class="select select-bordered w-full @error('Surface') select-error @enderror">
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
                                        <span class="label-text font-semibold">Start Ovality *</span>
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
                                        <span class="label-text font-semibold">End Ovality *</span>
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
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Stripe color</span>
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
                                <textarea wire:model="notes" class="textarea textarea-bordered w-full @error('notes') textarea-error @enderror"
                                    placeholder="Additional notes"></textarea>
                                @error('notes')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            <div class="form-control pt-4 flex gap-2">
                                <button type="submit" class="btn btn-accent btn-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Record Finished Goods
                                </button>
                                <button type="reset" wire:click="$refresh" class="btn btn-outline">Reset</button>
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
        <div class="overflow-x-auto mt-6">
            <table class="table table-zebra w-full text-sm">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Type</th>
                        <th>Length (m)</th>
                        <th>Surface</th>
                        <th>Outer Diameter</th>
                        <th>Quantity</th>
                        <th>Total Weight</th>
                        <th>Waste (kg)</th>
                        <th>Batch No</th>
                        <th>Ovality</th>
                        <th>Stripe Color</th>
                        <th>Production Date</th>
                        <th>Purpose</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($finishedGoods as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->length_m }}</td>
                            <td>{{ $item->surface }}</td>
                            <td>{{ $item->outer_diameter}}</td>
                            <td>{{ $item->quantity }} pieces</td>
                            <td>{{ $item->total_weight }}</td>
                            <td>{{ $item->waste_quantity ?? '0.00' }}</td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ number_format($item->start_ovality, 2) }} - 
    {{ $item->end_ovality ? number_format($item->end_ovality, 2) : 'N/A' }}</td>
                            <td>{{ $item->stripe_color }}</td>
                            <td>{{ $item->production_date }}</td>
                            <td>{{ $item->purpose }}</td>
                            <td>{{ $item->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                <!-- Pagination or other controls can go here -->
            </div>
        </div>
    </div>
</div>
