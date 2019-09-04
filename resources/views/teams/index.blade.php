@extends('layouts.app')

@push('title', 'Teams')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a class="btn btn-round btn-success" href="{{ route('teams.create') }}">
                        Create New Team
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                            <th>ID</th>
                            <th>UID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Owner</th>
                            <th class="text-right">Created At</th>
                            <th class="text-right">Actions</th>
                            </thead>
                            <tbody>
                            @foreach($teams as $team)
                                <tr>
                                    <td>{{ $team->id }}</td>
                                    <td>{{ $team->uid }}</td>
                                    <td>{{ $team->name }}</td>
                                    <td>{{ $team->slug }}</td>
                                    <td>{{ $team->owner->name }} ({{$team->owner->email}})</td>
                                    <td class="text-right">{{ $team->created_at->toDateString() }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('teams.edit', ['team' => $team->uid]) }}"
                                            class="btn btn-round btn-warning btn-icon btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ route('template_messages.index', ['team' => $team->uid]) }}"
                                            class="btn btn-round btn-primary btn-icon btn-sm">
                                            <i class="fa fa-comment"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $teams->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection