@extends('layouts.app')
@section('title','Uus silt – Admin')

@section('content')
<h1>Uus silt</h1>

<form action="{{ route('admin.tags.store') }}" method="post" class="stack">
    @csrf
    <label>Nimi
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="80">
    </label>

    <label>Slug
        <input type="text" name="slug" value="{{ old('slug') }}" maxlength="120" placeholder="tühjaks → genereeritakse">
    </label>

    <button class="btn btn-primary">Loo</button>
    <a class="btn" href="{{ route('admin.tags.index') }}">Tagasi</a>
</form>
@endsection
