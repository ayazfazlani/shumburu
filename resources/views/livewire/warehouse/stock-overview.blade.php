<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">Finished Goods Stock</h2>
            <p class="text-zinc-500 dark:text-zinc-400">Real-time inventory of produced goods available for dispatch.</p>
        </div>
        
        <div class="w-72">
            <input wire:model.live="search" type="text" placeholder="Search by product or batch..." class="input input-bordered w-full" />
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch Number</th>
                        <th>Total Physical</th>
                        <th>Reserved</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stocks as $stock)
                        <tr>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $stock->product->name }}</span>
                                    <span class="text-xs text-zinc-500">{{ $stock->product->code }}</span>
                                </div>
                            </td>
                            
                            <td>
                                <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded text-xs font-mono">
                                    {{ $stock->batch_number ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                <span class="font-semibold text-zinc-600 dark:text-zinc-400">
                                    {{ number_format($stock->quantity, 2) }}
                                </span>
                            </td>

                            <td>
                                <span class="font-semibold text-orange-600 dark:text-orange-400">
                                    {{ number_format($stock->reserved_quantity, 2) }}
                                </span>
                            </td>

                            <td>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($stock->available_quantity, 2) }}
                                </span>
                            </td>

                            <td>
                                @if($stock->status === 'available')
                                    <span class="badge badge-success">Available</span>
                                @elseif($stock->status === 'reserved')
                                    <span class="badge badge-warning">Reserved</span>
                                @elseif($stock->status === 'dispatched')
                                    <span class="badge badge-ghost">Dispatched</span>
                                @else
                                    <span class="badge badge-info">{{ ucfirst($stock->status) }}</span>
                                @endif
                            </td>

                            <td>{{ $stock->location ?? 'Main Warehouse' }}</td>
                            
                            <td>
                                <span class="text-xs text-zinc-500">
                                    {{ $stock->updated_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-zinc-500">
                                No stock found in inventory.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
            {{ $stocks->links() }}
        </div>
    </div>
</div>
