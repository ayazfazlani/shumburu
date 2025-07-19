<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <x-app-logo class="w-16 h-16" />
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs">Daily Raw Material Stock Balance</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document No.</span> PR/OF/012</div>
            <div><span class="font-semibold">Effective Date:</span> {{ $date }}</div>
        </div>
    </div>
    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
        <div class="flex items-center gap-2">
            <span class="font-semibold">Filters:</span>
            <input type="date" wire:model.live="date" class="border rounded px-2 py-1 text-xs" />
            <select wire:model.live="raw_material_id" class="border rounded px-2 py-1 text-xs">
                <option value="">All Raw Materials</option>
                @foreach($allMaterials as $material)
                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="overflow-x-auto mt-2">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-black">S/NO</th>
                    <th class="border border-black">Raw material name</th>
                    <th class="border border-black">Begning balance (kg)</th>
                    <th class="border border-black">Addition (kg)</th>
                    <th class="border border-black">OUT (kg)</th>
                    <th class="border border-black">Return (kg)</th>
                    <th class="border border-black">Ending Balance (kg)</th>
                    <th class="border border-black">Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                    <tr>
                        <td class="border border-black">{{ $i + 1 }}</td>
                        <td class="border border-black">{{ $row['name'] }}</td>
                        <td class="border border-black">{{ $row['beginning'] }}</td>
                        <td class="border border-black">{{ $row['addition'] }}</td>
                        <td class="border border-black">{{ $row['out'] }}</td>
                        <td class="border border-black">{{ $row['return'] }}</td>
                        <td class="border border-black">{{ $row['ending'] }}</td>
                        <td class="border border-black">{{ $row['remark'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-xs">
        <div class="mb-2">Remarks and calculations as per your sample can go here.</div>
        <div class="mb-2">65% scrap 140*30=4200kg send to ferida</div>
        <div class="mb-2">440kg is D=20% & V=60% & 65%pv=20% and ... (add more as needed)</div>
    </div>
    <div class="mt-6 flex flex-wrap justify-between text-xs">
        <div>
            <div class="mb-1">Prepared by <span class="underline">Hana</span></div>
            <div>Checked by <span class="underline">Minch</span></div>
            <div>Approved by <span class="underline">Minch A.</span></div>
        </div>
        <div class="text-right">
            <div>Date <span class="underline">{{ $date }}</span></div>
            <div>Date <span class="underline">{{ $date }}</span></div>
            <div>Date <span class="underline">{{ $date }}</span></div>
        </div>
    </div>
</div> 