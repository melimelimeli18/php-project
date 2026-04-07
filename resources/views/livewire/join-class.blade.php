<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-zinc-200 dark:border-zinc-700 w-full max-w-md mx-4 p-6 overflow-hidden transition-all transform transform duration-300 scale-100 opacity-100">
                <div class="flex justify-between items-start mb-5">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100" id="modal-title">
                        Join a Class
                    </h3>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-5">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Please enter the 6-digit class code provided by your teacher to join your classroom.
                    </p>
                </div>

                <form wire:submit.prevent="joinClass" class="flex flex-col gap-4">
                    <div>
                        <label for="join_code" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Class Code</label>
                        <input 
                            wire:model="join_code"
                            type="text" 
                            id="join_code" 
                            class="w-full uppercase bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2.5 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 tracking-widest text-center font-bold text-lg" 
                            placeholder="A3F9KZ" 
                            maxlength="6"
                            autocomplete="off"
                            autofocus
                        >
                        @error('join_code') 
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-2 flex gap-3">
                        <button type="button" wire:click="closeModal" class="w-full bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-700 font-semibold py-2.5 px-4 rounded-lg transition-colors">
                            Skip for now
                        </button>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors">
                            Join Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
