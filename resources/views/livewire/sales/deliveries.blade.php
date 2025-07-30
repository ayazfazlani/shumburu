{{-- <section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Deliveries</h1>
            <p class="text-gray-500">Create, edit, and delete deliveries.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="deliverySearch" placeholder="Search batch number..." class="input input-bordered" />
            <select wire:model="deliveryPerPage" class="select select-bordered">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <button class="btn btn-primary" wire:click="openDeliveryCreateModal">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Delivery
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Batch #</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Delivery Date</th>
                    <th>Notes</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $delivery)
                    <tr>
                        <td>{{ $delivery->productionOrder->order_number ?? '-' }}</td>
                        <td>{{ $delivery->customer->name ?? '-' }}</td>
                        <td>{{ $delivery->product->name ?? '-' }}</td>
                        <td>{{ $delivery->quantity }}</td>
                        <td>{{ $delivery->batch_number }}</td>
                        <td>{{ $delivery->unit_price }}</td>
                        <td>{{ $delivery->total_amount }}</td>
                        <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('Y-m-d') : '' }}</td>
                        <td>{{ $delivery->notes }}</td>
                        <td class="text-right flex gap-2 justify-end">
                            <button class="btn btn-xs btn-outline" wire:click="openDeliveryEditModal({{ $delivery->id }})">Edit</button>
                            <button class="btn btn-xs btn-error" wire:click="confirmDeliveryDelete({{ $delivery->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-gray-400 py-6">No deliveries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $deliveries->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="delivery-modal" class="modal" @if ($showDeliveryModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveDelivery">
            <h3 class="font-bold text-lg mb-4">{{ $isDeliveryEdit ? 'Edit Delivery' : 'Create Delivery' }}</h3>
            <div class="mb-4">
                <label class="label">Production Order</label>
                <select wire:model.defer="production_order_id" class="select select-bordered w-full">
                    <option value="">Select Production Order</option>
                    @foreach ($productionOrders as $order)
                        <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                    @endforeach
                </select>
                @error('production_order_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Customer</label>
                <select wire:model.defer="customer_id" class="select select-bordered w-full">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Product</label>
                <select wire:model.defer="product_id" class="select select-bordered w-full">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Quantity</label>
                <input type="number" wire:model.defer="quantity" class="input input-bordered w-full" min="1" placeholder="Quantity" />
                @error('quantity')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Batch Number</label>
                <input type="text" wire:model.defer="batch_number" class="input input-bordered w-full" placeholder="Batch Number" />
                @error('batch_number')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Unit Price</label>
                <input type="number" wire:model.defer="unit_price" class="input input-bordered w-full" min="0" step="0.01" placeholder="Unit Price" />
                @error('unit_price')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Delivery Date</label>
                <input type="date" wire:model.defer="delivery_date" class="input input-bordered w-full" />
                @error('delivery_date')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Notes</label>
                <textarea wire:model.defer="notes" class="textarea textarea-bordered w-full" placeholder="Additional notes"></textarea>
                @error('notes')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeliveryModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isDeliveryEdit ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-modal" class="modal" @if ($showDeliveryDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Delivery?</h3>
            <p class="mb-4">Are you sure you want to delete this delivery? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeliveryDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteDelivery">Delete</button>
            </div>
        </form>
    </dialog>
</section> --}}


<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Delivered Orders</h1>
            {{-- <p class="text-gray-500">Create, edit, and delete production orders.</p> --}}
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="orderSearch" placeholder="Search orders..." class="input input-bordered" />
            <select wire:model="orderPerPage" class="select select-bordered">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            {{-- <button class="btn btn-primary" wire:click="openOrderCreateModal">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> --}}
                {{-- New Order --}}
            {{-- </button> --}}
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    {{-- <th>Product</th>
                    <th>Quantity</th> --}}
                    <th>Status</th>
                    <th>Requested Date</th>
                    <th>Notes</th>
                    <th>View Details</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer->name ?? '-' }}</td>
                        {{-- <td>{{ $order->product->name ?? '-' }}</td>
                        <td>{{ $order->quantity }}</td> --}}
                        <td>
                            @if ($order->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($order->status === 'approved')
                                <span class="badge badge-info">Approved</span>
                            @elseif($order->status === 'in_production')
                                <span class="badge badge-primary">In Production</span>
                            @elseif($order->status === 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-accent">Delivered</span>
                            @endif
                        </td>
                        <td>{{ $order->requested_date ? $order->requested_date->format('Y-m-d') : '' }}</td>
                        <td>{{ $order->notes }}</td>
                        <td><a href="{{ route('order-items',$order->id) }}">view</a></td>
                        <td class="text-right flex gap-2 justify-end">
                            <button class="btn btn-xs btn-outline" wire:click="openOrderEditModal({{ $order->id }})">Edit</button>
                            <button class="btn btn-xs btn-error" wire:click="confirmOrderDelete({{ $order->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-400 py-6">No production orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="order-modal" class="modal" @if ($showOrderModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveOrder">
            <h3 class="font-bold text-lg mb-4">{{ $isOrderEdit ? 'Edit Order' : 'Create Order' }}</h3>
            <div class="mb-4">
                <label class="label">Order Number</label>
                <input type="text" wire:model.defer="order_number" class="input input-bordered w-full" placeholder="Order Number" readonly />
                @error('order_number')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Customer</label>
                <select wire:model.defer="customer_id" class="select select-bordered w-full">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div class="mb-4">
                <label class="label">Product</label>
                <select wire:model.defer="product_id" class="select select-bordered w-full">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Quantity</label>
                <input type="number" wire:model.defer="quantity" class="input input-bordered w-full" min="1" placeholder="Quantity" />
                @error('quantity')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div> --}}
            <div class="mb-4">
                <label class="label">Status</label>
                <select wire:model.defer="status" class="select select-bordered w-full">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="in_production">In Production</option>
                    <option value="completed">Completed</option>
                    <option value="delivered">Delivered</option>
                </select>
                @error('status')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Requested Date</label>
                <input type="date" wire:model.defer="requested_date" class="input input-bordered w-full" />
                @error('requested_date')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Notes</label>
                <textarea wire:model.defer="notes" class="textarea textarea-bordered w-full" placeholder="Additional notes"></textarea>
                @error('notes')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showOrderModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isOrderEdit ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-modal" class="modal" @if ($showOrderDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Order?</h3>
            <p class="mb-4">Are you sure you want to delete this order? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showOrderDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteOrder">Delete</button>
            </div>
        </form>
    </dialog>
</section>
