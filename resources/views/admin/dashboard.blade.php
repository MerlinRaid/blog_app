@extends('layouts.app')
@section('title', 'Admin – Avaleht')

@section('content')
<h1 class="mb-4">{{ Auth::user()->name }} paneel</h1>

<div class="row g-4">

    {{-- Komponentide loetelu --}}
    @php
        $sections = [
            ['title' => 'Vajab ülevaatust', 'items' => $needsAction, 'empty' => 'Kõik korras.', 'type' => 'posts'],
            ['title' => 'Planeeritud postitused', 'items' => $scheduled, 'empty' => 'Pole planeeritud postitusi.', 'type' => 'posts'],
            ['title' => 'Hiljuti avaldatud', 'items' => $recent, 'empty' => 'Hiljuti pole avaldatud.', 'type' => 'posts'],
            ['title' => 'Kommentaarid – ootel', 'items' => $pendingComments, 'empty' => 'Pole ootel kommentaare.', 'type' => 'comments'],
            ['title' => 'Arhiveeritud postitused', 'items' => $archived, 'empty' => 'Pole arhiveeritud postitusi.', 'type' => 'posts'],
            ['title' => 'Orvuks jäänud kommentaarid', 'items' => $orphanedComments, 'empty' => 'Pole orvuks jäänud kommentaare.', 'type' => 'orphaned'],
            ['title' => 'Prügikast', 'items' => $trashed, 'empty' => 'Prügikast on tühi.', 'type' => 'trash'],
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

                            {{-- Postitused --}}
                            @case('posts')
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    <small class="text-muted">
                                        {{ $item->author->name }} –
                                        {{ $item->updated_at?->format('d.m.Y H:i') ?? $item->published_at?->format('d.m.Y H:i') }}
                                        @if($item->status)
                                            <span class="badge bg-secondary ms-2">{{ $item->status }}</span>
                                        @endif
                                    </small>
                                </div>
                                <a href="{{ route('admin.posts.edit', $item) }}" class="btn btn-sm btn-outline-primary">Muuda</a>
                            </li>
                            @break

                            {{-- Kommentaarid --}}
                            @case('comments')
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ Str::limit($item->comment, 30) }}</div>
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
                                        <button class="btn btn-sm btn-outline-secondary">{{ ucfirst($status) }}</button>
                                    @endforeach
                                    @if(Auth::user()->is_admin)
                                        <button class="btn btn-sm btn-danger">Kustuta</button>
                                    @endif
                                </div>
                            </li>
                            @break

                            {{-- Orvuks jäänud kommentaarid --}}
                                @case('orphaned')
<li class="list-group-item">
    <div class="text-danger mb-1">Seos postitusega puudub!</div>
    <div>{{ $item->body }}</div>
    <small class="d-block text-muted mt-1">
        {{ $item->author->name ?? 'Anonüümne' }},
        {{ $item->created_at->format('d.m.Y H:i') }}
    </small>
    @if(Auth::user()->is_admin)
        <button class="btn btn-sm btn-danger mt-2">Kustuta</button>
    @endif
</li>
@break

                            @case('trash')
<li class="list-group-item">
    <div class="fw-semibold">{{ $item->title }}</div>
    <small class="text-muted d-block">
        {{ $item->author->name }},
        loodud: {{ $item->created_at->format('d.m.Y H:i') }},
        muudetud: {{ $item->updated_at->format('d.m.Y H:i') }},
        kustutatud: {{ $item->deleted_at->format('d.m.Y H:i') }}
    </small>

    {{-- Taasta ja kustuta jäädavalt nupud --}}
    <div class="mt-2 d-flex gap-2">

        {{-- Taasta --}}
        <form action="{{ route('admin.posts.restore', $item->id) }}" method="post">
            @csrf
            @method('patch')
            <button class="btn btn-sm btn-success" onclick="return confirm('Taasta postitus?')">
                Taasta
            </button>
        </form>

        {{-- Kustuta jäädavalt --}}
        <form action="{{ route('admin.posts.forceDelete', $item->id) }}" method="post">
            @csrf
            @method('delete')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Kustuta jäädavalt?')">
                Kustuta jäädavalt
            </button>
        </form>

    </div>
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
