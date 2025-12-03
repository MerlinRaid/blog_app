@extends('layouts.app')
@section('title','Muuda kasutajat – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1>Muuda kasutajat</h1>

@if (session('status'))
    <div class="alert">{{ session('status') }}</div>
@endif

<form action="{{ route('admin.users.update',$user) }}" method="post" class="stack">
    @csrf @method('put')

    <label>Nimi
        <input type="text" name="name" value="{{ old('name',$user->name) }}" required>
    </label>

    <label>E-post
        <input type="email" name="email" value="{{ old('email',$user->email) }}" required>
    </label>

    <label>Uus parool (valikuline)
        <input type="password" name="password">
    </label>
    <label>Korda uut parooli
        <input type="password" name="password_confirmation">
    </label>

    <label>Roll
        <select name="role_id" required>
            @foreach($roles as $id=>$name)
                <option value="{{ $id }}" @selected(old('role_id',optional($user->roles->first())->id)==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </label>

    <div class="row">
        <button class="btn btn-primary">Salvesta</button>
        <a class="btn" href="{{ route('admin.users.index') }}">Tagasi</a>
    </div>
</form>
@endsection
