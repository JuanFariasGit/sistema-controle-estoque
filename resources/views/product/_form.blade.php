<div class="form-group col-md-6">
    <label for="code">Código:</label>
    <input class="form-control @error('code') is-invalid @enderror" type="text" id="code" name="code" value="{{ isset($product->code) ? $product->code : '' }}">
    @error('code')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <label for="name">Nome:</label>
    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ isset($product->name) ? $product->name : '' }}">
    @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <label for="capacity">Capacidade (ml):</label>
    <input class="form-control @error('capacity') is-invalid @enderror" type="text" id="capacity" name="capacity" value="{{ isset($product->capacity) ? $product->capacity : '' }}">
    @error('capacity')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-md-6">
    <label>Foto:</label>
    <input class="form-control @error('photo') is-invalid @enderror" type="file" id="photo" name="photo">
    @error('photo')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    @if (isset($product->photo))
        <img class="mt-3" height="100" src="{{ $product->photo }}">
    @endif
</div>
<button type="submit" class="btn btn-sm btn-primary">SALVAR</button>