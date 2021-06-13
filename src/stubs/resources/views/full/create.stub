@extends('layouts.app')

@section('title', __('master.create'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ __('master.create') }}</div>
            <form method="POST" action="{{ route('masters.store') }}" accept-charset="UTF-8">
                {{ csrf_field() }}
                <div class="card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('master.title') }} <span class="form-required">*</span></label>
                        <input id="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" required>
                        {!! $errors->first('title', '<span class="invalid-feedback" role="alert">:message</span>') !!}
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">{{ __('master.description') }}</label>
                        <textarea id="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" rows="4">{{ old('description') }}</textarea>
                        {!! $errors->first('description', '<span class="invalid-feedback" role="alert">:message</span>') !!}
                    </div>
                </div>
                <div class="card-footer">
                    <input type="submit" value="{{ __('app.create') }}" class="btn btn-success">
                    <a href="{{ route('masters.index') }}" class="btn btn-link">{{ __('app.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
