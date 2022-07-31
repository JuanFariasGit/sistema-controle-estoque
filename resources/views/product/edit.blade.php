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
            let id = document.querySelector("input[name='id']").value;

            reader.onload = function () {
                imgPhoto.src = "{{ asset('imagens/padrao.png') }}";
            }

            filePhoto.value = '';
            
            reader.readAsDataURL(new Blob([], {type: 'image/png'}));
            
            deleteImg(id);
        })

        function deleteImg(id) {
            let urlDel = "{{ route('product.del-photo', ':id') }}";

            $.ajax ({
                "method": "DELETE",
                "url": `${urlDel.replace(':id', id)}`,
                "headers": {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                success: function() {
                    table.row($(`#row_${id}`).parents('tr')).remove().draw(false);
                    closeModal()
                }
            })
        }
    </script>
@endsection