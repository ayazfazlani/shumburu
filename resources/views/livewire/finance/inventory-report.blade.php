<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Inventory Report</h1>
    <div class="flex gap-2 mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search products..."
            class="input input-bordered" />
        <select wire:model="perPage" class="select select-bordered">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock In</th>
                    <th>Stock Out</th>
                    <th>Finished Goods</th>
                    <th>Current Inventory</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventory as $row)
                    <tr>
                        <td>{{ $row['product']->name }}</td>
                        <td>{{ $row['stock_in'] }}</td>
                        <td>{{ $row['stock_out'] }}</td>
                        <td>{{ $row['finished_goods'] }}</td>
                        <td class="font-bold">{{ $row['current'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
