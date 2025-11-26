@extends('layouts.app')

@section('title', 'Autor – Avaleht')

@section('content')
<div class="container">
    <h1 class="mb-3">Autor</h1>

    <div class="card">
        <div class="card-body">
            <p>Oled sisse logitud kui <strong>{{ auth()->user()->name }}</strong>.</p>

            <a href="{{ route('author.posts.index') }}" class="btn btn-primary">
                Minu postitused
            </a>

            <a href="{{ route('author.posts.create') }}" class="btn btn-success ms-2">
                + Uus postitus
            </a>
        </div>
    </div>
</div>
@endsection
