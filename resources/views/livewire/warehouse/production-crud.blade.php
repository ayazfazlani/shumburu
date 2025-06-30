<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Production Records') }}
                </h2>
                <a href="{{ route('warehouse.production.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Add Production') }}
                </a>
            </div>

            <!-- Filters -->
            <div class="mt-6 bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                        <input wire:model.live="search" type="text" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="{{ __('Search by reference or line...') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Production Line') }}</label>
                        <select wire:model.live="selectedLine" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Lines') }}</option>
                            @foreach($productionLines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Product') }}</label>
                        <select wire:model.live="selectedProduct" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Products') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Date From') }}</label>
                        <input wire:model.live="dateFrom" type="date" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Date To') }}</label>
                        <input wire:model.live="dateTo" type="date" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Production Table -->
            <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Raw Material') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Production Line') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Product') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Shift') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Finished Goods') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Scraps') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productions as $production)
                            {{-- {{dd($productions)}} --}}
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $production->materialStockOut->rawMaterial->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $production->productionLine->name ?? 'N/A' }}
                                    </td>
                                    @php
                                    $matchingFinishedGood = $production->finishedGoods->first();
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $matchingFinishedGood && $matchingFinishedGood->product ? $matchingFinishedGood->product->name : 'N/A' }}
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $production->shift }}
                                    </td>
                                    {{-- <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="space-y-1">
                                            @foreach($production->productionLengths as $length)
                                                <div class="text-xs">
                                                    <span class="font-medium">{{ $length->type }}:</span>
                                                    {{ $length->quantity }} x {{ $length->length_m }}m
                                                    ({{ number_format($length->total_weight, 2) }}kg)
                                                </div>
                                            @endforeach
                                        </div>
                                    </td> --}}
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="space-y-1">
                                            @foreach($production->scrapWastes as $scrap)
                                                <div class="text-xs">
                                                    <span class="font-medium">{{ $scrap->quantity }}kg:</span>
                                                    {{ $scrap->reason }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $production->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="viewProduction({{ $production->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                {{ __('View') }}
                                            </button>
                                            <button wire:click="editProduction({{ $production->id }})"
                                                    class="text-green-600 hover:text-green-900">
                                                {{ __('Edit') }}
                                            </button>
                                            <button wire:click="deleteProduction({{ $production->id }})"
                                                    wire:confirm="{{ __('Are you sure you want to delete this production record?') }}"
                                                    class="text-red-600 hover:text-red-900">
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('No production records found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $productions->links() }}
                </div>
            </div>
        </div>
    </div>
</div> 