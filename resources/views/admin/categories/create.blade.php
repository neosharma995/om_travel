@extends('layouts.admin')

@section('title', 'Create Category')
@section('page_title', 'Add Tour Category')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Tour Category</h2>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
@endsection
