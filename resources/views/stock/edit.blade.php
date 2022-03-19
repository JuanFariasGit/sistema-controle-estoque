@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ isset($movement->id) ? $movement->id : '' }}">
        <div class="d-flex flex-column align-items-center">
            <h1>EDITAR MOVIMENTAÇÃO</h1>
            @include('stock._form')
        </div>
    </form>
</div>
@endsection