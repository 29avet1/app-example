@extends('layouts.app')

@push('title', 'Update Team')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form method="POST" action="{{ route('teams.update', ['team' => $team->uid]) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <label>Phone</label>
                        <div class="form-group">
                            <input type="text" name="phone" required
                                   class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                   value="{{ old('phone') ?? $whatsappAccount->phone }}"
                                   placeholder="Enter Phone Number">

                            @if ($errors->has('phone'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>
                        <label>Whatsapp Template Namespace</label>
                        <div class="form-group">
                            <input type="text" name="template_namespace" required
                                   class="form-control {{ $errors->has('template_namespace') ? ' is-invalid' : '' }}"
                                   value="{{ old('template_namespace') ?? $whatsappAccount->template_namespace }}"
                                   placeholder="Enter Template Namespace">

                            @if ($errors->has('template_namespace'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('template_namespace') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-round">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
