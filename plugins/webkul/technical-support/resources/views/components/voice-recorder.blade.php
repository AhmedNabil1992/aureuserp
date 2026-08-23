<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        isRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioUrl: null,
        recordingTime: 0,
        timerInterval: null,

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
                clearInterval(this.timerInterval);
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

        <div x-show="!isRecording && !audioUrl" class="w-full">
            <button @click.prevent="start" type="button" class="w-full inline-flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition-colors shadow-sm">
                <x-heroicon-m-microphone class="w-5 h-5" />
                <span>بدء تسجيل رسالة صوتية</span>
            </button>
        </div>

        <div x-show="isRecording" style="display: none;" class="w-full flex items-center gap-3">
            <button @click.prevent="stop" type="button" class="flex-grow inline-flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-danger-600 hover:bg-danger-700 text-white text-sm font-medium transition-colors shadow-sm">
                <x-heroicon-m-stop-circle class="w-5 h-5" />
                <span>إيقاف التسجيل</span>
            </button>
            
            <div class="flex items-center gap-2 text-danger-600 font-bold text-base min-w-[70px] justify-center">
                <div class="w-2.5 h-2.5 rounded-full bg-danger-600 animate-pulse"></div>
                <span x-text="formattedTime"></span>
            </div>
        </div>

        <div x-show="audioUrl" style="display: none;" class="w-full flex items-center gap-2">
            <audio :src="audioUrl" controls class="flex-grow h-11 rounded-lg"></audio>
            
            <button @click.prevent="clear" type="button" class="p-2.5 rounded-lg bg-danger-50 text-danger-600 hover:bg-danger-100 dark:bg-danger-950 dark:text-danger-400 transition-colors" title="حذف وإعادة التسجيل">
                <x-heroicon-m-trash class="w-5 h-5" />
            </button>
        </div>
    </div>
</x-dynamic-component>
