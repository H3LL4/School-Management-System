@extends('layouts.school')
@section('title', 'Not allowed')
@section('content')
<div class="card text-center mx-auto" style="max-width:520px"><div class="card-body p-5">
    <div class="confirm-icon" style="background:#fff3cf;color:#a87500"><i class="bi bi-shield-lock"></i></div>
    <h1 class="h4 mb-2">You can't open this page</h1>
    <p class="text-muted mb-4">{{ $exception->getMessage() ?: 'Your role does not have permission for this action.' }}</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to dashboard</a>
</div></div>
@endsection
