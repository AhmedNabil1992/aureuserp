<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        isRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioUrl: null,

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

                    // تحويل الملف لـ Base64 عشان نبعته للـ Backend بسهولة
                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = () => {
                        this.state = reader.result;
                    };
                    stream.getTracks().forEach(track => track.stop());
                });

                this.mediaRecorder.start();
                this.isRecording = true;
            } catch (error) {
                alert('يرجى السماح باستخدام المايكروفون!');
            }
        },

        stop() {
            if(this.mediaRecorder) {
                this.mediaRecorder.stop();
                this.isRecording = false;
            }
        },

        clear() {
            this.state = null;
            this.audioUrl = null;
            this.audioChunks = [];
        }
    }" class="flex items-center gap-4 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">

        <!-- زرار التسجيل -->
        <button x-show="!isRecording && !audioUrl" @click.prevent="start" type="button" class="flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500">
            <x-heroicon-m-microphone class="w-5 h-5" />
            <span>تسجيل رسالة صوتية</span>
        </button>

        <!-- زرار الإيقاف -->
        <button x-show="isRecording" @click.prevent="stop" type="button" class="flex items-center gap-2 px-4 py-2 bg-danger-600 text-white rounded-lg animate-pulse">
            <x-heroicon-m-stop-circle class="w-5 h-5" />
            <span>إيقاف التسجيل</span>
        </button>

        <!-- مشغل الصوت بعد الانتهاء -->
        <audio x-show="audioUrl" :src="audioUrl" controls class="h-10"></audio>

        <!-- زرار الحذف -->
        <button x-show="audioUrl" @click.prevent="clear" type="button" class="text-danger-600 hover:text-danger-500">
            <x-heroicon-m-trash class="w-6 h-6" />
        </button>

    </div>
</x-dynamic-component>