@extends('layouts.app')

@push('title', 'Create New Team')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form method="POST" action="{{ route('teams.store') }}">
                    @csrf
                    <div class="card-body">
                        <label>Team Name</label>
                        <div class="form-group">
                            <input type="text" name="name" required
                                   class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   value="{{ old('name') }}"
                                   placeholder="Enter Team Name">

                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <label>Owner</label>
                        <div class="form-group">
                            <select name="user_id" required
                                    class="form-control {{ $errors->has('user_id') ? ' is-invalid' : '' }}">
                                <option value="" disabled selected>
                                    Select Team Owner
                                </option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                            {{ (old('user_id') == $user->id ? 'selected' : '') }}>
                                        {{ $user->name }}({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('user_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('user_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <label>Phone</label>
                        <div class="form-group">
                            <input type="tel" name="phone" required
                                   class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                   value="{{ old('phone') }}"
                                   placeholder="Enter Phone Number">

                            @if ($errors->has('phone'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-round">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
