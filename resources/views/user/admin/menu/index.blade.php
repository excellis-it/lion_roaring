@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Menu Names
@endsection

@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Menu Names</h3>
                    <p class="text-muted small mb-0">Menu Names</p>
                </div>
            </div>
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
                                  <button type="submit" class="print_btn me-2 mt-2">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
