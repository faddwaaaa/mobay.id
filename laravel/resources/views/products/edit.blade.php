@extends('layouts.dashboard')

@section('content')

@include('products._form', ['product' => $product])

@endsection
