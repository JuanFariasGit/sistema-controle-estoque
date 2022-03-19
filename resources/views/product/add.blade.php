@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" enctype="multipart/form-data">
        @csrf
        <div class="d-flex flex-column align-items-center">
            <h1>ADICIONAR PRODUTO</h1>
            @include('product._form')
        </div>
    </form>
</div>
@endsection