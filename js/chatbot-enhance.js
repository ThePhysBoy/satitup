/**
 * เพิ่มฟังก์ชันการทำงานให้กับปุ่มแชทบอท
 * ทำให้ปุ่มมีการเคลื่อนไหวและการแจ้งเตือนที่น่าสนใจ
 */

document.addEventListener('DOMContentLoaded', function() {
    // รอให้โหลดหน้าเว็บเสร็จก่อน
    setTimeout(function() {
        initChatbotEnhancement();
    }, 1500);
});

function initChatbotEnhancement() {
    // หาปุ่มแชทบอท
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    
    if (!chatbotToggle) return;
    
    // เพิ่มเอฟเฟกต์เมื่อกดปุ่ม
    chatbotToggle.addEventListener('click', function() {
        // เล่นเอฟเฟกต์เสียงเมื่อกดปุ่ม (ถ้าต้องการ)
        // playClickSound();
        
        // ลบสถานะมีข้อความใหม่
        this.classList.remove('has-new-message');
        
        // เล่นเอฟเฟกต์การกด
        this.style.transform = 'scale(0.9)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
    
    // สร้างฟังก์ชันจำลองการมีข้อความใหม่ (สำหรับการสาธิต)
    simulateNewMessage();
}

// ฟังก์ชันจำลองการมีข้อความใหม่ (เพื่อการสาธิต)
function simulateNewMessage() {
    // สุ่มเวลาในการแสดงการแจ้งเตือน
    const randomTime = Math.floor(Math.random() * 15000) + 10000; // 10-25 วินาที
    
    setTimeout(function() {
        const chatbotToggle = document.querySelector('.chatbot-toggle');
        if (chatbotToggle) {
            // เพิ่มคลาสแสดงว่ามีข้อความใหม่
            chatbotToggle.classList.add('has-new-message');
            
            // เพิ่มคลาสแสดงการแจ้งเตือน (CSS จะจัดการแอนิเมชันให้)
            // ไม่ต้องกำหนด style.animation โดยตรงเพราะเราใช้คลาส CSS แล้ว
            
            // เพิ่มเสียง (ถ้าต้องการ)
            playNotificationSound();
        }
    }, randomTime);
}

// ฟังก์ชันเล่นเสียงเมื่อกดปุ่ม (ถ้าต้องการใช้)
function playClickSound() {
    const audio = new Audio('sounds/click.mp3');
    audio.volume = 0.5;
    audio.play().catch(error => {
        // จัดการกรณีที่เบราว์เซอร์บล็อกการเล่นเสียงอัตโนมัติ
        console.log('Auto-play was prevented');
    });
}

// ฟังก์ชันเล่นเสียงแจ้งเตือน
function playNotificationSound() {
    // ถ้ามีไฟล์เสียง notification.mp3 ให้ใช้บรรทัดนี้
    // const audio = new Audio('sounds/notification.mp3');
    
    // ถ้าไม่มีไฟล์เสียง ใช้ Web Audio API สร้างเสียงแจ้งเตือนง่ายๆ
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        
        const audioCtx = new AudioContext();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(587.33, audioCtx.currentTime); // ความถี่เสียง D5
        
        gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
        gainNode.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.05);
        gainNode.gain.linearRampToValueAtTime(0, audioCtx.currentTime + 0.5);
        
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.5);
        
        // เล่นเสียงที่สอง (สูงกว่า) หลังจากเสียงแรกเล็กน้อย
        setTimeout(() => {
            const oscillator2 = audioCtx.createOscillator();
            oscillator2.connect(gainNode);
            oscillator2.type = 'sine';
            oscillator2.frequency.setValueAtTime(880, audioCtx.currentTime); // ความถี่เสียง A5
            
            gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.05);
            gainNode.gain.linearRampToValueAtTime(0, audioCtx.currentTime + 0.5);
            
            oscillator2.start();
            oscillator2.stop(audioCtx.currentTime + 0.5);
        }, 150);
    } catch (e) {
        console.log('Web Audio API is not supported in this browser');
    }
}
