<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Services\GeminiService;

class ArticleController extends Controller
{
    protected $gemini;
    
    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }
    
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('articles.index', compact('articles'));
    }
    
    public function create()
    {
        return view('articles.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
        
        $article = Article::create($request->all());
        
        return redirect()->route('articles.edit', $article)
            ->with('success', 'Makale oluşturuldu!');
    }
    
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }
    
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
        
        $article->update($request->all());
        
        return back()->with('success', 'Makale güncellendi!');
    }
    
    
    public function aiSummarize(Article $article)
    {
        try {
            $summary = $this->gemini->summarize($article->content);
            $article->update(['summary' => $summary]);
            
            return back()->with('success', 'Özet oluşturuldu!');
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }
    
    public function aiTags(Article $article)
    {
        try {
            $tags = $this->gemini->generateTags($article->content);
            $article->update(['tags' => $tags]);
            
            return back()->with('success', 'Etiketler oluşturuldu!');
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }
    
    public function aiSeo(Article $article)
{
    try {
        $seo = $this->gemini->generateSEO($article->title, $article->content);
        
        
        preg_match('/SEO Başlığı:\s*(.+?)(?:\n|$)/u', $seo, $titleMatch);
        preg_match('/Meta Açıklama:\s*(.+?)(?:\n|$)/u', $seo, $descMatch);
        
        $seoTitle = isset($titleMatch[1]) ? trim($titleMatch[1]) : 'SEO Başlık';
        $seoDesc = isset($descMatch[1]) ? trim($descMatch[1]) : 'SEO Açıklama';
        
        $article->update([
            'seo_title' => $seoTitle,
            'seo_description' => $seoDesc
        ]);
        
        return back()->with('success', 'SEO bilgileri oluşturuldu!');
    } catch (\Exception $e) {
        return back()->with('error', 'Hata: ' . $e->getMessage());
    }
}
    
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')
            ->with('success', 'Makale silindi!');
    }

    public function generateContent(Request $request)
{
    $request->validate([
        'topic' => 'required|string|max:255',
        'character_limit' => 'required|integer|min:500|max:10000'
    ]);

    try {
        set_time_limit(120); 
        
        $content = $this->gemini->generateArticle(
            $request->topic, 
            $request->character_limit
        );
        
        return response()->json([
            'success' => true,
            'content' => $content,
            'title' => $request->topic,
            'length' => strlen($content)
        ]);
        
    } catch (\Exception $e) {
        \Log::error('AI Content Generation Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Hata: ' . $e->getMessage()
        ], 500);
    }
}

public function quickGenerate(Request $request)
{
    $request->validate([
        'topic' => 'required|string|max:255'
    ]);

    try {
        set_time_limit(120);
        
        $content = $this->gemini->generateQuickArticle($request->topic);
        
        return response()->json([
            'success' => true,
            'content' => $content,
            'title' => $request->topic,
            'length' => strlen($content)
        ]);
        
    } catch (\Exception $e) {
        \Log::error('AI Quick Generation Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Hata: ' . $e->getMessage()
        ], 500);
    }
}

public function expandContent(Article $article, Request $request)
{
    try {
        $words = $request->input('words', 500);
        $expandedContent = $this->gemini->expandContent($article->content, $words);
        
        return response()->json([
            'success' => true,
            'content' => $expandedContent
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Hata: ' . $e->getMessage()
        ], 500);
    }
}
}
