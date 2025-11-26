@extends('layouts.app')

@section('title', 'Minu postitused')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<h1 class="mb-3">Minu postitused</h1>

<a href="{{ route('author.posts.create') }}" class="btn btn-success mb-3">
    + Uus postitus
</a>

@if($posts->count() === 0)
    <div class="alert alert-info">Sul pole veel postitusi.</div>
@else
<table class="table table-striped">
    <thead>
        <tr>
            <th>Pealkiri</th>
            <th>Kategooria</th>
            <th>Staatus</th>
            <th>Kuupäev</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $post)
        <tr>
            <td>{{ $post->title }}</td>
            <td>{{ $post->category->name ?? '-' }}</td>
            <td>{{ $post->status }}</td>
            <td>{{ $post->created_at->format('d.m.Y') }}</td>
            <td class="text-end">
                @can('update', $post)
                <a href="{{ route('author.posts.edit', $post) }}" class="btn btn-sm btn-primary">
                    Muuda
                </a>
                @endcan

                @can('delete', $post)
                <form action="{{ route('author.posts.destroy', $post) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger"
                            onclick="return confirm('Kustutada?')">
                        Kustuta
                    </button>
                </form>
                @endcan
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $posts->links() }}
@endif
@endsection
