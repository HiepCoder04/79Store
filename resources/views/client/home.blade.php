@extends('client.layouts.default')


<style>
.product-img img {
    width: 100%;
    height: 250px;
    /* hoặc chiều cao tùy ý */
    object-fit: cover;
    object-position: center;
    border-radius: 8px;
}
</style>
@section('title', 'Trang chủ')

@section('content')
@include('client.layouts.banner')
@include('client.layouts.services')
@include('client.layouts.about')
@include('client.layouts.testimonial')
@include('client.layouts.products')
@include('client.layouts.vouchers')
@include('client.layouts.blog')
@include('client.layouts.subscribe')


@endsection