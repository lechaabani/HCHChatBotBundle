const HCHChatBot = {
    config: {
        theme: 'light',
        position: 'bottom-right',
        headerText: 'Assistant',
        placeholder: 'Votre message...',
        hooks: {}
    },

    init(options) {
        this.config = { ...this.config, ...options };
        this.container = document.querySelector(options.container || '#hch-chatbot-widget');
        this.initializeWidget();
        this.bindEvents();
        this.executeHook('onInit');
    },

    initializeWidget() {
        this.widget = document.createElement('div');
        this.widget.className = `hch-chatbot-widget ${this.config.theme}`;
        this.widget.innerHTML = this.getWidgetTemplate();
        this.container.appendChild(this.widget);
    },

    bindEvents() {
        const input = this.widget.querySelector('textarea');
        const sendBtn = this.widget.querySelector('button[type="submit"]');
        const closeBtn = this.widget.querySelector('.close-btn');

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage(input.value);
                input.value = '';
            }
        });

        sendBtn.addEventListener('click', () => {
            this.sendMessage(input.value);
            input.value = '';
        });

        closeBtn.addEventListener('click', () => {
            this.executeHook('onClose');
            this.widget.classList.add('hidden');
        });
    },

    async sendMessage(message) {
        if (!message.trim()) return;

        const processedMessage = this.executeHook('beforeSend', message);
        this.addMessage('user', processedMessage);

        try {
            const response = await this.sendToServer(processedMessage);
            const processedResponse = this.executeHook('afterResponse', response);
            this.addMessage('bot', processedResponse);
        } catch (error) {
            this.executeHook('onError', error);
            this.addMessage('error', 'Une erreur est survenue');
        }
    },

    async sendToServer(message) {
        const response = await fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data.response;
    },

    addMessage(type, content) {
        const messagesContainer = this.widget.querySelector('.hch-chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.innerHTML = `
            <div class="${type}-avatar"></div>
            <div class="message-content">${content}</div>
            <span class="message-time">${new Date().toLocaleTimeString()}</span>
        `;
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    },

    executeHook(hookName, data) {
        if (this.config.hooks && typeof this.config.hooks[hookName] === 'function') {
            return this.config.hooks[hookName](data);
        }
        return data;
    },

    customize(config) {
        this.config = { ...this.config, ...config };
        this.applyCustomization();
    },

    applyCustomization() {
        if (this.config.headerText) {
            this.widget.querySelector('.hch-chatbot-header h3').textContent = this.config.headerText;
        }

        if (this.config.primaryColor) {
            this.widget.style.setProperty('--primary-color', this.config.primaryColor);
        }

        if (this.config.theme) {
            this.widget.className = `hch-chatbot-widget ${this.config.theme}`;
        }
    }
}; 