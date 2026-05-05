<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-earth-900 tracking-tight flex items-center">
            <span class="w-2 h-8 bg-sepia-500 rounded-full mr-4"></span>
            {{ __('Quản lý Liên lạc') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-beige-50" x-data="adminChat()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-beige-200 flex h-[700px]">
                
                <!-- Sidebar: List of Sessions -->
                <div class="w-1/3 border-r border-beige-200 bg-white flex flex-col">
                    <div class="p-6 border-b border-beige-100 bg-beige-50/50">
                        <h3 class="font-bold text-earth-900 uppercase tracking-widest text-sm">Danh sách khách hàng</h3>
                        <p class="text-[10px] text-earth-400 mt-1">Các phiên nhắn tin gần đây</p>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        @forelse($sessions as $session)
                            <div @click="selectSession('{{ $session->session_id }}')" 
                                 :class="activeSession === '{{ $session->session_id }}' ? 'bg-sepia-50 border-l-4 border-sepia-500' : 'hover:bg-beige-50 border-l-4 border-transparent'"
                                 class="p-4 cursor-pointer transition-colors duration-200 border-b border-beige-100 relative">
                                
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-earth-100 flex items-center justify-center text-earth-600 font-bold">
                                            @if($session->user)
                                                {{ substr($session->user->name, 0, 1) }}
                                            @else
                                                G
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-earth-900">
                                                @if($session->user)
                                                    {{ $session->user->name }}
                                                @else
                                                    Khách ẩn danh
                                                @endif
                                            </h4>
                                            <p class="text-[10px] text-earth-400 truncate w-32">Mã: {{ substr($session->session_id, 0, 8) }}...</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-earth-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($session->last_activity)->diffForHumans() }}</span>
                                </div>

                                @if($session->unread_count > 0)
                                    <span class="absolute top-4 right-4 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full" 
                                          x-show="activeSession !== '{{ $session->session_id }}'">
                                        {{ $session->unread_count }}
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-earth-400 text-sm">
                                Chưa có tin nhắn nào.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="w-2/3 bg-beige-50 flex flex-col relative">
                    <!-- Placeholder when no session selected -->
                    <div x-show="!activeSession" class="absolute inset-0 flex flex-col items-center justify-center text-earth-400 bg-beige-50 z-10">
                        <svg class="w-16 h-16 mb-4 text-earth-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <p class="font-medium text-sm">Chọn một khách hàng để bắt đầu chat</p>
                    </div>

                    <!-- Chat Header -->
                    <div class="bg-white border-b border-beige-200 p-6 flex justify-between items-center z-0">
                        <div class="flex items-center gap-4">
                            <h3 class="font-bold text-earth-900" x-text="'Phiên chat: ' + activeSession.substring(0, 8) + '...'"></h3>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div id="admin-chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4">
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.sender_type === 'admin' ? 'flex items-end justify-end' : 'flex items-start'">
                                <!-- Customer Avatar -->
                                <template x-if="msg.sender_type === 'customer'">
                                    <div class="w-8 h-8 rounded-full bg-earth-200 flex items-center justify-center text-earth-700 flex-shrink-0 mr-3">
                                        <span class="text-xs font-bold">C</span>
                                    </div>
                                </template>

                                <!-- Message Bubble -->
                                <div :class="msg.sender_type === 'admin' ? 'bg-sepia-600 text-white rounded-2xl rounded-tr-none' : 'bg-white border border-beige-200 text-earth-800 rounded-2xl rounded-tl-none'" 
                                     class="max-w-[70%] text-sm p-4 shadow-sm break-words whitespace-pre-wrap" x-text="msg.message">
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Area -->
                    <div class="bg-white border-t border-beige-200 p-4">
                        <form @submit.prevent="sendMessage" class="flex items-center gap-3">
                            <input x-model="newMessage" 
                                   type="text" 
                                   placeholder="Nhập tin nhắn trả lời..." 
                                   class="flex-1 bg-beige-50 border-none rounded-xl py-3 px-5 text-sm focus:ring-2 focus:ring-sepia-500 transition shadow-inner"
                                   :disabled="isLoading || !activeSession">
                            <button type="submit" 
                                    class="bg-sepia-600 text-white px-6 py-3 rounded-xl font-bold uppercase tracking-wider text-xs hover:bg-sepia-700 transition disabled:opacity-50"
                                    :disabled="!newMessage.trim() || isLoading || !activeSession">
                                Gửi
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminChat', () => ({
                activeSession: '',
                messages: [],
                newMessage: '',
                isLoading: false,
                pollInterval: null,

                init() {
                    // Start polling active session messages
                    this.pollInterval = setInterval(() => {
                        if (this.activeSession) {
                            this.fetchMessagesSilently();
                        }
                    }, 3000);
                },

                selectSession(sessionId) {
                    this.activeSession = sessionId;
                    this.messages = [];
                    this.fetchMessages();
                },

                fetchMessagesSilently() {
                    fetch(`/admin/contacts/${this.activeSession}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        const oldLength = this.messages.length;
                        this.messages = data;
                        if (data.length > oldLength) {
                            this.scrollToBottom();
                        }
                    })
                    .catch(err => console.error('Error fetching admin chat:', err));
                },

                fetchMessages() {
                    this.fetchMessagesSilently();
                    setTimeout(() => this.scrollToBottom(), 100);
                },

                scrollToBottom() {
                    const container = document.getElementById('admin-chat-messages');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                sendMessage() {
                    if (!this.newMessage.trim() || !this.activeSession) return;
                    
                    const msgText = this.newMessage;
                    this.newMessage = '';
                    this.isLoading = true;

                    // Optimistic update
                    this.messages.push({
                        id: 'temp_' + Date.now(),
                        sender_type: 'admin',
                        message: msgText
                    });
                    this.scrollToBottom();

                    fetch(`/admin/contacts/${this.activeSession}/reply`, {
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
                            this.fetchMessages();
                        }
                    })
                    .catch(err => {
                        console.error('Error sending reply:', err);
                        this.isLoading = false;
                    });
                }
            }));
        });
    </script>
</x-app-layout>
