@extends('layouts.app')
@section('title', 'Adicionar Produto')

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

@section('scripts')
    <script>
        let imgPhoto = document.querySelector('#imgPhoto');
        let filePhoto = document.querySelector('#filePhoto');
        let addPhoto = document.querySelector('#addPhoto');
       
        addPhoto.addEventListener('click', function () {
            filePhoto.click();
        })

        filePhoto.addEventListener('change', function () {
            let reader = new FileReader();
            
            reader.onload = function () {
                imgPhoto.src = reader.result;
            }
            
            reader.readAsDataURL(filePhoto.files[0]);
        })

        delPhoto.addEventListener('click', function () {
            let reader = new FileReader();

            reader.onload = function () {
                imgPhoto.src = "{{ asset('imagens/padrao.png') }}";
            }

            filePhoto.value = '';
            
            reader.readAsDataURL(new Blob([], {type: 'image/png'}));
        })
    </script>
@endsection