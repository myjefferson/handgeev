<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Workspace;

class CheckApiMethodPermission
{
    public function handle(Request $request, Closure $next, $endpoint): Response
    {
        $workspaceId = $this->getWorkspaceIdFromRequest($request, $endpoint);
        
        if (!$workspaceId) {
            return $next($request);
        }

        $workspace = Workspace::find($workspaceId);
        
        if (!$workspace || !$workspace->isMethodAllowed($endpoint, $request->method())) {
            return response()->json([
                'error' => 'Method not allowed',
                'message' => 'The HTTP method is not permitted for this endpoint'
            ], 405);
        }

        return $next($request);
    }

    private function getWorkspaceIdFromRequest(Request $request, $endpoint)
    {
        switch ($endpoint) {
            case 'workspace':
                return $request->route('workspaceId');
            case 'topics':
                // Para endpoints de tópicos, precisamos encontrar o workspace
                $topicId = $request->route('topicId');
                if ($topicId) {
                    $topic = \App\Models\Topic::find($topicId);
                    return $topic ? $topic->workspace_id : null;
                }
                return $request->route('workspaceId');
            case 'fields':
                // Para endpoints de campos, encontrar via tópico
                $fieldId = $request->route('fieldId');
                if ($fieldId) {
                    $field = \App\Models\Field::with('topic')->find($fieldId);
                    return $field ? $field->topic->workspace_id : null;
                }
                $topicId = $request->route('topicId');
                if ($topicId) {
                    $topic = \App\Models\Topic::find($topicId);
                    return $topic ? $topic->workspace_id : null;
                }
                return null;
            default:
                return null;
        }
    }
}