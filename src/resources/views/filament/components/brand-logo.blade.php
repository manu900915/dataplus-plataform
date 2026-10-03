<div class="fi-brand-wrapper flex items-center gap-3">
    <img 
        src="{{ asset('images/9.png') }}" 
        alt="Dataplus S.R.L." 
        class="fi-brand-icon h-9 w-9 rounded-full object-cover shadow-md flex-shrink-0 border border-sky-500/20"
    />
    <div 
        x-show="$store.sidebar ? $store.sidebar.isOpen : true" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-2"
        class="fi-brand-details flex flex-col leading-none"
    >
        <span class="fi-brand-title font-extrabold text-[1.1rem] tracking-tight text-white flex items-center gap-1 font-display">
            Dataplus <span class="text-sky-400 font-bold">S.R.L.</span>
        </span>
        <span class="fi-brand-subtitle text-[9px] font-semibold text-slate-400 uppercase tracking-widest mt-0.5">
            Plataforma
        </span>
    </div>
</div>
