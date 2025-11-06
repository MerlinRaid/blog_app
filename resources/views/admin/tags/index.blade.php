@extends('layouts.app')
@section('title','Sildid – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1>Sildid</h1>

<div class="row" style="align-items:center;margin-bottom:12px">
    <a class="btn" href="{{ route('admin.tags.create') }}">+ Uus silt</a>
</div>

@if($tags->isEmpty())
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
    @foreach($tags as $t)
        <tr>
            <td>{{ $t->name }}</td>
            <td class="muted">{{ $t->slug }}</td>
            <td class="row">
                <a class="btn" href="{{ route('admin.tags.edit',$t) }}">Muuda</a>
                <form action="{{ route('admin.tags.destroy',$t) }}" method="post" onsubmit="return confirm('Kustuta?')">
                    @csrf @method('delete')
                    <button class="btn">Kustuta</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $tags->links() }}
@endif
@endsection
