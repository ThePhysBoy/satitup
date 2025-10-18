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

        // สร้างและกำหนดเสียงแจ้งเตือน
        this.createNotificationSound();

        // เพิ่ม event listeners
        this.addEventListeners();

        // เพิ่มข้อความต้อนรับ
        this.addBotMessage(this.welcomeMessage);

        // เปิดแชทบอทอัตโนมัติถ้าตั้งค่าไว้
        if (this.autoOpen) {
            setTimeout(() => this.toggleChat(true), 1000);
        }
    }

    // สร้างและกำหนดเสียงแจ้งเตือน
    createNotificationSound() {
        // สร้าง function สำหรับเล่นเสียงด้วย Web Audio API
        this.playBeepSound = () => {
            try {
                // ตรวจสอบและสร้าง AudioContext ถ้ายังไม่มี
                if (!this.audioContext) {
                    this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }

                // ถ้า AudioContext ถูกระงับ (suspended) ให้ resume ก่อน
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }

                const oscillator = this.audioContext.createOscillator();
                const gainNode = this.audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(this.audioContext.destination);

                // สร้างเสียงบี๊บสองตัว (สูงแล้วต่ำ)
                oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, this.audioContext.currentTime + 0.1);

                gainNode.gain.setValueAtTime(0.1, this.audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.2);

                oscillator.start(this.audioContext.currentTime);
                oscillator.stop(this.audioContext.currentTime + 0.2);
            } catch (e) {
                console.log('ไม่สามารถเล่นเสียงแจ้งเตือนได้:', e);
            }
        };

        // สร้าง audio element สำหรับ fallback
        this.notificationSound = new Audio();
        this.notificationSound.volume = 0.3;

        // ลองใช้ไฟล์เสียงจริงก่อน ถ้ามี
        try {
            // พยายามใช้ไฟล์เสียงจากเซิร์ฟเวอร์ก่อน
            this.notificationSound.src = 'chatbot/sounds/notification.mp3';
        } catch (e) {
            try {
                // ถ้าไม่มีไฟล์เสียงจริง ให้ใช้ data URI เป็น fallback
                this.notificationSound.src = 'data:audio/mpeg;base64,/+MYxAAAAANIAAAAAExBTUUzLjk4LjIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
            } catch (e2) {
                // ถ้า data URI ไม่ทำงาน ให้ใช้ Web Audio API เป็นหลัก
                console.log('ไฟล์เสียงไม่พบ ใช้ Web Audio API แทน');
            }
        }

        // ทดสอบการโหลดเสียง
        this.notificationSound.addEventListener('loadeddata', () => {
            console.log('ไฟล์เสียงโหลดสำเร็จ');
        });

        this.notificationSound.addEventListener('error', () => {
            console.log('ไฟล์เสียงโหลดไม่สำเร็จ ใช้ Web Audio API แทน');
            this.notificationSound = null;
        });
    }

    // เล่นเสียงแจ้งเตือน
    playNotificationSound() {
        try {
            // ลำดับความสำคัญ: Web Audio API -> Audio Element
            if (this.playBeepSound) {
                this.playBeepSound();
            } else if (this.notificationSound) {
                this.notificationSound.currentTime = 0;
                this.notificationSound.play().catch(e => {
                    console.log('ไม่สามารถเล่นเสียงได้ ลองใช้ Web Audio API');
                    if (this.playBeepSound) {
                        this.playBeepSound();
                    }
                });
            }
        } catch (e) {
            console.log('เกิดข้อผิดพลาดในการเล่นเสียง:', e);
        }
    }

    // ฟังก์ชันสำหรับเปิดใช้งานเสียงเมื่อมีการคลิกครั้งแรก
    enableAudioContext() {
        if (this.audioContext && this.audioContext.state === 'suspended') {
            this.audioContext.resume().then(() => {
                console.log('AudioContext เปิดใช้งานแล้ว');
            }).catch(e => {
                console.log('ไม่สามารถเปิดใช้งาน AudioContext ได้:', e);
            });
        }
    }

    // สร้าง DOM elements สำหรับแชทบอท
    createElements() {
        console.log('กำลังสร้าง elements ของแชทบอท...');

        // ปรับสีหลักของแชทบอทตามที่ตั้งค่าไว้
        if (this.themeColor) {
            const style = document.createElement('style');
            style.textContent = `
                .chatbot-toggle, .chatbot-header {
                    background: linear-gradient(135deg, ${this.themeColor} 0%, ${this.getLighterColor(this.themeColor)} 100%) !important;
                }
                .chatbot-input button {
                    background-color: ${this.themeColor} !important;
                }
                .chatbot-input button:hover {
                    background-color: ${this.getLighterColor(this.themeColor)} !important;
                }
                .typing-indicator span {
                    background-color: ${this.themeColor} !important;
                }
                .user-message {
                    background-color: ${this.themeColor} !important;
                }
            `;
            document.head.appendChild(style);
        }
        
        // สร้างปุ่มเปิดแชทบอท
        this.toggleButton = document.createElement('div');
        this.toggleButton.className = 'chatbot-toggle';
        this.toggleButton.innerHTML = '<i class="fas fa-comments"></i>';
        this.toggleButton.title = '🤖 DesUPGPT - แชทบอทอัจฉริยะ';
        this.toggleButton.style.cssText = `
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            width: 60px !important;
            height: 60px !important;
            background: #7b3b95 !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #fff !important;
            cursor: pointer !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2), 0 0 0 3px #ffd700 !important;
            z-index: 999999 !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            animation: none !important;
            transition: none !important;
        `;

        // เพิ่มการตรวจสอบว่าปุ่มถูกสร้างใน DOM หรือไม่
        if (document.body.contains(this.toggleButton)) {
            console.log('✅ ปุ่มถูกเพิ่มใน DOM แล้ว');
        } else {
            console.error('❌ ปุ่มไม่ถูกเพิ่มใน DOM');
        }

        // เพิ่มข้อความแสดงชื่อข้างปุ่มแบบ inline style
        const textElement = document.createElement('div');
        textElement.innerHTML = '🤖 DesUPGPT';
        textElement.style.cssText = `
            position: absolute !important;
            bottom: -25px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            font-size: 10px !important;
            font-weight: bold !important;
            color: #ffd700 !important;
            text-shadow: 0 0 5px rgba(255, 215, 0, 0.8) !important;
            background: rgba(0, 0, 0, 0.3) !important;
            padding: 2px 6px !important;
            border-radius: 8px !important;
            border: 1px solid rgba(255, 215, 0, 0.5) !important;
            white-space: nowrap !important;
            z-index: 999999 !important;
            opacity: 1 !important;
            visibility: visible !important;
        `;

        this.toggleButton.appendChild(textElement);
        console.log('📝 เพิ่มข้อความข้างปุ่มแล้ว');

        document.body.appendChild(this.toggleButton);

        console.log('🔧 ปุ่มแชทบอทถูกสร้างแล้ว:', this.toggleButton);
        console.log('📏 ตำแหน่งและขนาดปุ่ม:', {
            offsetTop: this.toggleButton.offsetTop,
            offsetLeft: this.toggleButton.offsetLeft,
            offsetWidth: this.toggleButton.offsetWidth,
            offsetHeight: this.toggleButton.offsetHeight,
            computedStyle: window.getComputedStyle(this.toggleButton)
        });
        
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

        // เปิดใช้งาน AudioContext เมื่อมีการคลิกครั้งแรก
        this.enableAudioContext();

        // เล่นเสียงเมื่อคลิกปุ่ม toggle
        this.playNotificationSound();

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

        // เล่นเสียงแจ้งเตือนเมื่อผู้ใช้ส่งข้อความ
        this.playNotificationSound();

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

        // เล่นเสียงแจ้งเตือนเมื่อบอทตอบกลับ
        this.playNotificationSound();

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

                // เล่นเสียงแจ้งเตือนเมื่อได้รับข้อความตอบกลับแบบ streaming
                this.playNotificationSound();

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

        // เปิดใช้งาน AudioContext เมื่อมีการส่งข้อความ (user interaction)
        this.enableAudioContext();

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
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 เริ่มต้นการทำงานของแชทบอท DesUPGPT...');

    // ตรวจสอบว่า initChatbot function มีอยู่หรือไม่
    if (typeof initChatbot === 'function') {
        console.log('✅ พบฟังก์ชัน initChatbot');
        initChatbot();
    } else {
        console.error('❌ ไม่พบฟังก์ชัน initChatbot');
    }

    // เพิ่มเอฟเฟกต์ต้อนรับเมื่อหน้าเว็บโหลดเสร็จ
    setTimeout(function() {
        console.log('🔍 กำลังมองหาปุ่มแชทบอท...');
        const chatbotToggle = document.querySelector('.chatbot-toggle');

        if (chatbotToggle) {
            console.log('✅ พบปุ่มแชทบอท!');
            console.log('📍 ตำแหน่งปุ่มก่อนเพิ่มคลาส:', {
                top: chatbotToggle.offsetTop,
                left: chatbotToggle.offsetLeft,
                width: chatbotToggle.offsetWidth,
                height: chatbotToggle.offsetHeight,
                display: chatbotToggle.style.display,
                visibility: chatbotToggle.style.visibility,
                opacity: chatbotToggle.style.opacity,
                zIndex: chatbotToggle.style.zIndex
            });

            chatbotToggle.classList.add('page-loaded');

            console.log('📍 ตำแหน่งปุ่มหลังเพิ่มคลาส:', {
                top: chatbotToggle.offsetTop,
                left: chatbotToggle.offsetLeft,
                width: chatbotToggle.offsetWidth,
                height: chatbotToggle.offsetHeight,
                display: chatbotToggle.style.display,
                visibility: chatbotToggle.style.visibility,
                opacity: chatbotToggle.style.opacity,
                zIndex: chatbotToggle.style.zIndex
            });

            // เพิ่มเอฟเฟกต์เสียงต้อนรับแบบง่าย ๆ
            if (window.satitChatbot) {
                console.log('🔊 กำลังเล่นเสียงต้อนรับ...');
                try {
                    // สร้างเสียงบี๊บง่าย ๆ
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.2);

                    console.log('✅ เสียงต้อนรับเล่นสำเร็จ');
                } catch (e) {
                    console.log('❌ ไม่สามารถเล่นเสียงต้อนรับได้:', e);
                }
            }

            console.log('🎉 เอฟเฟกต์ต้อนรับทำงานแล้ว');
        } else {
            console.log('❌ ไม่พบปุ่มแชทบอท');

            // แสดงรายการ elements ทั้งหมดใน body เพื่อดูว่าเกิดอะไรขึ้น
            console.log('📋 Elements ใน body:', document.body.children.length);
            for (let i = 0; i < Math.min(10, document.body.children.length); i++) {
                console.log(`Element ${i}:`, document.body.children[i].tagName, document.body.children[i].className);
            }

            // ลองหาปุ่มอีกครั้งหลังจากรอเพิ่มเติม
            setTimeout(function() {
                const retryToggle = document.querySelector('.chatbot-toggle');
                if (retryToggle) {
                    console.log('✅ พบปุ่มแชทบอทในการลองครั้งที่ 2');
                } else {
                    console.log('❌ ยังไม่พบปุ่มแชทบอทในการลองครั้งที่ 2');
                }
            }, 2000);
        }
    }, 1000); // รอ 1 วินาทีหลังจากหน้าเว็บโหลดเสร็จ
});
