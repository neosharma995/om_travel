@extends('layouts.admin')

@section('title', 'Add Tour Guide')
@section('page_title', 'Add Tour Guide')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Tour Guide</h2>
            <a href="{{ route('admin.guides.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.guides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.guides._form')
        </form>
    </div>
@endsection
