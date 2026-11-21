@extends('layouts.admin')

@section('title', 'Edit Destination')
@section('page_title', 'Edit Destination')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $destination->name }}</h2>
            <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.destinations._form')
        </form>
    </div>
@endsection
