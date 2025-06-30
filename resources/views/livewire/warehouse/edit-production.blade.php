<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Production Record') }}
                </h2>
                <button wire:click="cancel" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Cancel') }}
                </button>
            </div>

            <form wire:submit="update" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Production Information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="production_line_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Production Line') }} *
                            </label>
                            <select wire:model="production_line_id" id="production_line_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Production Line') }}</option>
                                @foreach($lines as $line)
                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                @endforeach
                            </select>
                            @error('production_line_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Product') }} *
                            </label>
                            <select wire:model="product_id" id="product_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="material_stock_out_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Material Stock Out') }} *
                            </label>
                            <select wire:model="material_stock_out_id" id="material_stock_out_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Stock Out') }}</option>
                                @foreach($stockOuts as $stockOut)
                                    <option value="{{ $stockOut->id }}">{{ $stockOut->reference_number }}</option>
                                @endforeach
                            </select>
                            @error('material_stock_out_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="shift" class="block text-sm font-medium text-gray-700">
                                {{ __('Shift') }} *
                            </label>
                            <input wire:model="shift" type="text" id="shift" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('e.g., Morning, Evening, Night') }}">
                            @error('shift') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="size" class="block text-sm font-medium text-gray-700">
                                {{ __('Size') }} *
                            </label>
                            <input wire:model="size" type="text" id="size" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('e.g., 50mm x 50mm') }}">
                            @error('size') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="surface" class="block text-sm font-medium text-gray-700">
                                {{ __('Surface') }}
                            </label>
                            <input wire:model="surface" type="text" id="surface"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('Surface finish') }}">
                            @error('surface') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="thickness" class="block text-sm font-medium text-gray-700">
                                {{ __('Thickness') }}
                            </label>
                            <input wire:model="thickness" type="text" id="thickness"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('e.g., 2.5mm') }}">
                            @error('thickness') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="outer_diameter" class="block text-sm font-medium text-gray-700">
                                {{ __('Outer Diameter') }}
                            </label>
                            <input wire:model="outer_diameter" type="text" id="outer_diameter"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('e.g., 100mm') }}">
                            @error('outer_diameter') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="ovality" class="block text-sm font-medium text-gray-700">
                                {{ __('Ovality') }}
                            </label>
                            <input wire:model="ovality" type="text" id="ovality"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="{{ __('Ovality measurement') }}">
                            @error('ovality') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Finished Goods -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Finished Goods') }}</h3>
                        <button type="button" wire:click="addFinishedGood"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add Finished Good') }}
                        </button>
                    </div>

                    @foreach($finishedGoods as $index => $finishedGood)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-md font-medium text-gray-700">{{ __('Finished Good') }} #{{ $index + 1 }}</h4>
                                @if(count($finishedGoods) > 1)
                                    <button type="button" wire:click="removeFinishedGood({{ $index }})"
                                            class="text-red-600 hover:text-red-900">
                                        {{ __('Remove') }}
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Type') }} *
                                    </label>
                                    <select wire:model="finishedGoods.{{ $index }}.type" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="roll">{{ __('Roll') }}</option>
                                        <option value="cut">{{ __('Cut') }}</option>
                                    </select>
                                    @error("finishedGoods.{$index}.type") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Length (m)') }} *
                                    </label>
                                    <input wire:model="finishedGoods.{{ $index }}.length_m" type="number" step="0.01" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="{{ __('Length in meters') }}">
                                    @error("finishedGoods.{$index}.length_m") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Quantity') }} *
                                    </label>
                                    <input wire:model="finishedGoods.{{ $index }}.quantity" type="number" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="{{ __('Number of pieces') }}">
                                    @error("finishedGoods.{$index}.quantity") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Scraps -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Scrap/Waste') }}</h3>
                        <button type="button" wire:click="addScrap"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add Scrap') }}
                        </button>
                    </div>

                    @foreach($scraps as $index => $scrap)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-md font-medium text-gray-700">{{ __('Scrap') }} #{{ $index + 1 }}</h4>
                                @if(count($scraps) > 1)
                                    <button type="button" wire:click="removeScrap({{ $index }})"
                                            class="text-red-600 hover:text-red-900">
                                        {{ __('Remove') }}
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Quantity (kg)') }}
                                    </label>
                                    <input wire:model="scraps.{{ $index }}.quantity" type="number" step="0.01"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="{{ __('Weight in kg') }}">
                                    @error("scraps.{$index}.quantity") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Reason') }}
                                    </label>
                                    <input wire:model="scraps.{{ $index }}.reason" type="text"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="{{ __('Reason for scrap') }}">
                                    @error("scraps.{$index}.reason") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Notes') }}
                                    </label>
                                    <input wire:model="scraps.{{ $index }}.notes" type="text"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="{{ __('Additional notes') }}">
                                    @error("scraps.{$index}.notes") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="cancel"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Update Production') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 