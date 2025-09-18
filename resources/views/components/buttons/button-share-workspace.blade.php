<div class="flex items-center">
    <input type="hidden" id="global-hash" value="{{ Auth::user()->global_hash_api }}">

    <button type="button" id="view-json-btn" class="flex items-center font-medium bg-cyan-400 text-slate-950 rounded-xl py-2 px-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
        JSON
    </button>
</div>