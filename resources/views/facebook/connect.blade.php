@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>Connect Your Facebook Page</h2>
    <a href="{{ route('facebook.connect') }}" class="btn btn-primary">
        Connect with Facebook
    </a>
</div>
@endsection
