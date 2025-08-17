<section class="w-full">
    <x-page-heading>
        <x-slot:title>
            Orders Overview
        </x-slot:title>
        <x-slot:subtitle>
            Manage all production orders, payments, and deliveries
        </x-slot:subtitle>
        <x-slot:buttons>
            <a href="{{ route('sales.create-order') }}" class="btn btn-primary">
                Create New Order
            </a>
        </x-slot:buttons>
    </x-page-heading>

    <!-- Filters -->
    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="label">Search</label>
                    <input wire:model.live="search" type="text" placeholder="Search orders..." 
                           class="input input-bordered w-full" />
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="label">Status</label>
                    <select wire:model.live="statusFilter" class="select select-bordered w-full">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="pending_production">Pending Production</option>
                        <option value="approved">Approved</option>
                        <option value="in_production">In Production</option>
                        <option value="completed">Completed</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                
                <!-- Customer Filter -->
                <div>
                    <label class="label">Customer</label>
                    <select wire:model.live="customerFilter" class="select select-bordered w-full">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date Filter -->
                <div>
                    <label class="label">Date</label>
                    <input wire:model.live="dateFilter" type="date" class="input input-bordered w-full" />
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success mb-4">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Orders Table -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('order_number')" class="cursor-pointer">
                                Order #
                                @if($sortField === 'order_number')
                                    @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                @endif
                            </th>
                            <th wire:click="sortBy('requested_date')" class="cursor-pointer">
                                Date
                                @if($sortField === 'requested_date')
                                    @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                @endif
                            </th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr wire:key={$order->id}>
                                <td>
                                    <div class="font-medium">{{ $order->order_number }}</div>
                                </td>
                                <td>{{ $order->requested_date->format('M d, Y') }}</td>
                                <td>
                                    <div class="font-medium">{{ $order->customer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->customer->code }}</div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ $order->items->count() }} items</div>
                                    <div class="text-xs text-gray-500">{{ $order->items->sum('quantity') }} total qty</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ number_format($this->getOrderTotal($order), 2) }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ number_format($this->getTotalPaid($order), 2) }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <progress class="progress progress-primary w-16" 
                                                  value="{{ $this->getPaymentProgress($order) }}" max="100"></progress>
                                        <span class="text-xs">{{ round($this->getPaymentProgress($order)) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $this->getStatusColor($order->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
                                            Actions
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li><a wire:click="viewOrderDetails({{ $order->id }})">View Details</a></li>
                                            <li><a wire:click="addPayment({{ $order->id }})">Add Payment</a></li>
                                            <li><a wire:click="addDelivery({{ $order->id }})">Mark as Delivered</a></li>
                                            @if($order->status === 'pending')
                                                <li><a wire:click="updateOrderStatus({{ $order->id }}, 'approved')">Approve</a></li>
                                            @endif
                                            @if($order->status === 'approved')
                                                <li><a wire:click="updateOrderStatus({{ $order->id }}, 'in_production')">Start Production</a></li>
                                            @endif
                                            @if($order->status === 'in_production')
                                                <li><a wire:click="updateOrderStatus({{ $order->id }}, 'completed')">Mark Complete</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <dialog id="order-details-modal" class="modal" @if ($showOrderDetailsModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-4xl">
            <h3 class="font-bold text-lg mb-4">Order Details - {{ $selectedOrder->order_number ?? '' }}</h3>
            
            @if($selectedOrder)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Order Info -->
                    <div>
                        <h4 class="font-semibold mb-3">Order Information</h4>
                        <div class="space-y-2">
                            <div><strong>Customer:</strong> {{ $selectedOrder->customer->name }}</div>
                            <div><strong>Date:</strong> {{ $selectedOrder->requested_date->format('M d, Y') }}</div>
                            <div><strong>Status:</strong> 
                                <span class="badge {{ $this->getStatusColor($selectedOrder->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $selectedOrder->status)) }}
                                </span>
                            </div>
                            <div><strong>Total:</strong> {{ number_format($this->getOrderTotal($selectedOrder), 2) }}</div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div>
                        <h4 class="font-semibold mb-3">Payment Summary</h4>
                        <div class="space-y-2">
                            <div><strong>Total Paid:</strong> {{ number_format($this->getTotalPaid($selectedOrder), 2) }}</div>
                            <div><strong>Remaining:</strong> {{ number_format($this->getOrderTotal($selectedOrder) - $this->getTotalPaid($selectedOrder), 2) }}</div>
                            <div><strong>Progress:</strong> {{ round($this->getPaymentProgress($selectedOrder)) }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mt-6">
                    <h4 class="font-semibold mb-3">Order Items</h4>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedOrder->items as $item)
                                    <tr>
                                        {{-- <td>{{ $item->product->name }}</td> --}}
                                        <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payments -->
                @if($selectedOrder->payments->count() > 0)
                    <div class="mt-6">
                        <h4 class="font-semibold mb-3">Payments</h4>
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedOrder->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->notes }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Deliveries -->
                @if($selectedOrder->deliveries->count() > 0)
                    <div class="mt-6">
                        <h4 class="font-semibold mb-3">Deliveries</h4>
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedOrder->deliveries as $delivery)
                                        <tr>
                                            <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                            {{-- <td>{{ $delivery->product->name }}</td> --}}
                                            <td>{{ $delivery->quantity }}</td>
                                            <td>{{ number_format($delivery->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            <div class="modal-action">
                <button type="button" class="btn" wire:click="closeOrderDetailsModal">Close</button>
            </div>
        </form>
    </dialog>

    <!-- Payment Modal -->
    <dialog id="payment-modal" class="modal" @if ($showPaymentModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-md" wire:submit.prevent="savePayment">
            <h3 class="font-bold text-lg mb-4">Add Payment</h3>
            
            @if($selectedOrder)
                <div class="mb-4 p-3 bg-base-200 rounded">
                    <div><strong>Order:</strong> {{ $selectedOrder->order_number }}</div>
                    <div><strong>Customer:</strong> {{ $selectedOrder->customer->name }}</div>
                    <div><strong>Total Due:</strong> {{ number_format($this->getOrderTotal($selectedOrder) - $this->getTotalPaid($selectedOrder), 2) }}</div>
                </div>
            @endif

            <div class="mb-4">
                <label class="label">Amount *</label>
                <input wire:model="paymentAmount" type="number" step="0.01" min="0.01"
                       class="input input-bordered w-full" placeholder="Enter amount" />
                @error('paymentAmount')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">Payment Method *</label>
                <select wire:model="paymentMethod" class="select select-bordered w-full">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="credit_card">Credit Card</option>
                </select>
                @error('paymentMethod')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">Payment Date *</label>
                <input wire:model="paymentDate" type="date" class="input input-bordered w-full" />
                @error('paymentDate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">Notes</label>
                <textarea wire:model="paymentNotes" class="textarea textarea-bordered w-full" 
                          placeholder="Payment notes"></textarea>
            </div>

            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="closePaymentModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Payment</button>
            </div>
        </form>
    </dialog>

    <!-- Delivery Modal -->
    <dialog id="delivery-modal" class="modal" @if ($showDeliveryModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-md" wire:submit.prevent="saveDelivery">
            <h3 class="font-bold text-lg mb-4">Add Delivery</h3>
            
            @if($selectedOrder)
                <div class="mb-4 p-3 bg-base-200 rounded">
                    <div><strong>Order:</strong> {{ $selectedOrder->order_number }}</div>
                    <div><strong>Customer:</strong> {{ $selectedOrder->customer->name }}</div>
                    {{-- <div><strong>Product:</strong> {{ $selectedOrder->items->first()->product->name ?? 'N/A' }}</div> --}}
                </div>
            @endif

            <div class="mb-4">
                <label class="label">Quantity *</label>
                <input wire:model="deliveryQuantity" type="number" step="0.01" min="0.01"
                       class="input input-bordered w-full" placeholder="Enter quantity" />
                @error('deliveryQuantity')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">Delivery Date *</label>
                <input wire:model="deliveryDate" type="date" class="input input-bordered w-full" />
                @error('deliveryDate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">Notes</label>
                <textarea wire:model="deliveryNotes" class="textarea textarea-bordered w-full" 
                          placeholder="Delivery notes"></textarea>
            </div>

            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="closeDeliveryModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Delivery</button>
            </div>
        </form>
    </dialog>
</section> 