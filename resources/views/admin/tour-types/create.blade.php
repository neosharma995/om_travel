@extends('layouts.admin')

@section('title', 'Create Tour Type')
@section('page_title', 'Add Tour Type')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Tour Type</h2>
            <a href="{{ route('admin.tour-types.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.tour-types.store') }}" method="POST">
            @csrf
            @include('admin.tour-types._form')
        </form>
    </div>
@endsection
