@extends('layouts.app')
@section('title','Kategooriad – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1>Kategooriad</h1>

<div class="row" style="align-items:center;margin-bottom:12px">
    <a class="btn" href="{{ route('admin.categories.create') }}">+ Uus kategooria</a>
</div>

@if($categories->isEmpty())
    <p class="muted">—</p>
@else
<table class="table" style="width:100%;border-collapse:collapse">
    <thead>
        <tr>
            <th style="text-align:left">Nimi</th>
            <th>Slug</th>
            <th style="width:1%">Toimingud</th>
        </tr>
    </thead>
    <tbody>
    @foreach($categories as $c)
        <tr>
            <td>{{ $c->name }}</td>
            <td class="muted">{{ $c->slug }}</td>
            <td class="row">
                <a class="btn" href="{{ route('admin.categories.edit',$c) }}">Muuda</a>
                <form action="{{ route('admin.categories.destroy',$c) }}" method="post" onsubmit="return confirm('Kustuta?')">
                    @csrf @method('delete')
                    <button class="btn">Kustuta</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $categories->links() }}
@endif
@endsection
