@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-end">
        <div class="join">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="join-item btn btn-disabled">«</span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="join-item btn">
                    «
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="join-item btn btn-disabled">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="join-item btn btn-active">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="join-item btn">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="join-item btn">
                    »
                </button>
            @else
                <span class="join-item btn btn-disabled">»</span>
            @endif
        </div>
    </nav>
@endif
