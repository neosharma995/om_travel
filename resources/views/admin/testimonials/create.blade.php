@extends('layouts.admin')

@section('title', 'Add Testimonial')
@section('page_title', 'Add Testimonial')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">New Testimonial</h2>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.testimonials._form')
        </form>
    </div>
@endsection
