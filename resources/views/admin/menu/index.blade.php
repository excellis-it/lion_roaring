@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Menu Names
@endsection
@section('head')
    Menu Names
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('admin.menu.update') }}" method="post">
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>Menu Names</h4>
                        </div>
                        <div class="row justify-content-between">
                            @foreach ($items as $item)
                                <div class="col-md-6">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="name_{{ $item->key }}">{{ $item->default_name }}</label>
                                            <input type="text" class="form-control" id="name_{{ $item->key }}"
                                                name="names[{{ $item->key }}]"
                                                value="{{ old('names.' . $item->key, $item->name ?? $item->default_name) }}"
                                                placeholder="{{ $item->default_name }}">
                                            @if ($errors->has('names.' . $item->key))
                                                <div class="error" style="color:red;">
                                                    {{ $errors->first('names.' . $item->key) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
