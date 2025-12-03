@extends('layouts.app')

@section('title', 'Kommentaarid – Admin')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <h1 class="mb-4">Kommentaaride modereerimine</h1>

    {{-- Filter --}}
    <form method="get" class="d-flex align-items-center gap-2 mb-4">
        <label for="status" class="mb-0">Staatus:</label>
        <select name="status" id="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            <option value="">Kõik</option>
            @foreach(['pending', 'approved', 'hidden', 'spam'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </form>

    {{-- Kommentaarid --}}
    @if($comments->isEmpty())
        <p class="text-muted">Ühtegi kommentaari ei leitud.</p>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($comments as $comment)
                <div class="border rounded p-3 bg-light">
                    {{-- Info --}}
                    <div class="mb-2 small text-muted">
                        <strong>{{ $comment->author?->name ?? 'Anonüümne' }}</strong>
                        • {{ $comment->created_at->format('d.m.Y H:i') }}
                        • <span class="badge bg-secondary">{{ $comment->status }}</span>
                        <br>
                        @if($comment->post)
                            <span class="text-muted">
                                <i class="fa fa-link me-1"></i>Kommentaar postitusele:
                                <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" class="text-decoration-none">
                                    {{ $comment->post->title }}
                                </a>
                            </span>
                        @else
                            <span class="text-danger">
                                <i class="fa fa-exclamation-triangle me-1"></i>Seotud postitus puudub
                            </span>
                        @endif
                    </div>

                    {{-- Kommentaari sisu --}}
                    <div class="mb-3 fs-6">
                        {{ $comment->body }}
                    </div>

                    {{-- Nupud --}}
                    <div class="d-flex flex-wrap gap-2">
                        @foreach([
                            'approved' => ['label' => 'Kinnita', 'icon' => 'fa-check'],
                            'hidden'   => ['label' => 'Peida', 'icon' => 'fa-eye-slash'],
                            'spam'     => ['label' => 'Spam', 'icon' => 'fa-ban'],
                            'pending'  => ['label' => 'Ootele', 'icon' => 'fa-clock'],
                        ] as $status => $info)
                            <form action="{{ route('admin.comments.updateStatus', $comment) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="fa {{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
                                </button>
                            </form>
                        @endforeach

                        @role('Admin')
                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Kas kustutada kommentaar?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-trash me-1"></i>Kustuta
                            </button>
                        </form>
                        @endrole
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
