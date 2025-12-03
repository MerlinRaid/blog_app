@extends('layouts.app')
@section('title','Kasutajad – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1>Kasutajad</h1>

<form method="get" class="row" style="align-items:center;margin-bottom:12px">
    <input type="text" name="search" placeholder="Otsi nime või e-posti järgi" value="{{ request('search') }}" style="max-width:280px">
    <button class="btn" style="margin-left:8px">Otsi</button>
    <a class="btn" style="margin-left:8px" href="{{ route('admin.users.create') }}">+ Uus kasutaja</a>
</form>

@if($users->isEmpty())
    <p class="muted">—</p>
@else
<table class="table" style="width:100%;border-collapse:collapse">
    <thead>
        <tr>
            <th style="text-align:left">Nimi</th>
            <th>E-post</th>
            <th>Roll</th>
            <th style="width:1%">Toimingud</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $u)
        <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->roles->pluck('name')->implode(', ') ?: '—' }}</td>
            <td class="row">
                <a class="btn" href="{{ route('admin.users.edit',$u) }}">Muuda</a>
                <form action="{{ route('admin.users.destroy',$u) }}" method="post" onsubmit="return confirm('Kustuta?')">
                    @csrf @method('delete')
                    <button class="btn">Kustuta</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $users->links() }}
@endif
@endsection
