@extends('layouts.app')
@section('title', 'Admin – Avaleht')

@section('content')
<h1> {{ Auth::user()->name }} paneel</h1>
                    

    {{-- Needs action --}}
    <div class="col-md-6">
        <h4>Vajab ülevaatust</h4>
        @if($needsAction->isEmpty())
            <div class="text-muted">Kõik korras.</div>
        @else
            <ul class="list-group">
                @foreach($needsAction as $post)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $post->title }}</strong>
                        <br>
                        <small>{{ $post->author->name }} – {{ $post->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-primary">
                        Ava
                    </a>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Scheduled --}}
    <div class="col-md-6">
        <h4>Planeeritud</h4>
        @if($scheduled->isEmpty())
            <div class="text-muted">Pole planeeritud postitusi.</div>
        @else
            <ul class="list-group">
                @foreach($scheduled as $post)
                <li class="list-group-item">
                    <strong>{{ $post->title }}</strong>
                    <br>
                    <small>Publitseeritakse: {{ $post->published_at->format('d.m.Y H:i') }}</small>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Recent published --}}
    <div class="col-md-6">
        <h4>Hiljuti avaldatud</h4>
        @if($recent->isEmpty())
            <div class="text-muted">Hiljuti pole avaldatud.</div>
        @else
            <ul class="list-group">
                @foreach($recent as $post)
                <li class="list-group-item">
                    <strong>{{ $post->title }}</strong>
                    <br>
                    <small>{{ $post->author->name }} – {{ $post->published_at->format('d.m.Y H:i') }}</small>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Pending comments --}}
    <div class="col-md-6">
        <h4>Kommentaarid – ootel</h4>
        @if($pendingComments->isEmpty())
            <div class="text-muted">Pole ootel kommentaare.</div>
        @else
            <ul class="list-group">
                @foreach($pendingComments as $comment)
                @if($comment->post)
                <li class="list-group-item">
                    {{ Str::limit($comment->comment, 60) }}<br>
                    <small>
                        {{ $comment->author->name ?? 'Anonüümne' }},
                        {{ $comment->created_at->format('d.m.Y H:i') }},
                            post: <a href="{{ route('admin.posts.edit', $comment->post) }}">{{ $comment->post->title }}</a>
                        @else
                            <span class="text-muted">Postitus puudub</span>
                        @endif

                    </small>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
@endsection
