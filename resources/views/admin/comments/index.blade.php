@extends('layouts.app')

@section('title', 'Kommentaarid – Admin')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
    <h1 class="mb-4">Kommentaaride modereerimine</h1>

    {{-- Filtri vorm --}}
    <form method="get" class="d-flex align-items-center mb-3 gap-2">
        <label for="status" class="form-label mb-0">Staatus</label>
        <select name="status" id="status" class="form-select form-select-sm w-auto"
                onchange="this.form.submit()">
            <option value="">Kõik</option>
            @foreach(['pending', 'approved', 'hidden', 'spam'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>
                    {{ ucfirst($st) }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Kommentaarid --}}
    @if($comments->isEmpty())
        <p class="text-muted">Ühtegi kommentaari ei leitud.</p>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($comments as $c)
                <div class="border rounded p-3">
                    {{-- Ülemine info --}}
                    <div class="small text-muted mb-2">
                        {{ $c->author?->name ?? '—' }}
                        • {{ $c->created_at->format('d.m.Y H:i') }}
                        • [{{ $c->status }}]
                        • Postitus:
                        @if($c->post)
                            <a href="{{ route('blog.show', $c->post->slug) }}" target="_blank">
                                {{ $c->post->title }}
                            </a>
                        @else
                            <span class="text-danger">[kustutatud]</span>
                        @endif
                    </div>

                    {{-- Kommentaari sisu --}}
                    <div class="mb-3">
                        {{ $c->body }}
                    </div>

                    {{-- Toimingunupud --}}
                    <div class="d-flex flex-wrap gap-2">
                        @foreach([
                            'approved' => ['label' => 'Kinnita', 'class' => 'btn-success'],
                            'hidden'   => ['label' => 'Peida', 'class' => 'btn-secondary'],
                            'spam'     => ['label' => 'Spam', 'class' => 'btn-secondary'],
                            'pending'  => ['label' => 'Ootele', 'class' => 'btn-secondary'],
                        ] as $status => $info)
                            <form action="{{ route('admin.comments.updateStatus', $c) }}" method="post">
                                @csrf @method('patch')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button class="btn btn-sm {{ $info['class'] }}">{{ $info['label'] }}</button>
                            </form>
                        @endforeach

                        <form action="{{ route('admin.comments.destroy', $c) }}" method="post"
                              onsubmit="return confirm('Kustuta kommentaar?')">
                            @csrf @method('delete')
                            <button class="btn btn-danger btn-sm">Kustuta</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginatsioon --}}
        <div class="mt-4">
            {{ $comments->links() }}
        </div>
    @endif
@endsection
