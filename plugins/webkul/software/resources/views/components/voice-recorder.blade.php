<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        isRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioUrl: null,
        recordingTime: 0,
        timerInterval: null,

        // دالة لعرض الوقت بشكل 00:00
        get formattedTime() {
            let minutes = Math.floor(this.recordingTime / 60);
            let seconds = this.recordingTime % 60;
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },

        async start() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];

                this.mediaRecorder.addEventListener('dataavailable', event => {
                    this.audioChunks.push(event.data);
                });

                this.mediaRecorder.addEventListener('stop', () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    this.audioUrl = URL.createObjectURL(audioBlob);

                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = () => {
                        this.state = reader.result;
                    };
                    stream.getTracks().forEach(track => track.stop());
                });

                this.mediaRecorder.start();
                this.isRecording = true;
                
                // تشغيل العداد
                this.recordingTime = 0;
                this.timerInterval = setInterval(() => {
                    this.recordingTime++;
                }, 1000);

            } catch (error) {
                alert('يرجى السماح باستخدام المايكروفون!');
            }
        },

        stop() {
            if(this.mediaRecorder) {
                this.mediaRecorder.stop();
                this.isRecording = false;
                clearInterval(this.timerInterval); // إيقاف العداد
            }
        },

        clear() {
            this.state = null;
            this.audioUrl = null;
            this.audioChunks = [];
            this.recordingTime = 0;
            clearInterval(this.timerInterval);
        }
    }" class="flex flex-col gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 w-full">

        {{-- حالة قبل التسجيل --}}
        <div x-show="!isRecording && !audioUrl" class="w-full">
            <button @click.prevent="start" type="button" style="background-color: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 500; font-size: 14px; width: 100%; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                <x-heroicon-m-microphone class="w-5 h-5" />
                <span>بدء تسجيل رسالة صوتية</span>
            </button>
        </div>

        {{-- حالة أثناء التسجيل (زر إيقاف أحمر + عداد) --}}
        <div x-show="isRecording" style="display: none;" class="w-full flex items-center gap-3">
            <button @click.prevent="stop" type="button" style="background-color: #ef4444; color: white; padding: 8px 16px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 500; font-size: 14px; flex-grow: 1;">
                <x-heroicon-m-stop-circle class="w-5 h-5" />
                <span>إيقاف التسجيل</span>
            </button>
            
            {{-- العداد --}}
            <div style="display: flex; align-items: center; gap: 6px; color: #ef4444; font-weight: bold; font-size: 16px; min-width: 60px; justify-content: center;">
                <div style="width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%; animation: pulse 1s infinite;"></div>
                <span x-text="formattedTime"></span>
            </div>
        </div>

        {{-- حالة بعد الانتهاء (مشغل الصوت + زر حذف) --}}
        <div x-show="audioUrl" style="display: none;" class="w-full flex items-center gap-2">
            <audio :src="audioUrl" controls style="flex-grow: 1; height: 44px; border-radius: 8px;"></audio>
            
            <button @click.prevent="clear" type="button" style="background-color: #fee2e2; color: #ef4444; padding: 10px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: background-color 0.2s;" title="حذف وإعادة التسجيل">
                <x-heroicon-m-trash class="w-5 h-5" />
            </button>
        </div>

        {{-- تأثير النبض الخاص بالتسجيل --}}
        <style>
            @keyframes pulse {
                0% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.2); }
                100% { opacity: 1; transform: scale(1); }
            }
        </style>
    </div>
</x-dynamic-component>