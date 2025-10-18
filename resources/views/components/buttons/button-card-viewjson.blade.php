<form action="{{$buttonViewJson['route']}}" method="POST" target="_blank">
    <input type='hidden' value="{{$buttonViewJson['inputValue']}}" name="{{$buttonViewJson['inputName']}}">
    <input type="hidden" value="{{Auth::user()->global_key_api}}" name="global_key_api">
    <input type="hidden" value="{{Auth::user()->workspace_key_api}}" name="global_key_api">
    <button type="submit" class="inline-flex items-center py-2 px-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" width="1.4em" height="1.4em" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
    </button>
</form>