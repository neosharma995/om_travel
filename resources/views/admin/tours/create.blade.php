@extends('layouts.admin')

@section('title', 'Create Tour')
@section('page_title', 'Add Tour Package')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Tour Package</h2>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.tours._form')
        </form>
    </div>
@endsection
