import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['messages', 'input', 'status']
    static values = {
        config: Object
    }

    connect() {
        this.initializeWebSocket();
    }

    initializeWebSocket() {
        if (!this.configValue.websocket_enabled) {
            return;
        }

        this.socket = new WebSocket(this.configValue.websocket_url);
        
        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === 'message') {
                this.addMessage(data.response, 'bot');
            }
        };
    }

    async sendMessage(event) {
        event.preventDefault();
        
        const message = this.inputTarget.value;
        if (!message.trim()) return;

        this.addMessage(message, 'user');
        this.inputTarget.value = '';
        this.showTypingIndicator();

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            if (data.success) {
                this.addMessage(data.response, 'bot');
            } else {
                this.addMessage('Une erreur est survenue', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.addMessage('Une erreur est survenue', 'error');
        } finally {
            this.hideTypingIndicator();
        }
    }

    addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', `message-${type}`);
        messageDiv.textContent = content;
        this.messagesTarget.appendChild(messageDiv);
        this.scrollToBottom();
    }

    scrollToBottom() {
        this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight;
    }

    showTypingIndicator() {
        this.statusTarget.querySelector('.typing-indicator').classList.remove('d-none');
    }

    hideTypingIndicator() {
        this.statusTarget.querySelector('.typing-indicator').classList.add('d-none');
    }

    disconnect() {
        if (this.socket) {
            this.socket.close();
        }
    }
} 