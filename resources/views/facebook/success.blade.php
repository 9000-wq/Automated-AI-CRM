@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Pages Connected Successfully</h3>

    @if(!empty($pages))
        <ul>
            @foreach($pages as $page)
                <li><strong>{{ $page['name'] }}</strong> (ID: {{ $page['id'] }})</li>
            @endforeach
        </ul>
    @else
        <p>No pages found.</p>
    @endif
</div>
@endsection
