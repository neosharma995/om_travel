@extends('layouts.admin')

@section('title', 'Edit Tour Type')
@section('page_title', 'Edit Tour Type')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $tourType->name }}</h2>
            <a href="{{ route('admin.tour-types.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.tour-types.update', $tourType) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.tour-types._form')
        </form>
    </div>
@endsection
