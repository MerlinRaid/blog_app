@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4">Postitused</h1>

@if ($posts->isEmpty())
    <p>Hetkel pole avalikke postitusi.</p>
@else    
    <div class="vstack gap-4">
        @foreach ($posts as $post)
            <article class="card">
                <div class="card-body">
                    <h2 class="h5 mb-2">
                        <a class="text-decoration-none" href="{{ route('blog.show', $post->slug) }}">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <div class="text-muted small mb-2">
                        @if($post->category)
                            <span class="me-2">Kategooria: {{ $post->category->name }}</span>
                        @endif
                        <span class="me-2">Autor: {{ $post->author?->name ?? '—' }}</span>
                        @if($post->published_at)
                            <span> Avaldatud: {{ $post->published_at->format('d.m.Y H:i') }}</span>
                        @endif
                    </div>
                    @if($post->excerpt)
                        <p class="mb-2">{{ $post->excerpt }}</p>
                    @else
                        <p class="mb-2 text-muted">—</p>
                    @endif

                    @if($post->tags->isNotEmpty())
                        <div class="small">
                            @foreach ($post->tags as $tag)
                                <span class="badge bg-light text-dark me-1">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
@endif
@endsection
