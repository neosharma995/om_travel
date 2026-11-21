@extends('layouts.admin')

@section('title', 'Edit Tour')
@section('page_title', 'Edit Tour Package')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $tour->title }}</h2>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.tours.update', $tour) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.tours._form')
        </form>
    </div>
@endsection
