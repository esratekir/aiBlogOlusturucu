@extends('layouts.admin')

@section('title', 'Yeni Makale')
@section('page-title', 'Yeni Makale Oluştur')

@section('content')
<form action="{{ route('articles.store') }}" method="POST" id="articleForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Makale Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Başlık / Konu</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" required placeholder="Makale konusunu girin...">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <div class="position-relative">
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="15" required placeholder="Makale içeriği buraya yazılacak...">{{ old('content') }}</textarea>
                            <small class="text-muted">
                                <span id="charCount">0</span> karakter
                            </small>
                        </div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Loading Spinner -->
                    <div id="aiLoading" class="alert alert-info d-none">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>AI makale oluşturuyor, lütfen bekleyin...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- AI İçerik Üretici -->
            <div class="card sticky-top mb-3" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h4 class="header-title text-white mb-0">
                        🤖 AI İçerik Üretici
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
    <label class="form-label">Karakter Limiti</label>
    <input type="number" id="characterLimit" class="form-control" 
           value="3000" min="1000" max="20000" step="500">
    <small class="text-muted">Yaklaşık kelime: <span id="wordEstimate">500</span></small>
    <div class="mt-1">
        <small class="badge bg-info">Kısa: 1000-3000</small>
        <small class="badge bg-warning">Orta: 3000-6000</small>
        <small class="badge bg-success">Uzun: 6000+</small>
    </div>
</div>

                    <button type="button" class="btn btn-success w-100 mb-2" id="generateBtn">
                        <i class="ti ti-sparkles me-1"></i> Tam Makale Oluştur
                    </button>

                    <button type="button" class="btn btn-outline-success w-100 mb-2" id="quickGenerateBtn">
                        <i class="ti ti-bolt me-1"></i> Hızlı Oluştur (Kısa)
                    </button>

                    <div class="alert alert-info mb-0">
                        <small>
                            <strong>İpucu:</strong> Yukarıda konuyu girin, karakter limitini ayarlayın ve "Tam Makale Oluştur" butonuna basın.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Kaydet -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="header-title text-white mb-0">
                        <i class="ti ti-device-floppy"></i> Kaydet
                    </h4>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="ti ti-check me-1"></i> Kaydet ve Düzenle
                    </button>
                    <a href="{{ route('articles.index') }}" class="btn btn-light w-100">
                        <i class="ti ti-x me-1"></i> İptal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const contentTextarea = document.getElementById('content');
    const characterLimit = document.getElementById('characterLimit');
    const wordEstimate = document.getElementById('wordEstimate');
    const charCount = document.getElementById('charCount');
    const generateBtn = document.getElementById('generateBtn');
    const quickGenerateBtn = document.getElementById('quickGenerateBtn');
    const aiLoading = document.getElementById('aiLoading');

    // Karakter sayacı
    contentTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Kelime tahmini güncelle
    characterLimit.addEventListener('input', function() {
        const estimate = Math.round(this.value / 6);
        wordEstimate.textContent = estimate;
    });

    // Tam Makale Oluştur
    generateBtn.addEventListener('click', async function() {
        const topic = titleInput.value.trim();
        const limit = characterLimit.value;

        if (!topic) {
            alert('Lütfen önce bir konu/başlık girin!');
            titleInput.focus();
            return;
        }

        if (contentTextarea.value.trim() && !confirm('Mevcut içerik silinecek. Devam etmek istiyor musunuz?')) {
            return;
        }

        generateBtn.disabled = true;
        quickGenerateBtn.disabled = true;
        aiLoading.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("articles.ai.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    topic: topic,
                    character_limit: parseInt(limit)
                })
            });

            const data = await response.json();

            if (data.success) {
    contentTextarea.value = data.content;
    charCount.textContent = data.content.length;
    
    // Başarı mesajı
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        <strong>Başarılı!</strong> ${data.length} karakterlik makale oluşturuldu.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    aiLoading.parentElement.insertBefore(alert, aiLoading);
    setTimeout(() => alert.remove(), 5000);
} else {
                alert('Hata: ' + data.message);
            }
        } catch (error) {
            alert('Bir hata oluştu: ' + error.message);
        } finally {
            generateBtn.disabled = false;
            quickGenerateBtn.disabled = false;
            aiLoading.classList.add('d-none');
        }
    });

    // Hızlı Oluştur
    quickGenerateBtn.addEventListener('click', async function() {
        const topic = titleInput.value.trim();

        if (!topic) {
            alert('Lütfen önce bir konu/başlık girin!');
            titleInput.focus();
            return;
        }

        if (contentTextarea.value.trim() && !confirm('Mevcut içerik silinecek. Devam etmek istiyor musunuz?')) {
            return;
        }

        generateBtn.disabled = true;
        quickGenerateBtn.disabled = true;
        aiLoading.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("articles.ai.quick") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    topic: topic
                })
            });

            const data = await response.json();

            if (data.success) {
                contentTextarea.value = data.content;
                charCount.textContent = data.content.length;
                
                // Başarı mesajı
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    <strong>Başarılı!</strong> Kısa makale oluşturuldu.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                aiLoading.parentElement.insertBefore(alert, aiLoading);
                setTimeout(() => alert.remove(), 5000);
            } else {
                alert('Hata: ' + data.message);
            }
        } catch (error) {
            alert('Bir hata oluştu: ' + error.message);
        } finally {
            generateBtn.disabled = false;
            quickGenerateBtn.disabled = false;
            aiLoading.classList.add('d-none');
        }
    });
});
</script>
@endpush