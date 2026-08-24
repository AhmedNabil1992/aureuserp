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
    }" class="flex flex-col gap-3 p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-300 dark:border-gray-700 w-full shadow-sm">

        {{-- حالة قبل التسجيل (زرار البدء) --}}
        <div x-show="!isRecording && !audioUrl" class="w-full">
            <x-filament::button
                color="primary"
                icon="heroicon-m-microphone"
                class="w-full justify-center"
                x-on:click.prevent="start"
            >
                بدء تسجيل رسالة صوتية
            </x-filament::button>
        </div>

        {{-- حالة أثناء التسجيل (زرار الإيقاف + العداد) --}}
        <div x-show="isRecording" style="display: none;" class="w-full flex items-center gap-4">
            <div class="flex-grow">
                <x-filament::button
                    color="danger"
                    icon="heroicon-m-stop-circle"
                    class="w-full justify-center"
                    x-on:click.prevent="stop"
                >
                    إيقاف التسجيل
                </x-filament::button>
            </div>
            
            {{-- العداد وتأثير النبض --}}
            <div class="flex items-center gap-2 text-danger-600 dark:text-danger-500 font-bold text-lg min-w-[70px] justify-center" dir="ltr">
                <div class="w-3 h-3 rounded-full bg-danger-600 dark:bg-danger-500 animate-pulse"></div>
                <span x-text="formattedTime" class="tabular-nums"></span>
            </div>
        </div>

        {{-- حالة بعد الانتهاء (مشغل الصوت + زر الحذف) --}}
        <div x-show="audioUrl" style="display: none;" class="w-full flex items-center gap-3">
            {{-- مشغل الصوت --}}
            <audio :src="audioUrl" controls class="flex-grow h-11 rounded-lg outline-none"></audio>
            
            {{-- زرار الحذف --}}
            <div class="flex-shrink-0">
                <x-filament::icon-button
                    icon="heroicon-m-trash"
                    color="danger"
                    size="lg"
                    tooltip="حذف وإعادة التسجيل"
                    x-on:click.prevent="clear"
                />
            </div>
        </div>
    </div>
</x-dynamic-component>