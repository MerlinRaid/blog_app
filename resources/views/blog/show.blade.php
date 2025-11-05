@extends('layouts.app')

@section('title', $post->title)

@section('content')

 @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<article class="stack">
    <header>
        <h1 style="margin:0">{{ $post->title }}</h1>
        <p class="muted" style="margin:.25rem 0 0">
            @if($post->published_at) {{ $post->published_at->format('d.m.Y H:i:s') }} * @endif
            {{ $post->author?->name ?? '—' }}
            @if($post->category) * {{ $post->category->name }} @endif
        </p>
        @if($post->tags->isNotEmpty())
            <p class="muted" style="margin:.25rem 0 0">
                @foreach ($post->tags as $tag)
                    {{ $tag->name }}@if(!$loop->last), @endif
                @endforeach
            </p>
        @endif
    </header>

    @if($post->featured_image)
        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="" class="img-fluid" style="border-radius:6px;height:200px">
    @endif

    <section>
        {!! nl2br(e($post->body)) !!}
    </section>
</article>

<hr>

<section class="stack" aria-labelledby="kommentaarid">
    <h2 id="kommentaarid" style="margin:0">Kommentaarid</h2>

    @if ($comments->isEmpty())
        <p class="muted">Kommentaare veel pole.</p>
    @else
        @foreach ($comments as $comment)
            <div>
                <p class="muted" style="margin:0">
                    {{ $comment->author?->name ?? '—' }} * {{ $comment->created_at->format('d.m.Y H:i') }}
                </p>
                <p style="margin:.25rem 0 0">{{ $comment->body }}</p>
                <hr>
            </div>
        @endforeach

        {{ $comments->links() }}
    @endif

    @auth
        <form action="{{ route('comments.store', $post->slug) }}" method="post" class="stack" style="margin-top:.5rem">
            @csrf
            <label for="body">Lisa kommentaar (min 50 märki)</label>
            <textarea name="body" id="body" rows="4"
                @error('body') class="invalid" @enderror
                minlength="50" required>{{ old('body') }}</textarea>
            @error('body')
                <div class="muted" role="alert">{{ $message }}</div>
            @enderror
            <button type="submit" class="btn btn-primary">Saada</button>
        </form>
    @else
        <div class="alert">Logi sisse, et kommenteerida.</div>
    @endauth
</section>
@endsection
