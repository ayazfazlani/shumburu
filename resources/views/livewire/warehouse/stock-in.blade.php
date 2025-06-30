<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">📥 Material Stock In</h1>
                    <p class="text-base-content/70 mt-1">Record incoming raw materials to warehouse</p>
                </div>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-primary">
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
        @if (session()->has('message'))
            <div class="alert alert-success mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Stock In Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary mb-6">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                </path>
                            </svg>
                            New Stock In Record
                        </h2>

                        <form wire:submit.prevent="save" class="space-y-6">
                            <!-- Raw Material Selection -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Raw Material *</span>
                                </label>
                                <select wire:model="raw_material_id"
                                    class="select select-bordered w-full @error('raw_material_id') select-error @enderror">
                                    <option value="">Select Raw Material</option>
                                    @foreach ($rawMaterials as $material)
                                        <option value="{{ $material->id }}">{{ $material->name }}
                                            ({{ $material->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('raw_material_id')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Quantity (kg) *</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" wire:model="quantity" step="0.001" min="0.001"
                                        class="input input-bordered w-full @error('quantity') input-error @enderror"
                                        placeholder="Enter quantity in kg">
                                    <span class="btn btn-square btn-outline">kg</span>
                                </div>
                                @error('quantity')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Batch Number -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Batch Number *</span>
                                </label>
                                <input type="text" wire:model="batch_number"
                                    class="input input-bordered w-full @error('batch_number') input-error @enderror"
                                    placeholder="Enter batch number">
                                @error('batch_number')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Received Date -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Received Date *</span>
                                </label>
                                <input type="date" wire:model="received_date"
                                    class="input input-bordered w-full @error('received_date') input-error @enderror">
                                @error('received_date')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Notes</span>
                                </label>
                                <textarea wire:model="notes" rows="3" class="textarea textarea-bordered @error('notes') textarea-error @enderror"
                                    placeholder="Additional notes (optional)"></textarea>
                                @error('notes')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="form-control pt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Record Stock In
                                </button>
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
                            Stock In Guidelines
                        </h3>
                        <div class="space-y-4">
                            <div class="alert alert-info">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Material Types</h4>
                                    <p class="text-sm">We handle 7 types of raw materials for HDPE pipe production</p>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Batch Tracking</h4>
                                    <p class="text-sm">Each batch must have a unique batch number for traceability</p>
                                </div>
                            </div>

                            <div class="alert alert-success">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Quality Control</h4>
                                    <p class="text-sm">All materials are checked for quality before stock-in</p>
                                </div>
                            </div>
                        </div>

                        <!-- Raw Materials List -->
                        <div class="divider"></div>
                        <h4 class="font-semibold mb-3">Available Raw Materials</h4>
                        <div class="space-y-2">
                            @foreach ($rawMaterials as $material)
                                <div class="flex items-center justify-between p-2 bg-base-200 rounded-lg">
                                    <div>
                                        <div class="font-medium text-sm">{{ $material->name }}</div>
                                        <div class="text-xs opacity-70">{{ $material->code }}</div>
                                    </div>
                                    <div class="badge badge-outline badge-sm">{{ $material->unit }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <!-- head -->
                <thead>
                    <tr>
                        <th></th>
                        <th>Raw Material</th>
                        <th>Quantity</th>
                        <th>Batch No</th>
                        <th>Received Date</th>
                        <th>Received By</th>
                        <th>note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stockIns as $item)
                        <tr>
                            <th></th>
                            <td>{{ $item->rawMaterial->name }}</td>
                            <td>{{ round($item->quantity, 2) }} -kg</td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ $item->received_date }}</td>
                            <td>{{ $item->receivedBy->name }}</td>
                            <td>{{ $item->notes }}</td>
                        </tr>
                    @endforeach


                </tbody>
            </table>
            <div class="join float-end">
                <button class="join-item btn">1</button>
                <button class="join-item btn btn-active">2</button>
                <button class="join-item btn">3</button>
                <button class="join-item btn">4</button>
            </div>
        </div>
    </div>
</div>
