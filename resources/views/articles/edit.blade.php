@extends('layouts.admin')

@section('title', 'Makale Düzenle')
@section('page-title', 'Makale Düzenle')

@section('content')
<form action="{{ route('articles.update', $article) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Görsel Önizleme -->
            @if($article->image_path)
            <div class="card mb-3">
                <div class="card-body">
                    <img src="{{ Storage::url($article->image_path) }}" class="img-fluid rounded" alt="{{ $article->title }}">
                    @if($article->image_credit)
                    <small class="text-muted d-block mt-2">{{ $article->image_credit }}</small>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Makale Formu -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Makale Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" name="title" class="form-control" value="{{ $article->title }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <textarea name="content" class="form-control" rows="15" required>{{ $article->content }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Özet</label>
                        <textarea name="summary" class="form-control" rows="3">{{ $article->summary }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Etiketler</label>
                        <input type="text" name="tags" class="form-control" value="{{ $article->tags }}">
                        <small class="text-muted">Virgülle ayırarak yazın</small>
                    </div>
                </div>
            </div>
            
            <!-- SEO Bilgileri -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">SEO Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">SEO Başlık</label>
                        <input type="text" name="seo_title" class="form-control" value="{{ $article->seo_title }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">SEO Açıklama</label>
                        <textarea name="seo_description" class="form-control" rows="2">{{ $article->seo_description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Kaydet -->
            <div class="card sticky-top mb-3" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="header-title text-white mb-0">
                        <i class="ti ti-device-floppy"></i> Kaydet
                    </h4>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="ti ti-check me-1"></i> Güncelle
                    </button>
                    <a href="{{ route('articles.index') }}" class="btn btn-light w-100">
                        <i class="ti ti-arrow-left me-1"></i> Geri Dön
                    </a>
                </div>
            </div>
            
            <!-- AI Asistan -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h4 class="header-title text-white mb-0">
            🤖 AI Asistan
        </h4>
    </div>
    <div class="card-body">
        <!-- Mevcut butonlar -->
        <form action="{{ route('articles.ai.summarize', $article) }}" method="POST" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="ti ti-file-text me-1"></i> Özet Oluştur
            </button>
        </form>
        
        <form action="{{ route('articles.ai.tags', $article) }}" method="POST" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="ti ti-tags me-1"></i> Etiket Öner
            </button>
        </form>
        
        <form action="{{ route('articles.ai.seo', $article) }}" method="POST" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="ti ti-seo me-1"></i> SEO Oluştur
            </button>
        </form>

        <hr>

        <!-- YENİ: Ton Dönüştürücü -->
        <h6 class="mb-2">✨ Üslup Değiştir</h6>
        <div class="mb-2">
            <label class="form-label small">Yazım Tonu</label>
            <select id="toneSelector" class="form-select form-select-sm">
                <option value="formal">📋 Resmi & Profesyonel</option>
                <option value="casual">💬 Samimi & Gündelik</option>
                <option value="technical">🔧 Teknik & Detaylı</option>
                <option value="persuasive">💰 İkna Edici & Satış</option>
                <option value="emotional">❤️ Duygusal & Etkileyici</option>
                <option value="humorous">😄 Eğlenceli & Mizahi</option>
            </select>
            <small class="text-muted d-block mt-1" id="toneDescription">
                Profesyonel ve kurumsal dil
            </small>
        </div>
        
        <button type="button" class="btn btn-warning w-100 mb-2" id="rewriteToneBtn">
            <i class="ti ti-pencil me-1"></i> Üslubu Değiştir
        </button>

        <div id="toneLoading" class="alert alert-warning d-none">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2"></div>
                <small>AI üslubu değiştiriyor...</small>
            </div>
        </div>

        <hr class="mt-3">

        <!-- İçerik Genişletme -->
        <h6 class="mb-2">📝 İçerik Genişletme</h6>
        <div class="mb-2">
            <label class="form-label small">Eklenecek Kelime Sayısı</label>
            <input type="number" id="expandWords" class="form-control form-control-sm" value="500" min="200" max="2000" step="100">
        </div>
        <button type="button" class="btn btn-success w-100 mb-2" id="expandBtn">
            <i class="ti ti-text-plus me-1"></i> İçeriği Genişlet
        </button>

        <div id="expandLoading" class="alert alert-info d-none">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2"></div>
                <small>AI içeriği genişletiyor...</small>
            </div>
        </div>
    </div>
</div>
            
            <!-- Sil -->
            <div class="card border-danger">
                <div class="card-body">
                    <form action="{{ route('articles.destroy', $article) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('Silmek istediğinize emin misiniz?')">
                            <i class="ti ti-trash me-1"></i> Makaleyi Sil
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
    <div class="card-header">
        <h6 class="mb-0">📜 Ton Geçmişi</h6>
    </div>
    <div class="card-body">
        <div id="toneHistory"></div>
        <small class="text-muted">Son 5 üslup değişikliği</small>
    </div>
</div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const expandBtn = document.getElementById('expandBtn');
    const expandWords = document.getElementById('expandWords');
    const expandLoading = document.getElementById('expandLoading');
    const contentTextarea = document.querySelector('textarea[name="content"]');

    expandBtn?.addEventListener('click', async function() {
        if (!confirm('Mevcut içerik genişletilecek. Devam etmek istiyor musunuz?')) {
            return;
        }

        expandBtn.disabled = true;
        expandLoading.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("articles.ai.expand", $article) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    words: parseInt(expandWords.value)
                })
            });

            const data = await response.json();

            if (data.success) {
                contentTextarea.value = data.content;
                
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    <strong>Başarılı!</strong> İçerik genişletildi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                expandLoading.parentElement.insertBefore(alert, expandLoading);
                setTimeout(() => alert.remove(), 5000);
            } else {
                alert('Hata: ' + data.message);
            }
        } catch (error) {
            alert('Bir hata oluştu: ' + error.message);
        } finally {
            expandBtn.disabled = false;
            expandLoading.classList.add('d-none');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toneSelector = document.getElementById('toneSelector');
    const toneDescription = document.getElementById('toneDescription');
    const rewriteToneBtn = document.getElementById('rewriteToneBtn');
    const toneLoading = document.getElementById('toneLoading');
    const contentTextarea = document.querySelector('textarea[name="content"]');
    
    // Ton açıklamaları
    const toneDescriptions = {
        'formal': 'Profesyonel ve kurumsal dil',
        'casual': 'Arkadaşça ve gündelik üslup',
        'technical': 'Detaylı ve bilimsel anlatım',
        'persuasive': 'Satış odaklı ve etkili',
        'emotional': 'Hissiyata hitap eden',
        'humorous': 'Mizahi ve hafif'
    };
    
    // Ton seçimi değiştiğinde açıklamayı güncelle
    toneSelector?.addEventListener('change', function() {
        toneDescription.textContent = toneDescriptions[this.value];
    });
    
    // Ton Dönüştürme
    rewriteToneBtn?.addEventListener('click', async function() {
        const selectedTone = toneSelector.value;
        const currentContent = contentTextarea.value.trim();
        
        if (!currentContent) {
            alert('Lütfen önce makale içeriği girin!');
            return;
        }
        
        if (!confirm(`İçerik "${toneDescriptions[selectedTone]}" üslupla yeniden yazılacak. Devam etmek istiyor musunuz?\n\nMevcut içeriğinizin yedeğini almayı unutmayın!`)) {
            return;
        }
        
        rewriteToneBtn.disabled = true;
        toneSelector.disabled = true;
        toneLoading.classList.remove('d-none');
        
        try {
            const response = await fetch('{{ route("articles.ai.rewrite-tone", $article) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tone: selectedTone
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Scroll yukarı
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // İçeriği güncelle
                contentTextarea.value = data.content;
                
                // Karakter sayısını güncelle (varsa)
                const charCount = document.getElementById('charCount');
                if (charCount) {
                    charCount.textContent = data.length;
                }
                
                // Başarı mesajı
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    <strong>✨ Başarılı!</strong> 
                    İçerik "${data.tone.name}" üsluba dönüştürüldü. 
                    (${data.length} karakter)
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                contentTextarea.parentElement.insertBefore(alert, contentTextarea);
                
                // Otomatik kapat
                setTimeout(() => alert.remove(), 8000);
                
            } else {
                alert('Hata: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Bir hata oluştu: ' + error.message);
        } finally {
            rewriteToneBtn.disabled = false;
            toneSelector.disabled = false;
            toneLoading.classList.add('d-none');
        }
    });
    
    // İçerik Genişletme (mevcut kod)
    const expandBtn = document.getElementById('expandBtn');
    const expandWords = document.getElementById('expandWords');
    const expandLoading = document.getElementById('expandLoading');

    expandBtn?.addEventListener('click', async function() {
        if (!confirm('Mevcut içerik genişletilecek. Devam etmek istiyor musunuz?')) {
            return;
        }

        expandBtn.disabled = true;
        expandLoading.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("articles.ai.expand", $article) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    words: parseInt(expandWords.value)
                })
            });

            const data = await response.json();

            if (data.success) {
                contentTextarea.value = data.content;
                
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    <strong>Başarılı!</strong> İçerik genişletildi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                expandLoading.parentElement.insertBefore(alert, expandLoading);
                setTimeout(() => alert.remove(), 5000);
            } else {
                alert('Hata: ' + data.message);
            }
        } catch (error) {
            alert('Bir hata oluştu: ' + error.message);
        } finally {
            expandBtn.disabled = false;
            expandLoading.classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection