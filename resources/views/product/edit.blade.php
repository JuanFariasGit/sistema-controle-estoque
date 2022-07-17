@extends('layouts.app')
@section('title', 'Editar Produto')

@section('content')
<div class="container">
    <form method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ isset($product->id) ? $product->id : '' }}">
        <div class="d-flex flex-column align-items-center">
            <h1>EDITAR PRODUTO</h1>
            @include('product._form')
        </div>
    </form>
</div>
@endsection