<div class="form-group col-md-6">
    <label for="code">Código:</label>
    <input class="form-control @error('code') is-invalid @enderror" type="text" id="code" name="code" value="{{ $product->code ?? old('code') }}">
    @error('code')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <label for="name">Nome:</label>
    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ $product->name ?? old('name') }}">
    @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <label for="capacity">Capacidade (ml):</label>
    <input class="form-control @error('capacity') is-invalid @enderror" type="text" id="capacity" name="capacity" value="{{ $product->capacity ?? old('capacity') }}">
    @error('capacity')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6 d-flex align-items-center">
    <label class="mr-3">
        Foto:<br><br>
        <i id="addPhoto" class="fas fa-plus fa-2x text-primary mr-2"></i> 
        <i id="delPhoto" class="fas fa-minus-circle fa-2x text-danger"></i>
    </label>
    <input class="form-control @error('photo') is-invalid @enderror" type="file" id="filePhoto" name="photo" accept="image/*">
    @error('photo')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    @if (isset($product->photo) && !empty($product->photo))
        <img id="imgPhoto" class="mt-3" width="100" src="{{ asset('imagens/produtos/'.$product->photo) }}">
    @else
        <img id="imgPhoto" class="mt-3" width="100" src="{{ asset('imagens/padrao.png') }}">
    @endif
</div>
<button type="submit" class="btn btn-sm btn-primary">SALVAR</button>