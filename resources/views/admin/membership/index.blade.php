@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Membership Tiers
@endsection
@section('head')
    Membership Management
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <div class="form-head d-flex justify-content-between align-items-center">
                        <h4>Membership Tiers</h4>
                        <div>

                           
                            <a href="{{ route('admin.membership.create') }}" class="btn btn-primary">Add Tier</a>
                            <a href="{{ route('admin.membership.settings') }}" class="btn btn-secondary">Measurement
                                Settings</a>
                        </div>
                    </div>
                    <div class="row mt-3">
                        @foreach ($tiers as $tier)
                            <div class="col-md-4 mb-3">
                                <div class="card p-3">
                                    <h5>{{ $tier->name }}</h5>
                                    <p>{{ $tier->description }}</p>
                                    <p><strong>Cost:</strong> {{ $tier->cost }}</p>
                                    <ul>
                                        @foreach ($tier->benefits as $b)
                                            <li>{{ $b->benefit }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.membership.edit', $tier->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <a href="{{ route('admin.membership.delete', $tier->id) }}"
                                            class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
