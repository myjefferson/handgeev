<!-- Dropdown menu -->
<div id="dropdown-workspace-{{$workspace->id }}" class="absolute z-10 hidden bg-slate-800 divide-y divide-slate-700 rounded-lg shadow-sm w-36 overflow-hidden border border-slate-700">
    <ul class="py-1 text-sm text-gray-300">
        <li>
            <button class="edit-btn dropdown-option flex items-center w-full px-4 py-2 bg-slate-800"
                data-id="{{ $workspace->id }}"
                data-title="{{ $workspace->title }}"
                data-route="{{ route('workspace.update', ['id' => $workspace->id]) }}"
                data-type-id="{{ $workspace->type_workspace_id }}"
                data-is-published="{{ $workspace->is_published }}"
                >
                <i class="fas fa-edit mr-2 text-xs"></i> Editar
            </button>
        </li>
        <li>
            <button type="button" 
                class="delete-btn dropdown-option flex items-center w-full px-4 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 bg-slate-800"
                data-id="{{ $workspace->id }}"
                data-title="{{ $workspace->title }}"
                data-route="{{ route('workspace.delete', ['id' => $workspace->id]) }}">
                <i class="fas fa-trash mr-2 text-xs"></i> Delete
            </button>
        </li>
    </ul>
</div>