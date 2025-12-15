@extends('user.layouts.master')
@section('title')
    Store Dashboard - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row mb-3">
                            <div class="col-md-10">
                                <!-- <h3 class="mb-3">Ecclesias List</h3> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <h3 class="mb-3 float-left">Store Dashboard</h3>
                            </div>
                            <div class="col-md-2">
                                <!-- Additional content or buttons can go here -->
                            </div>
                        </div>
                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card store-card">
                                    <div class="card-body">
                                        <div class="flex-one d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                                                <div class="ms-3">
                                                    <h4 class="mb-0">Total Pages</h4>
                                                    <p class="mb-0">{{ $count['pages'] }}</p>
                                                </div>
                                            </div>
                                            <div class="view-btn ms-3">
                                                <a href="{{ route('user.store-cms.list') }}" class="btn btn-primary">View All</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card store-card">
                                    <div class="card-body">
                                        <div class="flex-one d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users fa-2x text-success"></i>
                                                <div class="ms-3">
                                                    <h4 class="mb-0">Newsletter</h4>
                                                    <p class="mb-0">{{ $count['newsletter'] }}</p>
                                                </div>
                                            </div>
                                            <div class="view-btn ms-3">
                                                <a href="{{ route('user.newsletters.index') }}" class="btn btn-primary">View
                                                    All</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
