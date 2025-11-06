@extends('layouts.app')
@section('title','Uus kategooria – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1>Uus kategooria</h1>

<form action="{{ route('admin.categories.store') }}" method="post" class="stack">
    @csrf
    <label>Nimi
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="120">
    </label>

    <label>Slug
        <input type="text" name="slug" value="{{ old('slug') }}" maxlength="160" placeholder="tühjaks → genereeritakse">
    </label>

    <label>Kirjeldus
        <textarea name="description" rows="4">{{ old('description') }}</textarea>
    </label>

    <button class="btn btn-primary">Loo</button>
    <a class="btn" href="{{ route('admin.categories.index') }}">Tagasi</a>
</form>
@endsection
