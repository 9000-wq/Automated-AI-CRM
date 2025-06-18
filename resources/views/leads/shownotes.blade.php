<div class="container my-4">
    
    <div id="note-list" class="mt-4">
        @include('leads.note_items', ['notes' => $notes])
    </div>

    @if ($notes->hasMorePages())
        <div class="text-center mt-3">
        
            <button id="load-more" class="btn btn-outline-primary " data-page="{{ $pageno ? $pageno + 1 : 2 }}" data-lead="{{ $leadId }}">
                Load More 
            </button>

        </div>
    @endif
</div>
