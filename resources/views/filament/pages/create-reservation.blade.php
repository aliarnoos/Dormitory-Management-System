        <div class=" m-8">
            <br/>
            <h1 class="text-2xl font-bold">Create Reservation</h1>
            <br/>
            <form wire:submit.prevent="submit">
                {{ $this->form }}
    
                <div class="mt-6">
                    <x-filament::button wire:click="submit">
                        Send
                    </x-filament::button>
                </div>
            </form>
        </div>
