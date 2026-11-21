@extends('layouts.admin')

@section('title', 'Edit Testimonial')
@section('page_title', 'Edit Testimonial')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Edit: {{ $testimonial->client_name }}</h2>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
        </div>

        <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.testimonials._form')
        </form>
    </div>
@endsection
