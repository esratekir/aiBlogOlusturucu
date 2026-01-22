@extends('layouts.admin')

@section('title', 'Makaleler')
@section('page-title', 'Makaleler')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
                <h4 class="header-title mb-0">Tüm Makaleler</h4>
                <a href="{{ route('articles.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Yeni Makale
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @forelse($articles as $article)
    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            @if($article->image_path)
            <img src="{{ Storage::url($article->image_path) }}" class="card-img-top" alt="{{ $article->title }}" style="height: 200px; object-fit: cover;">
            @else
            <div class="bg-primary-subtle" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                <i class="ti ti-article fs-1 text-primary"></i>
            </div>
            @endif
            
            <div class="card-body">
                <h5 class="card-title">{{ $article->title }}</h5>
                <p class="card-text text-muted">{{ Str::limit($article->content, 100) }}</p>
                
                @if($article->tags)
                <div class="mb-3">
                    @foreach(explode(',', $article->tags) as $tag)
                    <span class="badge bg-primary-subtle text-primary me-1">{{ trim($tag) }}</span>
                    @endforeach
                </div>
                @endif
                
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="ti ti-clock me-1"></i>
                        {{ $article->created_at->diffForHumans() }}
                    </small>
                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit"></i> Düzenle
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ti ti-article fs-1 text-muted mb-3"></i>
                <h5>Henüz makale yok</h5>
                <p class="text-muted">Hemen ilk makalenizi oluşturun!</p>
                <a href="{{ route('articles.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Yeni Makale Oluştur
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($articles->hasPages())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>
@endif
@endsection