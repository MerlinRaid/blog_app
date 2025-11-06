@extends('layouts.app')

@section('title','Muuda: '.$post->title)

@section('content')
<h1>Muuda postitust</h1>
<form action="{{ route('admin.posts.update',$post) }}" method="post" class="stack" enctype="multipart/form-data">
    @csrf @method('put')

    <label>Pealkiri
        <input type="text" name="title" value="{{ old('title',$post->title) }}" required maxlength="180">
    </label>

    <label>Slug
        <input type="text" name="slug" value="{{ old('slug',$post->slug) }}" required maxlength="220">
    </label>

    <label>Kategooria
        <select name="category_id">
            <option value="">—</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id',$post->category_id)==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </label>

    <label>Staatus
        <select name="status" required>
            @foreach (['draft','review','published','archived'] as $st)
                <option value="{{ $st }}" @selected(old('status',$post->status)===$st)>{{ $st }}</option>
            @endforeach
        </select>
    </label>

    <label>Avaldamise aeg
        <input type="datetime-local" name="published_at"
               value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}">
    </label>

    <label>Väljavõte
        <textarea name="excerpt" rows="3">{{ old('excerpt',$post->excerpt) }}</textarea>
    </label>

    <label>Sisu
        <textarea name="body" rows="10" required>{{ old('body',$post->body) }}</textarea>
    </label>

    <label>Sildid
        <select name="tag_ids[]" multiple size="6">
            @php $sel = old('tag_ids', $post->tags->pluck('id')->all()); @endphp
            @foreach($tags as $t)
                <option value="{{ $t->id }}" @selected(collect($sel)->contains($t->id))>{{ $t->name }}</option>
            @endforeach
        </select>
    </label>

    @if($post->featured_image)
        <div class="stack" style="margin-top:.5rem">
            <div class="muted">Praegune pilt:</div>
            <img src="{{ $post->featuredImageUrl() }}" alt="" style="max-width:280px;border-radius:6px;border:1px solid #ddd">
            <label style="display:inline-flex;align-items:center;gap:.5rem;margin-top:.5rem">
                <input type="checkbox" name="remove_image" value="1">
                Eemalda pilt
            </label>
        </div>
    @endif

    <label>Uus pilt (asendab olemasoleva)
        <input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.gif,.webp">
    </label>


    <div class="row">
        <button class="btn btn-primary">Salvesta</button>
        <a class="btn" href="{{ route('admin.posts.index') }}">Tagasi</a>
    </div>
</form>
@endsection
