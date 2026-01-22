<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1/models/';
    protected $model = 'gemini-2.5-flash'; 
    
    public function __construct()
    {
        $this->apiKey = config('services.google_ai.api_key');
    }
    
    protected function generateContent($prompt, $maxTokens = 4000)
    {
        $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;
        
        $response = Http::timeout(60)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.8,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => $maxTokens, 
                'stopSequences' => []
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE'
                ]
            ]
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            // Yanıt kontrolü
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $data['candidates'][0]['content']['parts'][0]['text'];
                
                // Finish reason kontrolü
                $finishReason = $data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
                
                if ($finishReason === 'MAX_TOKENS') {
                    \Log::warning('Gemini API response was truncated due to MAX_TOKENS');
                }
                
                return $text;
            }
            
            throw new \Exception('API yanıtında içerik bulunamadı');
        }
        
        throw new \Exception('API Hatası: ' . $response->body());
    }
    
    public function summarize($text)
    {
        $prompt = "Bu Türkçe metni kısa ve öz şekilde özetle (maksimum 2-3 cümle):\n\n{$text}";
        return $this->generateContent($prompt, 500);
    }
    
    public function generateTags($text)
    {
        $prompt = "Bu içerik için 5 adet Türkçe etiket öner. Sadece etiketleri virgülle ayırarak yaz, başka açıklama yazma:\n\n{$text}";
        return $this->generateContent($prompt, 200);
    }
    
    public function generateSEO($title, $content)
    {
        $prompt = "Bu makale için:\n1. SEO Başlığı (maksimum 60 karakter)\n2. Meta Açıklama (maksimum 160 karakter)\noluştur.\n\nBaşlık: {$title}\nİçerik: " . substr($content, 0, 500) . "\n\nYanıtı şu formatta ver:\nSEO Başlığı: [başlık]\nMeta Açıklama: [açıklama]";
        return $this->generateContent($prompt, 500);
    }
    
    public function improveContent($text)
    {
        $prompt = "Bu metni daha profesyonel ve akıcı hale getir. Türkçe dilbilgisi kurallarına uy:\n\n{$text}";
        return $this->generateContent($prompt, 4000);
    }

    

public function generateArticle($topic, $characterLimit = 2000)
    {
        // Karakter sayısından token sayısını hesapla (Türkçe için ~3 karakter = 1 token)
        $estimatedTokens = intval($characterLimit / 3);
        $maxTokens = min(max($estimatedTokens, 1000), 8000); // 1000 ile 8000 arası
        
        $wordLimit = intval($characterLimit / 6); 
        
        $prompt = "Sen profesyonel bir içerik yazarısın. Aşağıdaki konu hakkında detaylı bir Türkçe blog makalesi yaz.

KONU: {$topic}

GEREKSINIMLER:
1. Makale TAM OLARAK yaklaşık {$characterLimit} karakter ({$wordLimit} kelime) uzunluğunda olmalı
2. Giriş: Konuyu tanıt ve neden önemli olduğunu açıkla
3. Ana Bölümler: Konuyu detaylı şekilde ele al, örnekler ver
4. Alt başlıklar kullanma, düz metin olarak yaz
5. Sonuç: Özet ve son düşünceler
6. Akıcı, anlaşılır ve profesyonel bir dil kullan
7. Gereksiz tekrarlardan kaçın
8. ÖNEMLI: Makalenin tamamını yaz, yarıda kesme

Şimdi {$characterLimit} karakterlik makaleyi yaz:";

        return $this->generateContent($prompt, $maxTokens);
    }
    
    public function generateQuickArticle($topic)
    {
        $prompt = "Sen profesyonel bir içerik yazarısın. Aşağıdaki konu hakkında kısa ve öz bir Türkçe blog makalesi yaz.

KONU: {$topic}

GEREKSINIMLER:
1. Yaklaşık 600-1000 kelime uzunluğunda olmalı
2. Giriş, gelişme ve sonuç bölümleri olsun
3. Net ve anlaşılır bir dil kullan
4. Alt başlık kullanma, düz metin olarak yaz
5. ÖNEMLI: Makalenin tamamını yaz, yarıda kesme

Şimdi makaleyi yaz:";

        return $this->generateContent($prompt, 4000);
    }
    
    public function expandContent($existingContent, $additionalWords = 500)
    {
        $estimatedTokens = intval(($additionalWords * 6) / 3); // Kelime -> Karakter -> Token
        $maxTokens = min(max($estimatedTokens, 1000), 6000);
        
        $prompt = "Aşağıdaki makaleyi daha detaylı hale getir. Yaklaşık {$additionalWords} kelime daha ekle.

KURALLAR:
1. Mevcut içeriği KAYBETMEDENgeniş
2. Yeni paragraflar ve detaylar ekle
3. Akışı ve üslubu koru
4. Tekrara düşme, yeni bilgiler ekle
5. ÖNEMLI: Tam genişletilmiş versiyonu yaz

Mevcut İçerik:
{$existingContent}

Şimdi genişletilmiş makaleyi yaz:";

        return $this->generateContent($prompt, $maxTokens);
    }

    public function rewriteWithTone($content, $tone)
{
    $tones = [
        'formal' => [
            'name' => 'Resmi ve Profesyonel',
            'desc' => 'Kurumsal dil, akademik üslup, resmi ifadeler kullan. "Siz" dili kullan.'
        ],
        'casual' => [
            'name' => 'Samimi ve Gündelik',
            'desc' => 'Arkadaşça dil, günlük konuşma tarzı, samimi ifadeler kullan. "Sen" dili kullan.'
        ],
        'technical' => [
            'name' => 'Teknik ve Detaylı',
            'desc' => 'Teknik terimler, detaylı açıklamalar, bilimsel yaklaşım kullan.'
        ],
        'persuasive' => [
            'name' => 'İkna Edici ve Satış Odaklı',
            'desc' => 'Okuyucuyu ikna et, faydalar vurgula, harekete geçirici ifadeler kullan.'
        ],
        'emotional' => [
            'name' => 'Duygusal ve Etkileyici',
            'desc' => 'Duygulara hitap et, hikaye anlatımı kullan, empati kur.'
        ],
        'humorous' => [
            'name' => 'Eğlenceli ve Mizahi',
            'desc' => 'Espri yap, eğlenceli benzetmeler kullan, hafif bir üslup kullan.'
        ]
    ];
    
    $toneInfo = $tones[$tone] ?? $tones['formal'];
    
    $prompt = "Sen profesyonel bir içerik editörüsün. Aşağıdaki metni {$toneInfo['name']} bir üslupla yeniden yaz.

KURALLAR:
- {$toneInfo['desc']}
- Ana mesajı ve bilgileri koru
- Paragraf yapısını koru
- Türkçe dilbilgisi kurallarına uy
- İçeriğin uzunluğunu aynı tut
- Başlık ekleme, sadece içeriği yeniden yaz

Orijinal Metin:
{$content}

Yeniden yazılmış metin:";

    return $this->generateContent($prompt, 6000);
}

public function getToneInfo($tone)
{
    $tones = [
        'formal' => [
            'icon' => '📋',
            'name' => 'Resmi',
            'description' => 'Profesyonel ve kurumsal dil'
        ],
        'casual' => [
            'icon' => '💬',
            'name' => 'Samimi',
            'description' => 'Arkadaşça ve gündelik üslup'
        ],
        'technical' => [
            'icon' => '🔧',
            'name' => 'Teknik',
            'description' => 'Detaylı ve bilimsel anlatım'
        ],
        'persuasive' => [
            'icon' => '💰',
            'name' => 'İkna Edici',
            'description' => 'Satış odaklı ve etkili'
        ],
        'emotional' => [
            'icon' => '❤️',
            'name' => 'Duygusal',
            'description' => 'Hissiyata hitap eden'
        ],
        'humorous' => [
            'icon' => '😄',
            'name' => 'Eğlenceli',
            'description' => 'Mizahi ve hafif'
        ]
    ];
    
    return $tones[$tone] ?? $tones['formal'];
}

}