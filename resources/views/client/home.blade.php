@extends('client.layouts.default')

@section('title', 'Trang chủ')

@section('content')
@include('client.layouts.banner')
@include('client.layouts.services')
@include('client.layouts.about')
@include('client.layouts.testimonial')
@include('client.layouts.products')
@include('client.layouts.blog')
@include('client.layouts.subscribe')
@endsection