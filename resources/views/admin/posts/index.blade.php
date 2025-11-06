@extends('layouts.app')
@section('title','Postitused – Admin')

@section('content')
<h1>Postitused</h1>

<div class="stack" style="margin-bottom:12px">
    <a class="btn btn-primary" href="{{ route('admin.posts.create') }}">+ Uus postitus</a>

    <form method="get" class="row" style="align-items:center">
        <label for="status" class="muted fw-bold my-2">Staatus</label>
        <select name="status" id="status" class="form-select form-select-lg" onchange="this.form.submit()" style="margin-left:8px">
            <option value="">Kõik</option>
            @foreach (['draft','review','published','archived'] as $st)
                <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
            @endforeach
        </select>
    </form>
</div>

@if($posts->isEmpty())
    <p class="muted">—</p>
@else
    <table class="table table-bordered" style="width:100%;border-collapse:collapse">
        <thead>
            <tr>
                <th style="text-align:left">Pealkiri</th>
                <th>Staatus</th>
                <th>Avaldatud</th>
                <th>Autor</th>
                <th style="width:1%">Toimingud</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($posts as $p)
            <tr>
                <td>
                    <a href="{{ route('admin.posts.edit',$p) }}">{{ $p->title }}</a>
                    <div class="muted">{{ $p->slug }}</div>
                </td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->published_at?->format('d.m.Y H:i') ?? '—' }}</td>
                <td>{{ $p->author?->name ?? '—' }}</td>
                <td class="row">
                    @if($p->status!=='published')
                        <form action="{{ route('admin.posts.publish',$p) }}" method="post">
                            @csrf @method('patch')
                            <button class="btn">Avalda</button>
                        </form>
                    @else
                        <form action="{{ route('admin.posts.unpublish',$p) }}" method="post">
                            @csrf @method('patch')
                            <button class="btn">Eemalda</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.posts.destroy',$p) }}" method="post" onsubmit="return confirm('Kustuta?')">
                        @csrf @method('delete')
                        <button class="btn">Kustuta</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $posts->links() }}
@endif
@endsection
