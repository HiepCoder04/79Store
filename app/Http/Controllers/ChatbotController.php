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
        
        // Lấy thông tin sản phẩm để cung cấp context tốt hơn
        $products = Product::with(['category', 'variants'])->take(15)->get();
        $categories = Category::take(10)->get();
        
        $productInfo = $products->map(function($product) {
            $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
            
            // Lấy giá từ variant đầu tiên hoặc giá thấp nhất
            $price = 0;
            if ($product->variants && $product->variants->count() > 0) {
                $price = $product->variants->min('price');
            }
            
            $formattedPrice = number_format($price);
            return "- {$product->name} - Giá: {$formattedPrice}đ (Danh mục: {$categoryName})";
        })->implode("\n");
        
        $categoryInfo = $categories->map(function($category) {
            return "- {$category->name}";
        })->implode("\n");
        
        // Tạo context về cửa hàng cây cảnh với thông tin sản phẩm thực tế
        $systemPrompt = "Bạn là trợ lý AI của cửa hàng cây cảnh 79Store - chuyên cung cấp cây cảnh chất lượng cao.

**Thông tin về cửa hàng:**
- Tên cửa hàng: 79Store
- Chuyên nghiệp: Cây cảnh, cây trong nhà, cây ngoài trời
- Dịch vụ: Tư vấn chăm sóc cây, giao hàng tận nơi

**Danh mục sản phẩm hiện có:**
{$categoryInfo}

**Một số sản phẩm nổi bật:**
{$productInfo}

**Vai trò của bạn:**
- Tư vấn về các loại cây cảnh phù hợp
- Hướng dẫn cách chăm sóc cây (tưới nước, phân bón, ánh sáng, nhiệt độ)
- Gợi ý cây phù hợp với không gian và điều kiện sống
- Giải đáp thắc mắc về sản phẩm trong cửa hàng
- Hướng dẫn quy trình mua hàng và chính sách
- Tư vấn về việc bố trí cây trong nhà/văn phòng

**Cách trả lời:**
- Thân thiện, nhiệt tình và chuyên nghiệp
- Sử dụng emoji phù hợp để tạo cảm giác gần gũi
- Đưa ra lời khuyên thực tế và dễ thực hiện
- Khi khách hàng hỏi về giá cả hoặc sản phẩm cụ thể, LUÔN tham khảo danh sách sản phẩm với giá ở trên
- Nếu khách hàng hỏi về mức giá (ví dụ: có cây nào dưới 500000 không), hãy tìm trong danh sách và liệt kê các sản phẩm phù hợp kèm giá cụ thể
- Nếu được hỏi về chủ đề không liên quan đến cây cảnh, hãy lịch sự chuyển hướng

**Lưu ý:** Luôn khuyến khích khách hàng ghé thăm cửa hàng hoặc liên hệ để được tư vấn trực tiếp nếu cần thiết.";

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
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
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
            
            // Fallback responses cho các câu hỏi thường gặp
            $fallbackResponse = $this->getFallbackResponse($userMessage);
            
            return response()->json([
                'success' => true,
                'message' => $fallbackResponse
            ]);
        }
    }

    private function getFallbackResponse($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'chào') !== false || strpos($message, 'hello') !== false) {
            return "Xin chào! 👋 Chào mừng bạn đến với 79Store. Tôi có thể giúp bạn tư vấn về cây cảnh và cách chăm sóc. Bạn cần hỗ trợ gì?";
        }
        
        // Xử lý câu hỏi về giá cả với dữ liệu thực tế
        if (strpos($message, 'giá') !== false || strpos($message, 'bao nhiêu') !== false) {
            return $this->handlePriceQuery($message);
        }
        
        if (strpos($message, 'giao hàng') !== false || strpos($message, 'ship') !== false) {
            return "79Store có dịch vụ giao hàng tận nơi. Chúng tôi sẽ đóng gói cẩn thận để đảm bảo cây được vận chuyển an toàn. Thời gian giao hàng thường từ 1-3 ngày tùy khu vực. 🚚";
        }
        
        if (strpos($message, 'chăm sóc') !== false || strpos($message, 'tưới') !== false || 
            strpos($message, 'cách trồng') !== false || strpos($message, 'hướng dẫn') !== false) {
            
            // Kiểm tra xem có tên cây cụ thể không
            $plantNames = ['hoa tường vi', 'hoa giấy', 'huyết dụ', 'sen đá'];
            foreach ($plantNames as $plant) {
                if (stripos($message, $plant) !== false) {
                    return $this->getCareInstructions($plant);
                }
            }
            
            return "Việc chăm sóc cây rất quan trọng! 🌱 Một số lưu ý cơ bản:\n- Tưới nước vừa đủ, tránh úng\n- Đặt cây ở nơi có ánh sáng phù hợp\n- Bón phân định kỳ\n- Theo dõi sâu bệnh\nBạn muốn biết cách chăm sóc loại cây nào cụ thể?";
        }
        
        // Xử lý câu hỏi về sản phẩm cụ thể - ưu tiên tìm kiếm tên cây trước
        if (strpos($message, 'cây') !== false || strpos($message, 'sản phẩm') !== false || 
            strpos($message, 'muốn biết') !== false || strpos($message, 'thông tin') !== false) {
            return $this->handleProductQuery($message);
        }
        
        return "Xin lỗi, tôi đang gặp sự cố kỹ thuật nhỏ. 😅 Nhưng tôi luôn sẵn sàng giúp bạn! Bạn có thể:\n- Thử hỏi lại câu hỏi\n- Ghé thăm phần Shop để xem sản phẩm\n- Liên hệ trực tiếp với chúng tôi để được tư vấn chi tiết nhất! 🌿";
    }

    private function handlePriceQuery($message)
    {
        try {
            // Tìm kiếm sản phẩm theo mức giá được đề cập
            if (preg_match('/(\d+)/', $message, $matches)) {
                $priceLimit = (int)$matches[0];
                
                // Tìm sản phẩm có giá dưới mức được hỏi
                $affordableProducts = Product::with(['category', 'variants'])
                    ->whereHas('variants', function($query) use ($priceLimit) {
                        $query->where('price', '<=', $priceLimit);
                    })
                    ->take(5)
                    ->get();
                
                if ($affordableProducts->count() > 0) {
                    $response = "🌿 Tôi tìm thấy " . $affordableProducts->count() . " sản phẩm dưới " . number_format($priceLimit) . "đ:\n\n";
                    
                    foreach ($affordableProducts as $product) {
                        $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
                        
                        // Lấy giá từ variants
                        $price = 0;
                        if ($product->variants && $product->variants->count() > 0) {
                            $price = $product->variants->min('price');
                        }
                        
                        $response .= "🌱 {$product->name}\n";
                        $response .= "   💰 Giá: " . number_format($price) . "đ\n";
                        $response .= "   📂 Danh mục: {$categoryName}\n\n";
                    }
                    
                    $response .= "Bạn có muốn biết thêm thông tin chi tiết về cây nào không? 😊";
                    return $response;
                } else {
                    return "😔 Hiện tại chúng tôi không có sản phẩm nào dưới " . number_format($priceLimit) . "đ. \n\nTuy nhiên, hãy để tôi giới thiệu một số sản phẩm có giá hợp lý:\n\n" . $this->getAffordableProducts();
                }
            }
            
            // Nếu không có số cụ thể, hiển thị một số sản phẩm mẫu với giá
            return $this->getProductsWithPrices();
            
        } catch (\Exception $e) {
            return "Để biết giá cụ thể của từng sản phẩm, bạn có thể xem trong phần Shop hoặc liên hệ trực tiếp với chúng tôi. Giá cả sẽ tùy thuộc vào loại cây và kích thước. 💰";
        }
    }
    
    private function getAffordableProducts()
    {
        try {
            $cheapestProducts = Product::with(['category', 'variants'])
                ->whereHas('variants')
                ->take(3)
                ->get();
            
            $response = "";
            foreach ($cheapestProducts as $product) {
                $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
                
                // Lấy giá thấp nhất từ variants
                $price = 0;
                if ($product->variants && $product->variants->count() > 0) {
                    $price = $product->variants->min('price');
                }
                
                $response .= "🌱 {$product->name} - " . number_format($price) . "đ ({$categoryName})\n";
            }
            
            return $response;
        } catch (\Exception $e) {
            return "Vui lòng ghé thăm phần Shop để xem các sản phẩm với giá tốt nhất! 🌿";
        }
    }
    
    private function getProductsWithPrices()
    {
        try {
            $products = Product::with(['category', 'variants'])->take(5)->get();
            
            if ($products->count() > 0) {
                $response = "💰 Đây là một số sản phẩm và giá của chúng tôi:\n\n";
                
                foreach ($products as $product) {
                    $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
                    
                    // Lấy giá từ variants
                    $price = 0;
                    if ($product->variants && $product->variants->count() > 0) {
                        $price = $product->variants->min('price');
                    }
                    
                    $response .= "🌱 {$product->name}\n";
                    $response .= "   💰 Giá: " . number_format($price) . "đ\n";
                    $response .= "   📂 Danh mục: {$categoryName}\n\n";
                }
                
                $response .= "Bạn có câu hỏi gì khác về sản phẩm không? 😊";
                return $response;
            }
            
            return "Để biết giá cụ thể của từng sản phẩm, bạn có thể xem trong phần Shop hoặc liên hệ trực tiếp với chúng tôi. 💰";
            
        } catch (\Exception $e) {
            return "Để biết giá cụ thể của từng sản phẩm, bạn có thể xem trong phần Shop hoặc liên hệ trực tiếp với chúng tôi. 💰";
        }
    }

    private function handleProductQuery($message)
    {
        try {
            // Đầu tiên, tìm kiếm sản phẩm theo tên chính xác
            $exactProducts = Product::where('name', 'LIKE', '%' . $message . '%')
                ->with(['category', 'variants'])
                ->get();
            
            if ($exactProducts->count() > 0) {
                $response = "🌿 Tôi tìm thấy thông tin về sản phẩm:\n\n";
                
                foreach ($exactProducts as $product) {
                    $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
                    
                    // Lấy giá từ variants
                    $price = 0;
                    if ($product->variants && $product->variants->count() > 0) {
                        $price = $product->variants->min('price');
                    }
                    
                    $response .= "🌱 **{$product->name}**\n";
                    $response .= "   💰 Giá: " . number_format($price) . "đ\n";
                    $response .= "   📂 Danh mục: {$categoryName}\n";
                    if ($product->description) {
                        $response .= "   📝 Mô tả: " . substr($product->description, 0, 100) . "...\n";
                    }
                    $response .= "\n";
                }
                
                $response .= "Bạn có muốn biết thêm về cách chăm sóc cây này không? 🌿";
                return $response;
            }
            
            // Tìm kiếm sản phẩm dựa trên từ khóa chi tiết hơn
            $keywords = [
                'hoa tường vi' => ['hoa tường vi', 'tường vi'],
                'hoa giấy' => ['hoa giấy', 'giấy'],
                'huyết dụ' => ['huyết dụ', 'huyết'],
                'sen đá' => ['sen đá', 'sen'],
                'hoa hồng' => ['hoa hồng', 'hồng'],
                'cây xanh' => ['cây xanh', 'xanh'],
                'trong nhà' => ['trong nhà', 'nhà'],
                'ngoài trời' => ['ngoài trời', 'trời'],
                'dễ trồng' => ['dễ trồng', 'dễ'],
                'khó chăm sóc' => ['khó chăm sóc', 'khó']
            ];
            
            $foundKeyword = null;
            $searchTerms = [];
            
            foreach ($keywords as $mainKeyword => $variations) {
                foreach ($variations as $variation) {
                    if (stripos($message, $variation) !== false) {
                        $foundKeyword = $mainKeyword;
                        $searchTerms = $variations;
                        break 2;
                    }
                }
            }
            
            if ($foundKeyword && !empty($searchTerms)) {
                $query = Product::with(['category', 'variants']);
                
                // Tìm kiếm theo nhiều điều kiện
                $query->where(function($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('name', 'LIKE', '%' . $term . '%')
                          ->orWhere('description', 'LIKE', '%' . $term . '%');
                    }
                });
                
                $products = $query->take(3)->get();
                
                if ($products->count() > 0) {
                    $response = "🌿 Tôi tìm thấy " . $products->count() . " sản phẩm liên quan đến '{$foundKeyword}':\n\n";
                    
                    foreach ($products as $product) {
                        $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
                        
                        // Lấy giá từ variants
                        $price = 0;
                        if ($product->variants && $product->variants->count() > 0) {
                            $price = $product->variants->min('price');
                        }
                        
                        $response .= "🌱 **{$product->name}**\n";
                        $response .= "   💰 Giá: " . number_format($price) . "đ\n";
                        $response .= "   📂 Danh mục: {$categoryName}\n";
                        if ($product->description) {
                            $response .= "   📝 Mô tả: " . substr($product->description, 0, 80) . "...\n";
                        }
                        $response .= "\n";
                    }
                    
                    $response .= "Bạn có muốn biết thêm về cách chăm sóc loại cây này không? 🌿";
                    return $response;
                }
            }
            
            // Nếu không tìm thấy từ khóa cụ thể, hiển thị sản phẩm phổ biến
            $popularProducts = Product::with(['category', 'variants'])->take(4)->get();
            
            if ($popularProducts->count() > 0) {
                $response = "🌿 Đây là một số sản phẩm phổ biến của chúng tôi:\n\n";
                
                foreach ($popularProducts as $product) {
                    $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
                    
                    // Lấy giá từ variants
                    $price = 0;
                    if ($product->variants && $product->variants->count() > 0) {
                        $price = $product->variants->min('price');
                    }
                    
                    $response .= "🌱 {$product->name} - " . number_format($price) . "đ ({$categoryName})\n";
                }
                
                $response .= "\nBạn muốn biết thêm về cây nào cụ thể? 😊";
                return $response;
            }
            
            return "Hiện tại chúng tôi có nhiều loại cây cảnh đẹp. Bạn có thể ghé thăm phần Shop để xem chi tiết! 🌿";
            
        } catch (\Exception $e) {
            return "Hiện tại chúng tôi có nhiều loại cây cảnh đẹp. Bạn có thể ghé thăm phần Shop để xem chi tiết! 🌿";
        }
    }

    private function getCareInstructions($plantName)
    {
        $careGuides = [
            'hoa tường vi' => [
                'title' => 'Cây Hoa Tường Vi',
                'care' => [
                    '💧 Tưới nước: 2-3 lần/tuần, tránh úng nước',
                    '☀️ Ánh sáng: Ưa ánh sáng gián tiếp, tránh nắng gắt',
                    '🌡️ Nhiệt độ: 20-30°C',
                    '🌱 Phân bón: Bón phân NPK loãng 2 tuần/lần',
                    '✂️ Cắt tỉa: Cắt bỏ lá khô, hoa tàn để kích thích ra hoa mới'
                ]
            ],
            'hoa giấy' => [
                'title' => 'Cây Hoa Giấy',
                'care' => [
                    '💧 Tưới nước: Tưới ít, đất khô mới tưới',
                    '☀️ Ánh sáng: Cần nhiều ánh sáng trực tiếp',
                    '🌡️ Nhiệt độ: Chịu được nhiệt độ cao',
                    '🌱 Phân bón: Bón phân kali để kích thích ra hoa',
                    '✂️ Cắt tỉa: Cắt tỉa thường xuyên để tạo dáng'
                ]
            ],
            'huyết dụ' => [
                'title' => 'Cây Huyết Dụ',
                'care' => [
                    '💧 Tưới nước: Giữ đất ẩm nhưng không úng',
                    '☀️ Ánh sáng: Ưa bóng râm, tránh nắng trực tiếp',
                    '🌡️ Nhiệt độ: 18-25°C',
                    '🌱 Phân bón: Bón phân hữu cơ loãng',
                    '🍃 Đặc biệt: Thường xuyên phun sương để tăng độ ẩm'
                ]
            ]
        ];
        
        foreach ($careGuides as $plant => $guide) {
            if (stripos($plantName, $plant) !== false) {
                $response = "🌿 **Hướng dẫn chăm sóc {$guide['title']}:**\n\n";
                foreach ($guide['care'] as $instruction) {
                    $response .= $instruction . "\n";
                }
                $response .= "\n💡 **Lưu ý:** Quan sát cây thường xuyên và điều chỉnh chế độ chăm sóc phù hợp với môi trường của bạn!";
                return $response;
            }
        }
        
        return "🌿 Để có hướng dẫn chăm sóc chi tiết nhất, bạn vui lòng liên hệ trực tiếp với chúng tôi. Mỗi loại cây có yêu cầu chăm sóc khác nhau! 😊";
    }

    public function getSuggestions()
    {
        $suggestions = [
            'Cây nào phù hợp trồng trong nhà có ít ánh sáng?',
            'Làm thế nào để chăm sóc cây?',
            'Cây nào dễ trồng cho người mới bắt đầu?',
            'Tần suất tưới nước cho cây cảnh như thế nào?',
            'Cây nào có thể lọc không khí tốt nhất?',
            'Làm sao biết cây cần phân bón?',
            'Cây phong thủy nào nên trồng trong nhà?',
            'Giá cả sản phẩm như thế nào?'
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
}
