@extends('layouts.admin')

@section('title', 'Edit Gallery Photo')
@section('page_title', 'Edit Gallery Photo')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $item->title }}</h2>
            <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.gallery.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.gallery._form')
        </form>
    </div>
@endsection
