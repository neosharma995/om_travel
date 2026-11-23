@extends('layouts.admin')

@section('title', 'Edit Activity')
@section('page_title', 'Edit Activity')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $activity->name }}</h2>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.activities._form')
        </form>
    </div>
@endsection
