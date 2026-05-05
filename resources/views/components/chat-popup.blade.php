<div x-data="chatPopup()" 
     x-init="initChat()" 
     class="fixed bottom-6 right-6 z-[90] font-sans">
    
    <!-- Chat Button -->
    <button @click="toggleChat()" 
            class="w-14 h-14 bg-sepia-600 rounded-full text-white shadow-xl flex items-center justify-center hover:bg-sepia-700 hover:scale-105 transition-all duration-300 relative">
        <svg x-show="!isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        <svg x-show="isOpen" style="display: none;" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <!-- Unread Badge (Optional if we want to show badge for unread admin messages) -->
        <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white"></span>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-beige-200 flex flex-col overflow-hidden"
         style="display: none; height: 500px; max-height: calc(100vh - 120px);">
        
        <!-- Header -->
        <div class="bg-sepia-600 text-white p-4 flex justify-between items-center shadow-md z-10">
            <div>
                <h3 class="font-bold tracking-wide">Hỗ trợ khách hàng</h3>
                <p class="text-[10px] text-sepia-200 uppercase tracking-widest mt-0.5">Rynna Stationery</p>
            </div>
            <button @click="isOpen = false" class="text-sepia-200 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages-container" class="flex-1 p-4 overflow-y-auto bg-beige-50 space-y-4">
            <!-- Welcome message -->
            <div class="flex items-start mb-4">
                <div class="w-8 h-8 rounded-full bg-sepia-100 flex items-center justify-center text-sepia-600 flex-shrink-0 mr-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <div class="bg-white border border-beige-200 text-earth-800 text-sm rounded-2xl rounded-tl-none p-3 shadow-sm">
                    Xin chào! Rynna có thể giúp gì cho bạn?
                </div>
            </div>

            <!-- Dynamic Messages -->
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.sender_type === 'customer' ? 'flex items-end justify-end' : 'flex items-start'">
                    
                    <!-- Admin Avatar -->
                    <template x-if="msg.sender_type === 'admin'">
                        <div class="w-8 h-8 rounded-full bg-sepia-100 flex items-center justify-center text-sepia-600 flex-shrink-0 mr-3">
                            <span class="text-xs font-bold">AD</span>
                        </div>
                    </template>

                    <!-- Message Bubble -->
                    <div :class="msg.sender_type === 'customer' ? 'bg-earth-800 text-white rounded-2xl rounded-tr-none' : 'bg-white border border-beige-200 text-earth-800 rounded-2xl rounded-tl-none'" 
                         class="max-w-[80%] text-sm p-3 shadow-sm break-words whitespace-pre-wrap" x-text="msg.message">
                    </div>
                </div>
            </template>
            
            <div x-show="isLoading" class="text-center text-xs text-earth-400 py-2">Đang gửi...</div>
        </div>

        <!-- Input Area -->
        <div class="p-3 border-t border-beige-200 bg-white">
            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                <input x-model="newMessage" 
                       type="text" 
                       placeholder="Nhập tin nhắn..." 
                       class="flex-1 bg-beige-50 border-none rounded-full py-2 px-4 text-sm focus:ring-1 focus:ring-sepia-500 placeholder-earth-300"
                       :disabled="isLoading">
                <button type="submit" 
                        class="w-10 h-10 rounded-full bg-sepia-600 text-white flex items-center justify-center hover:bg-sepia-700 transition disabled:opacity-50 flex-shrink-0"
                        :disabled="!newMessage.trim() || isLoading">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatPopup', () => ({
        isOpen: false,
        messages: [],
        newMessage: '',
        isLoading: false,
        pollInterval: null,
        unreadCount: 0,
        lastMessageId: 0,

        initChat() {
            // Load messages initially if open, or wait until opened
            this.$watch('isOpen', value => {
                if (value) {
                    this.fetchMessages();
                    this.unreadCount = 0; // reset badge
                    this.startPolling();
                } else {
                    this.stopPolling();
                }
            });
            // Also fetch silently once to check for unread messages if needed
            this.fetchMessagesSilently();
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
        },

        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('chat-messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 50);
        },

        fetchMessagesSilently() {
            fetch('/chat/messages', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.messages = data;
                if (data.length > 0) {
                    const newLastId = data[data.length - 1].id;
                    if (newLastId > this.lastMessageId) {
                        this.lastMessageId = newLastId;
                        if (!this.isOpen) {
                            // Count unread if closed
                            this.unreadCount = data.filter(m => m.sender_type === 'admin' && !m.is_read).length;
                        } else {
                            this.scrollToBottom();
                        }
                    }
                }
            })
            .catch(err => console.error('Error fetching chat:', err));
        },

        fetchMessages() {
            this.fetchMessagesSilently();
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;
            
            const msgText = this.newMessage;
            this.newMessage = '';
            this.isLoading = true;

            // Optimistic UI update
            const tempMsg = {
                id: 'temp_' + Date.now(),
                sender_type: 'customer',
                message: msgText
            };
            this.messages.push(tempMsg);
            this.scrollToBottom();

            fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: msgText })
            })
            .then(res => res.json())
            .then(data => {
                this.isLoading = false;
                if (data.success) {
                    // Replace temp msg with real one, or just fetch all
                    this.fetchMessages();
                }
            })
            .catch(err => {
                console.error('Error sending message:', err);
                this.isLoading = false;
                // remove temp message on failure
                this.messages = this.messages.filter(m => m.id !== tempMsg.id);
            });
        },

        startPolling() {
            if (!this.pollInterval) {
                this.pollInterval = setInterval(() => {
                    this.fetchMessagesSilently();
                }, 3000); // Poll every 3 seconds
            }
        },

        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        }
    }));
});
</script>
