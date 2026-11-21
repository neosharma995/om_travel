@extends('layouts.admin')

@section('title', 'Write Article')
@section('page_title', 'Write Blog Article')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Blog Article</h2>
            <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.blog-posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.blog-posts._form')
        </form>
    </div>
@endsection
