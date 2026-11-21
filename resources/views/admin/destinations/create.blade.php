@extends('layouts.admin')

@section('title', 'Create Destination')
@section('page_title', 'Add Destination')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Destination</h2>
            <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.destinations._form')
        </form>
    </div>
@endsection
