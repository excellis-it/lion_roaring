@extends('user.layouts.master')
@section('title')
    Bulletin Board - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="messaging_sec">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading_hp">
                        <h2>Bulletin Board</h2>
                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Bulletin Chat</h2>
                </div>
                <div class="bulletin_board" id="show-bulletin">
                    @include('user.bulletin-board.show-bulletin')
                  

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
