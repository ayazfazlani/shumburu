<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Production Entry</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label>Production Line</label>
                <select wire:model="production_line_id" class="input">
                    <option value="">Select Line</option>
                    @foreach ($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Product</label>
                <select wire:model="product_id" class="input">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><label>Shift</label><input type="text" wire:model="shift" class="input"></div>
        </div>
        <div>
            <label>Notes</label>
            <textarea wire:model="notes" class="input w-full"></textarea>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Raw Materials Used</h2>
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Stock Out Batch</th>
                        <th>Quantity Used</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stockOutUsages as $index => $usage)
                        <tr>
                            <td>
                                <select wire:model="stockOutUsages.{{ $index }}.stock_out_line_id" class="input">
                                    <option value="">Select Batch</option>
                                    @foreach ($stockOutLines as $line)
                                        <option value="{{ $line->id }}">
                                            Batch #{{ $line->materialStockOut->batch_number }} | {{ $line->materialStockOut->rawMaterial->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" min="0.001" step="0.001" wire:model="stockOutUsages.{{ $index }}.quantity_used" class="input">
                            </td>
                            <td>
                                <button type="button" wire:click="removeStockOutUsage({{ $index }})" class="btn btn-danger">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" wire:click="addStockOutUsage" class="btn btn-primary mt-2">Add More</button>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Finished Goods (Rolls/Cuts)</h2>
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Length (m)</th>
                        <th>Quantity</th>
                        <th>Outer Diameter</th>
                        <th>Size</th>
                        <th>Surface</th>
                        <th>Thickness</th>
                        <th>Ovality</th>
                        <th>Batch #</th>
                        <th>Production Date</th>
                        <th>Purpose</th>
                        <th>Customer</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($finishedGoods as $index => $fg)
                        <tr>
                            <td>
                                <select wire:model="finishedGoods.{{ $index }}.type" class="input">
                                    <option value="roll">Roll</option>
                                    <option value="cut">Cut</option>
                                </select>
                            </td>
                            <td><input type="number" wire:model="finishedGoods.{{ $index }}.length_m" class="input"></td>
                            <td><input type="number" wire:model="finishedGoods.{{ $index }}.quantity" class="input"></td>
                            <td><input type="number" wire:model="finishedGoods.{{ $index }}.outer_diameter" class="input"></td>
                            <td><input type="text" wire:model="finishedGoods.{{ $index }}.size" class="input"></td>
                            <td><input type="text" wire:model="finishedGoods.{{ $index }}.surface" class="input"></td>
                            <td><input type="number" wire:model="finishedGoods.{{ $index }}.thickness" class="input"></td>
                            <td><input type="number" wire:model="finishedGoods.{{ $index }}.ovality" class="input"></td>
                            <td><input type="text" wire:model="finishedGoods.{{ $index }}.batch_number" class="input"></td>
                            <td><input type="date" wire:model="finishedGoods.{{ $index }}.production_date" class="input"></td>
                            <td>
                                <select wire:model="finishedGoods.{{ $index }}.purpose" class="input">
                                    <option value="for_stock">For Stock</option>
                                    <option value="for_customer_order">For Customer Order</option>
                                </select>
                            </td>
                            <td>
                                <select wire:model="finishedGoods.{{ $index }}.customer_id" class="input">
                                    <option value="">N/A</option>
                                    {{-- @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach --}}
                                </select>
                            </td>
                            <td><input type="text" wire:model="finishedGoods.{{ $index }}.notes" class="input"></td>
                            <td>
                                <button type="button" wire:click="removeFinishedGood({{ $index }})" class="btn btn-danger">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" wire:click="addFinishedGood" class="btn btn-primary mt-2">Add Finished Good</button>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Scrap/Waste</h2>
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Stock Out Batch</th>
                        <th>Scrap Quantity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stockOutScraps as $index => $scrap)
                        <tr>
                            <td>
                                <select wire:model="stockOutScraps.{{ $index }}.stock_out_line_id" class="input">
                                    <option value="">Select Batch</option>
                                    @foreach ($stockOutLines as $line)
                                        <option value="{{ $line->id }}">
                                            Batch #{{ $line->materialStockOut->batch_number }} | {{ $line->materialStockOut->rawMaterial->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" min="0.001" step="0.001" wire:model="stockOutScraps.{{ $index }}.quantity_scrapped" class="input">
                            </td>
                            <td>
                                <button type="button" wire:click="removeStockOutScrap({{ $index }})" class="btn btn-danger">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" wire:click="addStockOutScrap" class="btn btn-primary mt-2">Add More Scrap</button>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Save Production Entry</button>
        </div>
    </form>
    @if (session()->has('success'))
        <div class="alert alert-success mt-4">{{ session('success') }}</div>
    @endif
</div>
