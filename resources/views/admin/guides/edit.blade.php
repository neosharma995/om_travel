@extends('layouts.admin')

@section('title', 'Edit Tour Guide')
@section('page_title', 'Edit Tour Guide')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $guide->name }}</h2>
            <a href="{{ route('admin.guides.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.guides.update', $guide) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.guides._form')
        </form>
    </div>
@endsection
