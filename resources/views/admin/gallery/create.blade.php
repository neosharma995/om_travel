@extends('layouts.admin')

@section('title', 'Add Gallery Photo')
@section('page_title', 'Add Gallery Photo')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Gallery Photo</h2>
            <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.gallery._form')
        </form>
    </div>
@endsection
