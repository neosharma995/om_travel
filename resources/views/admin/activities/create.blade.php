@extends('layouts.admin')

@section('title', 'Create Activity')
@section('page_title', 'Add Activity')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Activity</h2>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.activities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.activities._form')
        </form>
    </div>
@endsection
