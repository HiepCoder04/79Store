<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private $geminiApiKey;
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $userMessage = $request->input('message');
        
        // Tạo context về cửa hàng cây cảnh
        $systemPrompt = "Bạn là trợ lý AI của cửa hàng cây cảnh 79Store. Bạn chuyên tư vấn về:
        - Các loại cây cảnh, cây trong nhà, cây ngoài trời
        - Cách chăm sóc cây (tưới nước, phân bón, ánh sáng)
        - Lựa chọn cây phù hợp với không gian
        - Giải đáp thắc mắc về sản phẩm
        - Hướng dẫn mua hàng và chính sách
        
        Hãy trả lời một cách thân thiện, hữu ích và chuyên nghiệp. Nếu được hỏi về thông tin không liên quan đến cây cảnh, hãy lịch sự chuyển hướng về chủ đề cây cảnh.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $systemPrompt . "\n\nCâu hỏi của khách hàng: " . $userMessage
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $botReply = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    return response()->json([
                        'success' => true,
                        'message' => $botReply
                    ]);
                } else {
                    throw new \Exception('Invalid response format from Gemini API');
                }
            } else {
                throw new \Exception('API request failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau hoặc liên hệ trực tiếp với chúng tôi.'
            ], 500);
        }
    }

    public function getSuggestions()
    {
        $suggestions = [
            'Cây nào phù hợp trồng trong nhà?',
            'Làm thế nào để chăm sóc cây sen đá?',
            'Cây nào dễ trồng cho người mới bắt đầu?',
            'Tần suất tưới nước cho cây cảnh như thế nào?',
            'Cây nào có thể lọc không khí tốt?',
            'Làm sao biết cây cần phân bón?'
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
}
