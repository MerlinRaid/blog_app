@extends('layouts.app')
@section('title', 'Admin – Avaleht')

@section('content')
<h1 class="mb-4">{{ Auth::user()->name }} paneel</h1>

<div class="row g-4">

    @php
        $sections = [
            ['title' => 'Vajab ülevaatust', 'items' => $needsAction, 'empty' => 'Kõik korras.', 'type' => 'posts'],
            ['title' => 'Planeeritud postitused', 'items' => $scheduled, 'empty' => 'Pole planeeritud postitusi.', 'type' => 'posts'],
            ['title' => 'Hiljuti avaldatud', 'items' => $recent, 'empty' => 'Hiljuti pole avaldatud.', 'type' => 'recent'],
            ['title' => 'Kommentaarid – ootel', 'items' => $pendingComments, 'empty' => 'Pole ootel kommentaare.', 'type' => 'comments'],
            ['title' => 'Arhiveeritud postitused', 'items' => $archived, 'empty' => 'Pole arhiveeritud postitusi.', 'type' => 'posts'],
            ['title' => 'Orvuks jäänud kommentaarid', 'items' => $orphanedComments, 'empty' => 'Pole orvuks jäänud kommentaare.', 'type' => 'orphaned'],
             ['title' => 'Postituste prügikast', 'items' => $trashed, 'empty' => 'Postituste prügikast on tühi.', 'type' => 'trash'],
            ['title' => 'Kommentaaride prügikast', 'items' => $trashedComments, 'empty' => 'Kommentaaride prügikast on tühi.', 'type' => 'comment_trash'],
           
        ];
    @endphp

    @foreach($sections as $section)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $section['title'] }}</h5>
                <small class="text-muted">{{ $section['items']->count() }} kokku</small>
            </div>
            <div class="card-body">
                @if($section['items']->isEmpty())
                    <div class="text-muted">{{ $section['empty'] }}</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($section['items']->take(5) as $item)
                            @switch($section['type'])

                            {{-- POSTITUSED --}}
                            @case('posts')
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    <small class="text-muted">
                                        {{ $item->author->name }} –
                                        {{ $item->published_at?->format('d.m.Y H:i') ?? $item->updated_at?->format('d.m.Y H:i') }}
                                        <span class="badge bg-secondary ms-2">{{ $item->status }}</span>
                                    </small>
                                </div>
                                <a href="{{ route('admin.posts.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit"></i> Muuda
                                </a>
                            </li>
                            @break

                            {{-- HILJUTI AVALDATUD (ilma nuputa) --}}
                            @case('recent')
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ $item->title }}</div>
                                <small class="text-muted">
                                    {{ $item->author->name }} –
                                    {{ $item->published_at->format('d.m.Y H:i') }}
                                    <span class="badge bg-secondary ms-2">{{ $item->status }}</span>
                                </small>
                            </li>
                            @break

                            {{-- KOMMENTAARID --}}
                            @case('comments')
                            <li class="list-group-item">
                                <div class="fw-semibold">
                                    {{ Str::limit($item->body ?? '-', 30) }}
                                </div>
                                <small class="text-muted d-block">
                                    {{ $item->author->name ?? 'Anonüümne' }},
                                    {{ $item->created_at->format('d.m.Y H:i') }}
                                    @if($item->post)
                                        – <a href="{{ route('admin.posts.edit', $item->post) }}">{{ $item->post->title }}</a>
                                    @else
                                        – <span class="text-danger">Postitus puudub</span>
                                    @endif
                                </small>
                                <div class="mt-2 d-flex flex-wrap gap-1">
                                    @foreach(['approved', 'hidden', 'spam', 'pending'] as $status)
                                        <form action="{{ route('admin.comments.updateStatus', $item) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button class="btn btn-sm btn-outline-secondary">
                                                {{ ucfirst($status) }}
                                            </button>
                                        </form>
                                    @endforeach
                                    @role('Admin')
                                    <form action="{{ route('admin.comments.destroy', $item) }}" method="post" onsubmit="return confirm('Kustutada kommentaar?')">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i> Kustuta
                                        </button>
                                    </form>
                                    @endrole
                                </div>
                            </li>
                            @break

                            {{-- ORVUKS JÄÄNUD KOMMENTAARID --}}
                            @case('orphaned')
                            <li class="list-group-item">
                                <div class="text-danger mb-1">Seos postitusega puudub!</div>
                                <div>{{ $item->body }}</div>
                                <small class="d-block text-muted mt-1">
                                    {{ $item->author->name ?? 'Anonüümne' }},
                                    {{ $item->created_at->format('d.m.Y H:i') }}
                                </small>
                                @role('Admin')
                                <form action="{{ route('admin.comments.destroy', $item) }}" method="post" class="mt-2" onsubmit="return confirm('Kustutada kommentaar?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i> Kustuta
                                    </button>
                                </form>
                                @endrole
                            </li>
                            @break

                            {{-- KOMMENTAARIDE PRÜGIKAST --}}
                            @case('comment_trash')
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ Str::limit($item->body, 60) }}</div>
                                <small class="text-muted d-block">
                                    {{ $item->author->name ?? 'Anonüümne' }},
                                    kustutatud: {{ $item->deleted_at->format('d.m.Y H:i') }}
                                </small>
                                @role('Admin')
                                <div class="mt-2 d-flex gap-2">
                                    <form action="{{ route('admin.comments.restore', $item->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Taasta kommentaar?')">
                                            <i class="fa fa-undo"></i> Taasta
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.comments.forceDelete', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Kustuta jäädavalt?')">
                                            <i class="fa fa-trash"></i> Kustuta jäädavalt
                                        </button>
                                    </form>
                                </div>
                                @endrole
                            </li>
                            @break

                            {{-- POSTITUSTE PRÜGIKAST --}}
                            @case('trash')
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ $item->title }}</div>
                                <small class="text-muted d-block">
                                    {{ $item->author->name }},
                                    loodud: {{ $item->created_at->format('d.m.Y H:i') }},
                                    muudetud: {{ $item->updated_at->format('d.m.Y H:i') }},
                                    kustutatud: {{ $item->deleted_at->format('d.m.Y H:i') }}
                                </small>
                                @role('Admin')
                                <div class="mt-2 d-flex gap-2">
                                    <form action="{{ route('admin.posts.restore', $item->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Taasta postitus?')">
                                            <i class="fa fa-undo"></i> Taasta
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.posts.forceDelete', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Kustuta jäädavalt?')">
                                            <i class="fa fa-trash"></i> Kustuta jäädavalt
                                        </button>
                                    </form>
                                </div>
                                @endrole
                            </li>
                            @break

                            @endswitch
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection
