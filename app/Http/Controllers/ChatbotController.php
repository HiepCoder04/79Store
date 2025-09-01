<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;

class ChatbotController extends Controller
{
    private $geminiApiKey;
    private string $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
        
        // Debug: Kiểm tra API key
        if (!$this->geminiApiKey) {
            Log::error('GEMINI_API_KEY not found in environment');
        } else {
            Log::info('GEMINI_API_KEY loaded: ' . substr($this->geminiApiKey, 0, 10) . '...');
        }
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $userMessage = $request->input('message');
        
        // Log user message
        Log::info('User message: ' . $userMessage);
        
        try {
            // Lấy thông tin sản phẩm với xử lý encoding an toàn
            $products = Product::with(['category', 'variants'])->take(20)->get();
            $categories = Category::take(15)->get();
            
            $productInfo = $products->map(function($product) {
                try {
                    $categoryName = $product->category ? $this->cleanText($product->category->name) : 'Chưa phân loại';
                    
                    // Lấy giá từ variant với xử lý chi tiết hơn
                    $minPrice = 0;
                    $maxPrice = 0;
                    $priceRange = "Liên hệ";
                    
                    if ($product->variants && $product->variants->count() > 0) {
                        $prices = $product->variants->pluck('price')->filter(function($price) {
                            return $price > 0;
                        });
                        
                        if ($prices->count() > 0) {
                            $minPrice = $prices->min();
                            $maxPrice = $prices->max();
                            
                            $formattedMinPrice = number_format($minPrice, 0, ',', '.');
                            $formattedMaxPrice = number_format($maxPrice, 0, ',', '.');
                            
                            if ($minPrice == $maxPrice) {
                                $priceRange = "{$formattedMinPrice}đ";
                            } else {
                                $priceRange = "{$formattedMinPrice}đ - {$formattedMaxPrice}đ";
                            }
                        }
                    }
                    
                    $productName = $this->cleanText($product->name);
                    $description = $product->description ? ' - ' . $this->cleanText(substr($product->description, 0, 80)) : '';
                    
                    return "- {$productName} | Giá: {$priceRange} | Danh mục: {$categoryName}{$description}";
                } catch (\Exception $e) {
                    Log::warning('Error processing product: ' . $e->getMessage());
                    return "- Sản phẩm | Giá: Liên hệ | Danh mục: Cây cảnh";
                }
            })->filter()->implode("\n");
            
            $categoryInfo = $categories->map(function($category) {
                try {
                    return "- " . $this->cleanText($category->name);
                } catch (\Exception $e) {
                    return "- Danh mục cây cảnh";
                }
            })->filter()->implode("\n");
            
        } catch (\Exception $e) {
            Log::error('Error loading products/categories: ' . $e->getMessage());
            $productInfo = "- Các loại cây cảnh đa dạng | Giá: Liên hệ";
            $categoryInfo = "- Cây trong nhà\n- Cây ngoài trời\n- Chậu và phụ kiện";
        }
        
        // Tạo system prompt chi tiết hơn về giá cả
        $systemPrompt = "Bạn là trợ lý AI của cửa hàng cây cảnh 79Store. Hãy trả lời thân thiện và chi tiết về cây cảnh.

THÔNG TIN CỬA HÀNG:
- Tên: 79Store
- Chuyên: Cây cảnh, cây trong nhà, cây ngoài trời, chậu và phụ kiện
- Dịch vụ: Tư vấn chuyên sâu, hướng dẫn chăm sóc, giao hàng tận nơi

SẢN PHẨM VÀ GIÁ CỤ THỂ:
{$productInfo}

DANH MỤC SẢN PHẨM:
{$categoryInfo}

HƯỚNG DẪN TƯ VẤN:
- Khi khách hỏi về giá, hãy tham khảo danh sách sản phẩm cụ thể ở trên
- Khi khách hỏi trong khoảng giá nào đó, hãy liệt kê các sản phẩm phù hợp
- Luôn đề cập đến việc ghé thăm cửa hàng để xem sản phẩm trực tiếp
- Tư vấn cây phù hợp với không gian, điều kiện chăm sóc
- Hướng dẫn chăm sóc cây chi tiết (tưới nước, ánh sáng, phân bón)";

        try {
            $apiUrl = $this->geminiApiUrl . '?key=' . $this->geminiApiKey;
            
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $systemPrompt . "\n\nCâu hỏi: " . $userMessage
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ];
            
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $requestData);

            Log::info('API Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $botReply = $this->cleanText($data['candidates'][0]['content']['parts'][0]['text']);
                    
                    return response()->json([
                        'success' => true,
                        'message' => $botReply
                    ]);
                } else {
                    Log::error('Invalid response format from Gemini API');
                    throw new \Exception('Invalid response format from Gemini API');
                }
            } else {
                Log::error('Gemini API Error - Status: ' . $response->status());
                throw new \Exception('API request failed: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'message' => $this->getMinimalFallback($userMessage)
            ]);
        }
    }

    /**
     * Làm sạch text và xử lý encoding UTF-8
     */
    private function cleanText($text)
    {
        if (empty($text)) {
            return '';
        }
        
        // Chuyển đổi encoding và làm sạch text
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        $text = trim($text);
        
        return $text;
    }

    private function getMinimalFallback($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'chào') !== false || strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
            return "Xin chào! 👋 Chào mừng bạn đến với 79Store - cửa hàng cây cảnh uy tín!\n\n🌿 Tôi có thể tư vấn:\n• Cây phù hợp với không gian\n• Cách chăm sóc cây cảnh\n• Chậu và phụ kiện\n• Giá cả sản phẩm\n\nHãy hỏi tôi bất cứ điều gì về cây cảnh nhé! 😊";
        }
        
        return "Xin chào! 🌿 Tôi là trợ lý AI của 79Store.\n\nTôi có thể giúp bạn:\n• Tư vấn chọn cây phù hợp\n• Hướng dẫn chăm sóc cây\n• Thông tin về sản phẩm và giá cả\n• Gợi ý cây theo phong thủy\n\nBạn muốn tìm hiểu về loại cây nào? 😊";
    }

    public function getSuggestions()
    {
        $suggestions = [
            'Cây nào phù hợp trồng trong nhà có ít ánh sáng?',
            'Làm thế nào để chăm sóc cây cho người mới?',
            'Cây nào dễ trồng cho người mới bắt đầu?',
            'Chậu nào phù hợp với cây cảnh?',
            'Giá cả sản phẩm như thế nào?',
            'Cây phong thủy nào tốt cho văn phòng?',
            'Làm sao chọn cây theo không gian nhà?',
            'Tư vấn cây hoa đẹp dễ chăm sóc?'
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
}

