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
    private string $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

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
        
        // Lấy thông tin sản phẩm để cung cấp context tốt hơn
        $products = Product::with(['category', 'variants'])->take(20)->get();
        $categories = Category::take(15)->get();
        
        $productInfo = $products->map(function($product) {
            $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
            
            // Lấy giá từ variant đầu tiên hoặc giá thấp nhất
            $minPrice = 0;
            $maxPrice = 0;
            if ($product->variants && $product->variants->count() > 0) {
                $minPrice = $product->variants->min('price');
                $maxPrice = $product->variants->max('price');
            }
            
            $formattedMinPrice = number_format($minPrice);
            $formattedMaxPrice = number_format($maxPrice);
            $priceRange = $minPrice == $maxPrice ? "{$formattedMinPrice}đ" : "{$formattedMinPrice}đ – {$formattedMaxPrice}đ";
            
            $description = $product->description ? ' - ' . substr($product->description, 0, 100) : '';
            
            return "- {$product->name} | Giá: {$priceRange} | Danh mục: {$categoryName}{$description}";
        })->implode("\n");
        
        $categoryInfo = $categories->map(function($category) {
            return "- {$category->name}";
        })->implode("\n");
        
        // Tạo context về cửa hàng cây cảnh với thông tin sản phẩm thực tế
        $systemPrompt = "Bạn là trợ lý AI thân thiện và chuyên nghiệp của cửa hàng cây cảnh 79Store - chuyên cung cấp cây cảnh chất lượng cao tại Việt Nam.

**THÔNG TIN CỬA HÀNG:**
- Tên cửa hàng: 79Store
- Chuyên ngành: Cây cảnh, cây trong nhà, cây ngoài trời, chậu và phụ kiện
- Dịch vụ: Tư vấn chuyên sâu, hướng dẫn chăm sóc, giao hàng tận nơi

**DANH MUC SẢN PHẨM:**
{$categoryInfo}

**SẢN PHẨM CỤ THỂ VÀ GIÁ:**
{$productInfo}

**VAI TRÒ CỦA BẠN:**
1. Tư vấn cây cảnh phù hợp theo không gian, điều kiện sống, sở thích
2. Hướng dẫn chăm sóc chi tiết (tưới nước, phân bón, ánh sáng, nhiệt độ, độ ẩm)
3. Gợi ý chậu và phụ kiện phù hợp với từng loại cây
4. Giải đáp thắc mắc về sản phẩm, giá cả trong cửa hàng
5. Tư vấn bố trí cây trong nhà/văn phòng theo phong thủy
6. Hướng dẫn quy trình mua hàng, chính sách bảo hành

**CÁCH TRẢ LỜI:**
- Luôn thân thiện, nhiệt tình và chuyên nghiệp
- Sử dụng emoji phù hợp để tạo cảm giác gần gũi
- Đưa ra lời khuyên thực tế, dễ thực hiện
- Khi nói về giá cả, LUÔN tham khảo danh sách sản phẩm cụ thể ở trên
- Khi khách hỏi về mức giá hoặc sản phẩm trong khoảng giá nào đó, hãy tìm và liệt kê các sản phẩm phù hợp kèm giá chính xác
- Khi tư vấn chậu, hãy xem xét kích thước cây, loại cây và điều kiện môi trường
- Nếu không tìm thấy thông tin cụ thể, hãy tư vấn dựa trên kinh nghiệm chung về cây cảnh

**KIẾN THỨC CHUYÊN MÔN:**
- Hiểu biết sâu về đặc tính từng loại cây
- Nắm rõ cách chăm sóc theo mùa và điều kiện khí hậu Việt Nam  
- Biết cách phối hợp chậu và cây hài hòa
- Hiểu về phong thủy và ý nghĩa cây cảnh

**LƯU Ý QUAN TRỌNG:**
- Nếu được hỏi về chủ đề không liên quan cây cảnh, hãy lịch sự chuyển hướng
- Luôn khuyến khích khách hàng liên hệ trực tiếp nếu cần tư vấn chi tiết hơn
- Khi không chắc chắn về thông tin, hãy thẳng thắn nói và đề xuất liên hệ trực tiếp
- Luôn đề cập đến việc ghé thăm cửa hàng để xem sản phẩm trực tiếp";

        try {
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $systemPrompt . "\n\n**Câu hỏi của khách hàng:** " . $userMessage
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1500,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH', 
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
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
                Log::error('Gemini API Error: ' . $response->body());
                throw new \Exception('API request failed: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            // Chỉ fallback cơ bản nhất khi API hoàn toàn fail
            return response()->json([
                'success' => true,
                'message' => $this->getMinimalFallback($userMessage)
            ]);
        }
    }

    private function getMinimalFallback($message)
    {
        $message = strtolower($message);
        
        // Chỉ xử lý những trường hợp cực kỳ cơ bản
        if (strpos($message, 'chào') !== false || strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
            return "Xin chào! 👋 Chào mừng bạn đến với 79Store - cửa hàng cây cảnh uy tín. Tôi đang gặp sự cố kỹ thuật nhỏ nhưng vẫn sẵn sàng hỗ trợ bạn! Bạn có thể:\n\n🌿 Xem sản phẩm tại phần Shop\n📞 Liên hệ trực tiếp để được tư vấn\n⏳ Hoặc thử hỏi lại sau ít phút\n\nCảm ơn bạn đã tin tưởng 79Store! 😊";
        }
        
        // Fallback chung cho mọi trường hợp khác
        return "Xin lỗi, tôi đang gặp sự cố kỹ thuật tạm thời. 😔\n\n🌿 **79Store luôn sẵn sàng hỗ trợ bạn:**\n• Ghé thăm phần Shop để xem sản phẩm\n• Liên hệ trực tiếp để được tư vấn chi tiết\n• Thử hỏi lại sau vài phút\n\nCảm ơn bạn đã kiên nhẫn! 🙏";
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
