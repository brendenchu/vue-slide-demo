<?php

namespace App\Http\Controllers\Story\Form;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoryFormRequest;
use App\Services\ProjectService;
use App\Services\TokenService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Save form responses for a story project.
 */
class SaveFormController extends Controller
{
    /**
     * Save user responses for a project step.
     *
     * Guest users can view forms but cannot save responses.
     * All other authenticated users can save their progress.
     *
     * @param  StoryFormRequest  $request  Validated form data (project, step, token, responses)
     * @param  ProjectService  $projectService  Service for project operations
     * @param  TokenService  $tokenService  Service for token operations
     * @return JsonResponse Success response with message
     *
     * @throws Exception If project or token operations fail
     */
    public function __invoke(
        StoryFormRequest $request,
        ProjectService $projectService,
        TokenService $tokenService,
    ): JsonResponse {

        /**
         * Guest users are allowed to view and navigate forms,
         * but their responses are not persisted to the database.
         * This allows for demo/preview functionality without data pollution.
         */
        if (! auth()->user()->hasRole('guest')) {
            // save the validated responses
            $projectService
                ->setProject($request->project['id'])
                ->setSteps($request->step['id'])
                ->saveResponses($request->validated());

            // save the last position for resume functionality
            $tokenService
                ->setToken($request->token)
                ->saveLastPosition(
                    $request->step['id'],
                    $request->page
                );
        }

        // flash success message (shown even for guests for better UX)
        session()->flash('success', 'Your responses have been saved.');

        return response()->json([
            'message' => 'Your responses have been saved.',
            'success' => true,
        ]);
    }
}
