@if(count($leads) > 0)
@foreach($leads as $lead)
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">{{$lead->name}}</h5>
            <p class="card-text">{{$lead->source}}</p>
            <p class="card-text">{{$lead->case_ref}}</p>
            <p style="text-align: end;">{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
    </div>
@endforeach

<div class="d-flex justify-content-between">
    <button class="btn btn-primary prev-page" data-status="{{ $status }}" data-page="{{ $leads->currentPage() - 1 }}" {{ $leads->onFirstPage() ? 'disabled' : '' }}><i class="fas fa-arrow-circle-left"></i></button>
    <button class="btn  btn-primary next-page" data-status="{{ $status }}" data-page="{{ $leads->currentPage() + 1 }}" {{ $leads->currentPage() == $leads->lastPage() ? 'disabled' : '' }}><i class="fas fa-arrow-circle-right"></i></button>
</div>
@endif
