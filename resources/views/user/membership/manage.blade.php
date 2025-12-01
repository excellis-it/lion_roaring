@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Membership Tiers
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-8">
                    <h3 class="mb-3">Membership Tiers</h3>
                </div>
                <div class="col-md-4 text-end">
                    @if (auth()->user()->can('Create Membership'))
                        <a href="{{ route('user.membership.create') }}" class="btn btn-primary me-2">+ Add Tier</a>
                    @endif
                    @if (auth()->user()->can('View Membership Settings'))
                        <a href="{{ route('user.membership.settings') }}" class="btn btn-secondary">Settings</a>
                    @endif
                </div>
            </div>
            <div class="row">
                @foreach ($tiers as $tier)
                    <div class="col-md-4 mb-3">
                        <div class="card p-3 h-100">
                            <h5>{{ $tier->name }}</h5>
                            <p class="text-muted">{{ $tier->description }}</p>
                            <p><strong>Cost:</strong> {{ $tier->cost }} {{ $measurement->label ?? '' }}</p>
                            <ul class="mb-3">
                                @foreach ($tier->benefits as $b)
                                    <li>{{ $b->benefit }}</li>
                                @endforeach
                            </ul>
                            <div class="d-flex justify-content-between mt-auto">
                                @if (auth()->user()->can('Edit Membership'))
                                    <a href="{{ route('user.membership.edit', $tier->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if (auth()->user()->can('Delete Membership'))
                                    <a href="{{ route('user.membership.delete', $tier->id) }}" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete?')">Delete</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
