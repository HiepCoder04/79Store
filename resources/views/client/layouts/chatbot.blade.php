<!-- Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <!-- Chatbot Toggle Button -->
    <div id="chatbot-toggle" class="chatbot-toggle">
        <i class="fa fa-comments"></i>
        <span class="chatbot-notification" id="chatbot-notification">1</span>
    </div>

    <!-- Chatbot Container -->
    <div id="chatbot-container" class="chatbot-container">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <img src="{{ asset('assets/img/core-img/leaf.png') }}" alt="79Store" class="chatbot-avatar">
                <div>
                    <h4>Trợ lý AI - 79Store</h4>
                    <p>Chuyên gia tư vấn cây cảnh</p>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="message bot-message">
                <div class="message-avatar">
                    <img src="{{ asset('assets/img/core-img/leaf.png') }}" alt="Bot">
                </div>
                <div class="message-content">
                    <p>Xin chào! 👋 Tôi là trợ lý AI của 79Store. Tôi có thể giúp bạn tư vấn về cây cảnh, cách chăm sóc và lựa chọn cây phù hợp. Bạn cần hỗ trợ gì?</p>
                </div>
            </div>
        </div>

        <!-- Quick Suggestions -->
        <div id="chatbot-suggestions" class="chatbot-suggestions">
            <div class="suggestion-item" data-text="Cây nào phù hợp trồng trong nhà có ít ánh sáng?">
                🌿 Cây cho nhà ít ánh sáng?
            </div>
            <div class="suggestion-item" data-text="Làm thế nào để chăm sóc cây sen đá?">
                🌵 Cách chăm sóc cây sen đá?
            </div>
            <div class="suggestion-item" data-text="Cây nào dễ trồng cho người mới bắt đầu?">
                🌱 Cây dễ trồng cho người mới?
            </div>
            <div class="suggestion-item" data-text="Giá cả sản phẩm như thế nào?">
                💰 Giá cả sản phẩm?
            </div>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input">
            <form id="chatbot-form">
                <div class="input-group">
                    <input type="text" id="chatbot-message" placeholder="Nhập câu hỏi của bạn..." maxlength="500" required>
                    <button type="submit" id="chatbot-send">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <div class="chatbot-typing" id="chatbot-typing" style="display: none;">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</div>

<!-- Chatbot Styles -->
<style>
.chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    font-family: 'Arial', sans-serif;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #70C745, #5BA832);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(112, 199, 69, 0.3);
    transition: all 0.3s ease;
    position: relative;
}

.chatbot-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(112, 199, 69, 0.4);
}

.chatbot-toggle i {
    color: white;
    font-size: 24px;
}

.chatbot-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.chatbot-container {
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    position: absolute;
    bottom: 80px;
    right: 0;
    overflow: hidden;
}

.chatbot-header {
    background: linear-gradient(135deg, #70C745, #5BA832);
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chatbot-header-info {
    display: flex;
    align-items: center;
}

.chatbot-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
    background: white;
    padding: 5px;
}

.chatbot-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.chatbot-header p {
    margin: 0;
    font-size: 12px;
    opacity: 0.9;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background 0.3s ease;
}

.chatbot-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
}

.message {
    display: flex;
    margin-bottom: 15px;
    align-items: flex-start;
}

.bot-message {
    justify-content: flex-start;
}

.user-message {
    justify-content: flex-end;
}

.message-avatar {
    width: 30px;
    height: 30px;
    margin-right: 10px;
}

.user-message .message-avatar {
    order: 2;
    margin-right: 0;
    margin-left: 10px;
}

.message-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.4;
}

.bot-message .message-content {
    background: white;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.user-message .message-content {
    background: #70C745;
    color: white;
    border-bottom-right-radius: 5px;
}

.chatbot-suggestions {
    padding: 10px 15px;
    border-top: 1px solid #e9ecef;
    background: white;
}

.suggestion-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 20px;
    padding: 8px 12px;
    margin: 5px 0;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
    display: inline-block;
    margin-right: 5px;
}

.suggestion-item:hover {
    background: #70C745;
    color: white;
    border-color: #70C745;
}

.chatbot-input {
    padding: 15px;
    background: white;
    border-top: 1px solid #e9ecef;
}

.input-group {
    display: flex;
    align-items: center;
}

#chatbot-message {
    flex: 1;
    border: 1px solid #e9ecef;
    border-radius: 25px;
    padding: 10px 15px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s ease;
}

#chatbot-message:focus {
    border-color: #70C745;
}

#chatbot-send {
    background: #70C745;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-left: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}

#chatbot-send:hover {
    background: #5BA832;
}

#chatbot-send i {
    color: white;
    font-size: 16px;
}

.chatbot-typing {
    display: flex;
    align-items: center;
    padding: 10px 0;
    margin-top: 10px;
}

.chatbot-typing span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #70C745;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.chatbot-typing span:nth-child(2) {
    animation-delay: 0.2s;
}

.chatbot-typing span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

@media (max-width: 768px) {
    .chatbot-container {
        width: 300px;
        height: 450px;
        bottom: 70px;
        right: -10px;
    }
    
    .chatbot-widget {
        bottom: 15px;
        right: 15px;
    }
}
</style>

<!-- Chatbot JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotMessage = document.getElementById('chatbot-message');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotSuggestions = document.getElementById('chatbot-suggestions');
    const chatbotTyping = document.getElementById('chatbot-typing');
    const chatbotNotification = document.getElementById('chatbot-notification');

    let isOpen = false;

    // Toggle chatbot
    chatbotToggle.addEventListener('click', function() {
        if (isOpen) {
            chatbotContainer.style.display = 'none';
            isOpen = false;
        } else {
            chatbotContainer.style.display = 'flex';
            isOpen = true;
            chatbotNotification.style.display = 'none';
            chatbotMessage.focus();
        }
    });

    // Close chatbot
    chatbotClose.addEventListener('click', function() {
        chatbotContainer.style.display = 'none';
        isOpen = false;
    });

    // Handle suggestion clicks
    chatbotSuggestions.addEventListener('click', function(e) {
        if (e.target.classList.contains('suggestion-item')) {
            const text = e.target.getAttribute('data-text');
            chatbotMessage.value = text;
            sendMessage(text);
        }
    });

    // Handle form submission
    chatbotForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatbotMessage.value.trim();
        if (message) {
            sendMessage(message);
        }
    });

    function sendMessage(message) {
        // Add user message to chat
        addMessage(message, 'user');
        
        // Clear input
        chatbotMessage.value = '';
        
        // Hide suggestions
        chatbotSuggestions.style.display = 'none';
        
        // Show typing indicator
        chatbotTyping.style.display = 'flex';
        
        // Scroll to bottom
        scrollToBottom();

        // Send to server
        fetch('{{ route("chatbot.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hide typing indicator
            chatbotTyping.style.display = 'none';
            
            if (data.success) {
                addMessage(data.message, 'bot');
            } else {
                addMessage('Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.', 'bot');
            }
            
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error:', error);
            chatbotTyping.style.display = 'none';
            addMessage('Đã xảy ra lỗi kết nối. Vui lòng thử lại sau.', 'bot');
            scrollToBottom();
        });
    }

    function addMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'message-avatar';
        
        if (sender === 'bot') {
            avatarDiv.innerHTML = '<img src="{{ asset("assets/img/core-img/leaf.png") }}" alt="Bot">';
        } else {
            avatarDiv.innerHTML = '<img src="{{ asset("assets/img/core-img/user-avatar.png") }}" alt="User" onerror="this.style.display=\'none\'">';
        }
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.innerHTML = `<p>${message}</p>`;
        
        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);
        
        chatbotMessages.appendChild(messageDiv);
    }

    function scrollToBottom() {
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
});
</script>
