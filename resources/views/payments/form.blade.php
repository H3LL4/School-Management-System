@extends('layouts.school')
@section('title', $payment->exists ? 'Edit payment' : 'Record payment')
@section('content')
<h1 class="h3 mb-3">{{ $payment->exists ? 'Edit payment ' . $payment->reference : 'Record payment' }}</h1>
<div class="card" style="max-width:700px"><div class="card-body p-4">
    <form method="POST" action="{{ $payment->exists ? route('payments.update', $payment) : route('payments.store') }}">
        @csrf
        @if ($payment->exists) @method('PUT') @endif

        @if ($payment->exists)
            <dl class="row mb-3">
                <dt class="col-sm-3">Student</dt><dd class="col-sm-9">{{ $payment->enrollment->student->name }}</dd>
                <dt class="col-sm-3">Course</dt><dd class="col-sm-9">{{ $payment->enrollment->course->code }} · {{ $payment->enrollment->semester->name }}</dd>
                <dt class="col-sm-3">Tuition fee</dt><dd class="col-sm-9">${{ number_format($payment->enrollment->course->tuition_fee, 2) }}</dd>
            </dl>
        @else
            <div class="mb-3">
                <label for="enrollment_id" class="form-label">Enrollment <small class="text-muted">(enrolled students only)</small></label>
                <select id="enrollment_id" name="enrollment_id" class="form-select @error('enrollment_id') is-invalid @enderror" required>
                    <option value="">Select enrollment…</option>
                    @foreach ($enrollments as $e)
                        @php $due = max(0, (float) $e->course->tuition_fee - (float) $e->paid_total); @endphp
                        <option value="{{ $e->id }}" @selected((string) old('enrollment_id', $payment->enrollment_id) === (string) $e->id)>
                            {{ $e->student->name }} — {{ $e->course->code }} ({{ $e->semester->name }}) · fee ${{ number_format($e->course->tuition_fee, 2) }} · owes ${{ number_format($due, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('enrollment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endif

        <div class="row">
            <x-field class="col-md-6" name="amount" label="Amount ($)" type="number" :value="$payment->amount" step="0.01" min="0.01" required />
            <x-field class="col-md-6" name="paid_at" label="Payment date" type="date" :value="$payment->paid_at?->format('Y-m-d')" required />
            <x-select class="col-md-6" name="method" label="Method" :options="array_combine(\App\Models\Payment::METHODS, \App\Models\Payment::METHODS)" :selected="$payment->method" placeholder="Select method…" required />
            <x-select class="col-md-6" name="status" label="Status" :options="array_combine(\App\Models\Payment::STATUSES, \App\Models\Payment::STATUSES)" :selected="$payment->status" placeholder="Select status…" required />
        </div>
        <x-field name="note" label="Note (optional)" :value="$payment->note" />
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
