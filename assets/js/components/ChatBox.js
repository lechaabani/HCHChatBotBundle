import { Controller } from '@hotwired/stimulus';
import { ChatApi } from '../services/api';
import { WebSocketService } from '../services/websocket';

export default class extends Controller {
    static targets = ['messages', 'input']
    static values = {
        websocketUrl: String
    }

    connect() {
        this.api = new ChatApi();
        this.websocket = new WebSocketService(this.websocketUrlValue);
        this.websocket.onMessage(this.handleWebSocketMessage.bind(this));
    }

    async sendMessage(event) {
        event.preventDefault();
        const message = this.inputTarget.value;
        
        try {
            const response = await this.api.sendMessage(message);
            this.addMessage(message, 'user');
            this.addMessage(response.message, 'bot');
        } catch (error) {
            console.error('Error sending message:', error);
        }
        
        this.inputTarget.value = '';
    }

    handleWebSocketMessage(event) {
        const data = JSON.parse(event.data);
        switch (data.type) {
            case 'message':
                this.addMessage(data.content, 'bot');
                break;
            case 'typing':
                this.showTypingIndicator();
                break;
            case 'stop_typing':
                this.hideTypingIndicator();
                break;
            case 'error':
                this.showError(data.message);
                break;
        }
    }

    addMessage(content, type) {
        const messageEl = document.createElement('div');
        messageEl.classList.add('message', `message-${type}`);
        
        const timestamp = document.createElement('span');
        timestamp.classList.add('message-time');
        timestamp.textContent = new Date().toLocaleTimeString();
        
        const contentEl = document.createElement('div');
        contentEl.classList.add('message-content');
        contentEl.textContent = content;
        
        messageEl.appendChild(timestamp);
        messageEl.appendChild(contentEl);
        
        this.messagesTarget.appendChild(messageEl);
        this.scrollToBottom();
    }

    showTypingIndicator() {
        const indicator = this.element.querySelector('.typing-indicator');
        if (indicator) {
            indicator.classList.remove('d-none');
        }
    }

    hideTypingIndicator() {
        const indicator = this.element.querySelector('.typing-indicator');
        if (indicator) {
            indicator.classList.add('d-none');
        }
    }

    showError(message) {
        const errorEl = document.createElement('div');
        errorEl.classList.add('alert', 'alert-danger', 'mt-2');
        errorEl.textContent = message;
        
        this.messagesTarget.appendChild(errorEl);
        setTimeout(() => errorEl.remove(), 5000);
    }

    scrollToBottom() {
        this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight;
    }

    disconnect() {
        if (this.websocket) {
            this.websocket.close();
        }
    }

    // ... autres méthodes
} 