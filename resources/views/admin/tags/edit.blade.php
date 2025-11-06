@extends('layouts.app')
@section('title','Muuda silti – Admin')

@section('content')
<h1>Muuda silti</h1>
@if (session('status')) <div class="alert">{{ session('status') }}</div> @endif

<form action="{{ route('admin.tags.update',$tag) }}" method="post" class="stack">
    @csrf @method('put')

    <label>Nimi
        <input type="text" name="name" value="{{ old('name',$tag->name) }}" required maxlength="80">
    </label>

    <label>Slug
        <input type="text" name="slug" value="{{ old('slug',$tag->slug) }}" required maxlength="120">
    </label>

    <div class="row">
        <button class="btn btn-primary">Salvesta</button>
        <a class="btn" href="{{ route('admin.tags.index') }}">Tagasi</a>
    </div>
</form>
@endsection
