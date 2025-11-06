@extends('layouts.app')
@section('title','Muuda kategooriat – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1>Muuda kategooriat</h1>
@if (session('status')) <div class="alert">{{ session('status') }}</div> @endif

<form action="{{ route('admin.categories.update',$category) }}" method="post" class="stack">
    @csrf @method('put')

    <label>Nimi
        <input type="text" name="name" value="{{ old('name',$category->name) }}" required maxlength="120">
    </label>

    <label>Slug
        <input type="text" name="slug" value="{{ old('slug',$category->slug) }}" required maxlength="160">
    </label>

    <label>Kirjeldus
        <textarea name="description" rows="4">{{ old('description',$category->description) }}</textarea>
    </label>

    <div class="row">
        <button class="btn btn-primary">Salvesta</button>
        <a class="btn" href="{{ route('admin.categories.index') }}">Tagasi</a>
    </div>
</form>
@endsection
