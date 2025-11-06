@extends('layouts.app')
@section('title','Uus postitus')

@section('content')
<h1>Uus postitus</h1>
<form action="{{ route('admin.posts.store') }}" method="post" class="stack" enctype="multipart/form-data">
    @csrf
    <label>Pealkiri
        <input type="text" name="title" value="{{ old('title') }}" required maxlength="180">
    </label>

    <label>Slug
        <input type="text" name="slug" value="{{ old('slug') }}" maxlength="220" placeholder="tühjaks → genereeritakse">
    </label>

    <label>Kategooria
        <select name="category_id">
            <option value="">—</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </label>

    <label>Staatus
        <select name="status" required>
            @foreach (['draft','review','published','archived'] as $st)
                <option value="{{ $st }}" @selected(old('status')===$st)>{{ $st }}</option>
            @endforeach
        </select>
    </label>

    <label>Avaldamise aeg
        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}">
    </label>

    <label>Väljavõte
        <textarea name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
    </label>

    <label>Sisu
        <textarea name="body" rows="10" required>{{ old('body') }}</textarea>
    </label>

    <label>Sildid
        <select name="tag_ids[]" multiple size="6">
            @foreach($tags as $t)
                <option value="{{ $t->id }}" @selected(collect(old('tag_ids',[]))->contains($t->id))>{{ $t->name }}</option>
            @endforeach
        </select>
    </label>

    <label>Pildi fail (valikuline)
        <input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.gif,.webp">
    </label>


    <button class="btn btn-primary" href="{{ route('admin.posts.index') }}">Salvesta</button>
</form>
@endsection
