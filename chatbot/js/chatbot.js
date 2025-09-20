/**
 * ไฟล์ JavaScript สำหรับแชทบอทโรงเรียนสาธิตมหาวิทยาลัยพะเยา
 * ใช้ Gemini API ในการตอบคำถาม
 */

// คลาสหลักสำหรับแชทบอท
class SatitChatbot {
    constructor(apiKey = null) {
        // ใช้ค่าจาก CHATBOT_CONFIG ถ้ามี
        const config = window.CHATBOT_CONFIG || {};
        
        this.apiKey = apiKey;
        this.model = config.model || 'meta/llama-4-maverick-17b-128e-instruct';
        this.useBackendApi = config.useBackendApi || false;
        this.apiEndpoint = config.apiEndpoint || 'chatbot/api/chat.php';
        this.apiUrl = `https://integrate.api.nvidia.com/v1/chat/completions`;
        this.chatHistory = [];
        this.isOpen = false;
        this.isWaiting = false;
        
        // ตั้งค่าอื่นๆ
        this.botName = config.botName || 'แชทบอทโรงเรียนสาธิต';
        this.welcomeMessage = config.welcomeMessage || 'สวัสดีครับ ผมเป็นแชทบอทของโรงเรียนสาธิตมหาวิทยาลัยพะเยา มีอะไรให้ช่วยไหมครับ?';
        this.placeholderText = config.placeholderText || 'พิมพ์ข้อความที่นี่...';
        this.themeColor = config.themeColor || '#7b3b95';
        this.autoOpen = config.autoOpen || false;
        
        // สร้าง DOM elements
        this.createElements();
        
        // เพิ่ม event listeners
        this.addEventListeners();
        
        // เพิ่มข้อความต้อนรับ
        this.addBotMessage(this.welcomeMessage);
        
        // เปิดแชทบอทอัตโนมัติถ้าตั้งค่าไว้
        if (this.autoOpen) {
            setTimeout(() => this.toggleChat(true), 1000);
        }
    }
    
    // สร้าง DOM elements สำหรับแชทบอท
    createElements() {
        // ปรับสีหลักของแชทบอทตามที่ตั้งค่าไว้
        if (this.themeColor) {
            const style = document.createElement('style');
            style.textContent = `
                .chatbot-toggle, .chatbot-header {
                    background: linear-gradient(135deg, ${this.themeColor} 0%, ${this.getLighterColor(this.themeColor)} 100%);
                }
                .chatbot-input button {
                    background-color: ${this.themeColor};
                }
                .chatbot-input button:hover {
                    background-color: ${this.getLighterColor(this.themeColor)};
                }
                .typing-indicator span {
                    background-color: ${this.themeColor};
                }
                .user-message {
                    background-color: ${this.themeColor};
                }
            `;
            document.head.appendChild(style);
        }
        
        // สร้างปุ่มเปิดแชทบอท
        this.toggleButton = document.createElement('div');
        this.toggleButton.className = 'chatbot-toggle';
        this.toggleButton.innerHTML = '<i class="fas fa-comments"></i>';
        document.body.appendChild(this.toggleButton);
        
        // สร้างคอนเทนเนอร์แชทบอท
        this.container = document.createElement('div');
        this.container.className = 'chatbot-container';
        
        // สร้างส่วนหัวของแชทบอท
        const header = document.createElement('div');
        header.className = 'chatbot-header';
        header.innerHTML = `
            <h3><i class="fas fa-robot"></i> ${this.botName}</h3>
            <div class="chatbot-controls">
                <button class="minimize-btn" title="ย่อ"><i class="fas fa-minus"></i></button>
                <button class="close-btn" title="ปิด"><i class="fas fa-times"></i></button>
            </div>
        `;
        
        // สร้างส่วนแสดงข้อความแชท
        this.messagesContainer = document.createElement('div');
        this.messagesContainer.className = 'chatbot-messages';
        
        // สร้างส่วนพิมพ์ข้อความ
        const inputArea = document.createElement('div');
        inputArea.className = 'chatbot-input';
        
        this.inputField = document.createElement('input');
        this.inputField.type = 'text';
        this.inputField.placeholder = this.placeholderText;
        
        this.sendButton = document.createElement('button');
        this.sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        this.sendButton.disabled = true;
        
        inputArea.appendChild(this.inputField);
        inputArea.appendChild(this.sendButton);
        
        // ประกอบส่วนต่างๆ เข้าด้วยกัน
        this.container.appendChild(header);
        this.container.appendChild(this.messagesContainer);
        this.container.appendChild(inputArea);
        
        document.body.appendChild(this.container);
    }
    
    // เพิ่ม event listeners
    addEventListeners() {
        // เปิด/ปิดแชทบอท
        this.toggleButton.addEventListener('click', () => this.toggleChat());
        
        // ปุ่มปิดและย่อ
        this.container.querySelector('.close-btn').addEventListener('click', () => this.toggleChat(false));
        this.container.querySelector('.minimize-btn').addEventListener('click', () => this.toggleChat(false));
        
        // ส่งข้อความ
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
        
        // ตรวจสอบการพิมพ์ข้อความ
        this.inputField.addEventListener('input', () => {
            this.sendButton.disabled = this.inputField.value.trim() === '';
        });
    }
    
    // เปิด/ปิดแชทบอท
    toggleChat(open = !this.isOpen) {
        this.isOpen = open;
        
        if (this.isOpen) {
            this.container.classList.add('active');
            this.inputField.focus();
        } else {
            this.container.classList.remove('active');
        }
    }
    
    // เพิ่มข้อความจากผู้ใช้
    addUserMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'chat-message user-message';
        messageElement.textContent = message;
        
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
        
        // เก็บประวัติการสนทนา
        this.chatHistory.push({
            role: 'user',
            parts: [{ text: message }]
        });
    }
    
    // เพิ่มข้อความจากบอท
    addBotMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'chat-message bot-message';
        messageElement.textContent = message;
        
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
        
        // เก็บประวัติการสนทนา
        this.chatHistory.push({
            role: 'model',
            parts: [{ text: message }]
        });
    }
    
    // แสดงตัวบ่งชี้การพิมพ์
    showTypingIndicator() {
        this.typingIndicator = document.createElement('div');
        this.typingIndicator.className = 'typing-indicator';
        this.typingIndicator.innerHTML = 'กำลังพิมพ์ <span></span><span></span><span></span>';
        
        this.messagesContainer.appendChild(this.typingIndicator);
        this.scrollToBottom();
    }
    
    // ซ่อนตัวบ่งชี้การพิมพ์
    hideTypingIndicator() {
        if (this.typingIndicator && this.typingIndicator.parentNode) {
            this.typingIndicator.parentNode.removeChild(this.typingIndicator);
        }
    }
    
    // เลื่อนลงไปที่ข้อความล่าสุด
    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
    
    // ส่งข้อความไปยัง AI API (Nvidia NIM)
    async sendToGemini(message) {
        try {
            // ถ้าใช้ Backend API
            if (this.useBackendApi) {
                return await this.sendToBackendApi(message);
            }
            
            // ถ้าไม่ใช้ Backend API (ไม่แนะนำสำหรับการใช้งานจริง)
            // ตรวจสอบว่ามี API key หรือไม่
            if (!this.apiKey) {
                throw new Error('API key is not set');
            }
            
            // กำหนดให้ใช้ streaming หรือไม่
            const stream = false;
            
            // สร้าง headers ตามตัวอย่าง Python
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiKey}`,
                'Accept': stream ? 'text/event-stream' : 'application/json'
            };
            
            // สร้าง payload ตามตัวอย่าง Python
            const payload = {
                model: "meta/llama-4-maverick-17b-128e-instruct",
                messages: this.prepareConversationForNvidia(message),
                max_tokens: 512,
                temperature: 1.00,
                top_p: 1.00,
                frequency_penalty: 0.00,
                presence_penalty: 0.00,
                stream: stream
            };
            
            // ส่งคำขอไปยัง API
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload)
            });
            
            if (!response.ok) {
                throw new Error('API request failed');
            }
            
            // ถ้าไม่ใช้ streaming
            if (!stream) {
                const data = await response.json();
                return data.choices[0].message.content;
            } else {
                // ถ้าใช้ streaming (ไม่ใช้ในส่วนนี้ แต่เตรียมไว้สำหรับการขยายในอนาคต)
                // จะถูกจัดการโดย sendToBackendApi แทน
                return 'Streaming mode is not supported in direct API calls';
            }
        } catch (error) {
            console.error('Error calling Nvidia NIM API:', error);
            return 'ขออภัย เกิดข้อผิดพลาดในการเชื่อมต่อกับ AI ลองอีกครั้งในภายหลังนะครับ';
        }
    }
    
    // ส่งข้อความไปยัง Backend API
    async sendToBackendApi(message) {
        try {
            // สร้างตัวแปรสำหรับเก็บข้อความทั้งหมด
            let fullResponse = '';
            
            // สร้าง placeholder สำหรับข้อความที่กำลังได้รับ
            const placeholderId = `response-${Date.now()}`;
            const placeholderElement = document.createElement('div');
            placeholderElement.className = 'chat-message bot-message streaming';
            placeholderElement.id = placeholderId;
            placeholderElement.textContent = '';
            this.messagesContainer.appendChild(placeholderElement);
            this.scrollToBottom();
            
            // ซ่อนตัวบ่งชี้การพิมพ์
            this.hideTypingIndicator();
            
            // ส่งคำขอไปยัง API แบบ streaming
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: this.chatHistory
                })
            });
            
            if (!response.ok) {
                throw new Error('Backend API request failed');
            }
            
            // ตรวจสอบว่าเป็น streaming response หรือไม่
            const contentType = response.headers.get('Content-Type');
            
            if (contentType && contentType.includes('text/event-stream')) {
                // ใช้ ReadableStream API สำหรับอ่าน streaming response
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                
                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    
                    // แปลง binary data เป็น text
                    const chunk = decoder.decode(value, { stream: true });
                    
                    // แยกข้อมูลตามบรรทัด
                    const lines = chunk.split('\n');
                    
                    for (const line of lines) {
                        // ข้าม comment หรือบรรทัดว่าง
                        if (!line || line.startsWith(':')) continue;
                        
                        // ตรวจสอบว่าเป็น data line หรือไม่
                        if (line.startsWith('data: ')) {
                            const jsonData = line.substring(6);
                            
                            // ตรวจสอบว่าเป็น [DONE] หรือไม่
                            if (jsonData.trim() === '[DONE]') continue;
                            
                            try {
                                const data = JSON.parse(jsonData);
                                
                                // สำหรับ Nvidia NIM API - ตรวจสอบ finish_reason
                                if (data.choices && data.choices[0]) {
                                    const choice = data.choices[0];
                                    
                                    // ถ้ามี content ใน delta
                                    if (choice.delta && choice.delta.content) {
                                        const textChunk = choice.delta.content;
                                        
                                        // เพิ่มข้อความในตัวแปรเก็บข้อความทั้งหมด
                                        fullResponse += textChunk;
                                        
                                        // อัปเดต placeholder ด้วยข้อความใหม่
                                        placeholderElement.textContent = fullResponse;
                                        this.scrollToBottom();
                                    }
                                    
                                    // ตรวจสอบว่าจบแล้วหรือยัง
                                    if (choice.finish_reason === 'stop' || choice.finish_reason) {
                                        console.log('Streaming finished:', choice.finish_reason);
                                        // จบการ streaming แล้ว
                                        if (fullResponse.trim() === '') {
                                            fullResponse = 'ขออภัย ไม่ได้รับการตอบกลับจาก AI';
                                            placeholderElement.textContent = fullResponse;
                                        }
                                    }
                                }
                                // สำหรับ Gemini API (เก็บไว้เผื่อต้องการใช้ในอนาคต)
                                else if (data.candidates && data.candidates[0]?.content?.parts?.length > 0) {
                                    const textChunk = data.candidates[0].content.parts[0].text || '';
                                    
                                    // เพิ่มข้อความในตัวแปรเก็บข้อความทั้งหมด
                                    fullResponse += textChunk;
                                    
                                    // อัปเดต placeholder ด้วยข้อความใหม่
                                    placeholderElement.textContent = fullResponse;
                                    this.scrollToBottom();
                                }
                            } catch (e) {
                                console.error('Error parsing SSE data:', e, 'Raw data:', jsonData);
                            }
                        }
                    }
                }
                
                // ลบ streaming class เมื่อได้รับข้อความทั้งหมดแล้ว
                placeholderElement.classList.remove('streaming');
                
                // เก็บประวัติการสนทนา
                this.chatHistory.push({
                    role: 'model',
                    parts: [{ text: fullResponse }]
                });
                
                return null; // ไม่ต้องส่งคืนข้อความเพราะได้แสดงไปแล้ว
            } else {
                // ถ้าไม่ใช่ streaming response ให้ใช้วิธีเดิม
                const data = await response.json();
                
                // ลบ placeholder
                placeholderElement.remove();
                
                // สำหรับ Nvidia NIM API (รูปแบบตามตัวอย่าง Python)
                if (data.choices && data.choices[0]?.message?.content) {
                    return data.choices[0].message.content;
                }
                // สำหรับ Nvidia NIM API อีกรูปแบบหนึ่ง
                else if (data.choices && data.choices[0]?.message?.content !== undefined) {
                    return data.choices[0].message.content;
                }
                // สำหรับ Gemini API (เก็บไว้เผื่อต้องการใช้ในอนาคต)
                else if (data.candidates && data.candidates[0]?.content?.parts?.length > 0) {
                    return data.candidates[0].content.parts[0].text;
                }
                
                return 'ขออภัย ไม่สามารถประมวลผลคำตอบได้';
            }
        } catch (error) {
            console.error('Error calling Backend API:', error);
            return 'ขออภัย เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์ ลองอีกครั้งในภายหลังนะครับ';
        }
    }
    
    // เตรียมข้อมูลการสนทนาสำหรับส่งไปยัง Gemini API (เก็บไว้เผื่อต้องการใช้ในอนาคต)
    prepareConversation(message) {
        // สร้างบริบทเกี่ยวกับโรงเรียนสาธิตมหาวิทยาลัยพะเยา
        const systemPrompt = {
            role: 'system',
            parts: [{ text: 'คุณเป็นแชทบอทของโรงเรียนสาธิตมหาวิทยาลัยพะเยา ให้ข้อมูลเกี่ยวกับโรงเรียน หลักสูตร การรับสมัคร และกิจกรรมต่างๆ ตอบคำถามสั้นๆ ด้วยภาษาที่เป็นมิตร' }]
        };
        
        // ใช้ประวัติการสนทนาล่าสุด (จำกัดจำนวนเพื่อไม่ให้เกินขีดจำกัดของ API)
        const recentHistory = this.chatHistory.slice(-10);
        
        // เพิ่มข้อความปัจจุบัน
        const currentMessage = {
            role: 'user',
            parts: [{ text: message }]
        };
        
        return [systemPrompt, ...recentHistory, currentMessage];
    }
    
    // เตรียมข้อมูลการสนทนาสำหรับส่งไปยัง Nvidia NIM API
    prepareConversationForNvidia(message) {
        // สร้างบริบทเกี่ยวกับโรงเรียนสาธิตมหาวิทยาลัยพะเยา
        const messages = [
            {
                role: 'system',
                content: 'คุณเป็นแชทบอทของโรงเรียนสาธิตมหาวิทยาลัยพะเยา ให้ข้อมูลเกี่ยวกับโรงเรียน หลักสูตร การรับสมัคร และกิจกรรมต่างๆ ตอบคำถามสั้นๆ ด้วยภาษาที่เป็นมิตร'
            }
        ];
        
        // ใช้ประวัติการสนทนาล่าสุด (จำกัดจำนวนเพื่อไม่ให้เกินขีดจำกัดของ API)
        const recentHistory = this.chatHistory.slice(-10);
        
        // แปลงรูปแบบประวัติการสนทนาให้เข้ากับ Nvidia NIM API
        recentHistory.forEach(entry => {
            if (entry.role && entry.parts && entry.parts[0] && entry.parts[0].text) {
                // แปลง role 'model' เป็น 'assistant' สำหรับ Nvidia API
                const role = entry.role === 'model' ? 'assistant' : entry.role;
                messages.push({
                    role: role,
                    content: entry.parts[0].text
                });
            }
        });
        
        // เพิ่มข้อความปัจจุบัน
        messages.push({
            role: 'user',
            content: message
        });
        
        return messages;
    }
    
    // ฟังก์ชันสำหรับปรับสีให้อ่อนลง
    getLighterColor(hex) {
        // แปลง hex เป็น RGB
        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);
        
        // ปรับให้อ่อนลง
        r = Math.min(255, r + 40);
        g = Math.min(255, g + 40);
        b = Math.min(255, b + 40);
        
        // แปลงกลับเป็น hex
        return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
    }
    
    // ส่งข้อความ
    async sendMessage() {
        const message = this.inputField.value.trim();
        if (message === '' || this.isWaiting) return;
        
        // เคลียร์ช่องข้อความ
        this.inputField.value = '';
        this.sendButton.disabled = true;
        
        // แสดงข้อความผู้ใช้
        this.addUserMessage(message);
        
        // แสดงตัวบ่งชี้การพิมพ์
        this.showTypingIndicator();
        this.isWaiting = true;
        
        try {
            // ส่งข้อความไปยัง Gemini API
            const response = await this.sendToGemini(message);
            
            // ซ่อนตัวบ่งชี้การพิมพ์
            this.hideTypingIndicator();
            
            // แสดงข้อความตอบกลับ (ถ้ามี - กรณี streaming อาจได้รับ null)
            if (response !== null) {
                this.addBotMessage(response);
            }
        } catch (error) {
            console.error('Error in sendMessage:', error);
            this.hideTypingIndicator();
            this.addBotMessage('ขออภัย เกิดข้อผิดพลาดในการประมวลผล ลองอีกครั้งในภายหลังนะครับ');
        } finally {
            this.isWaiting = false;
        }
    }
}

// ฟังก์ชันสำหรับเริ่มต้นแชทบอทเมื่อหน้าเว็บโหลดเสร็จ
function initChatbot() {
    // รับการตั้งค่าจาก config
    const config = window.CHATBOT_CONFIG || {};
    let apiKey = '';
    
    // ถ้าไม่ใช้ backend API ให้รับ API key จาก hidden input
    if (!config.useBackendApi) {
        apiKey = document.getElementById('nvidia-api-key')?.value || '';
        if (!apiKey) {
            console.error('API key not found. Chatbot may not work properly.');
        }
    } else {
        console.log('Using backend API - no need for frontend API key');
    }
    
    // สร้างอินสแตนซ์ของแชทบอท
    window.satitChatbot = new SatitChatbot(apiKey);
    
    // ถ้าตั้งค่าให้เปิดอัตโนมัติ
    if (config.autoOpen) {
        setTimeout(() => {
            if (window.satitChatbot) {
                window.satitChatbot.toggleChat();
            }
        }, 1000); // รอ 1 วินาทีก่อนเปิด
    }
    
    // โหลด Font Awesome ถ้ายังไม่มี
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(fontAwesome);
    }
}

// เริ่มต้นแชทบอทเมื่อหน้าเว็บโหลดเสร็จ
document.addEventListener('DOMContentLoaded', initChatbot);
