@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page_title', 'Edit Tour Category')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $category->name }}</h2>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
@endsection
