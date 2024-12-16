export class ChatApi {
    constructor(baseUrl = '/chat') {
        this.baseUrl = baseUrl;
    }

    async sendMessage(message, context = {}) {
        const response = await fetch(`${this.baseUrl}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message, context })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return response.json();
    }

    async getHistory() {
        const response = await fetch(`${this.baseUrl}/history`);
        if (!response.ok) {
            throw new Error('Failed to fetch history');
        }
        return response.json();
    }

    async streamResponse(message, context = {}) {
        const response = await fetch(`${this.baseUrl}/stream`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message, context })
        });

        if (!response.ok) {
            throw new Error('Stream request failed');
        }

        return new ReadableStream({
            start(controller) {
                const reader = response.body.getReader();
                return pump();

                function pump() {
                    return reader.read().then(({done, value}) => {
                        if (done) {
                            controller.close();
                            return;
                        }
                        controller.enqueue(value);
                        return pump();
                    });
                }
            }
        });
    }

    async validateMessage(message) {
        const response = await fetch(`${this.baseUrl}/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message })
        });
        return response.json();
    }

    async getSettings() {
        const response = await fetch(`${this.baseUrl}/settings`);
        if (!response.ok) {
            throw new Error('Failed to fetch settings');
        }
        return response.json();
    }

    async updateSettings(settings) {
        const response = await fetch(`${this.baseUrl}/settings`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(settings)
        });
        if (!response.ok) {
            throw new Error('Failed to update settings');
        }
        return response.json();
    }

    // ... autres méthodes API
} 