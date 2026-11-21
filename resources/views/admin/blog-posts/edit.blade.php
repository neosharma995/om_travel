@extends('layouts.admin')

@section('title', 'Edit Article')
@section('page_title', 'Edit Blog Article')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $post->title }}</h2>
            <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.blog-posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.blog-posts._form')
        </form>
    </div>
@endsection
