<?php

namespace App\Http\Controllers\Story\Form;

use App\Enums\ProjectStep;
use App\Http\Resources\Story\ProjectResource;
use App\Models\Story\Project;
use App\Services\ProjectService;
use App\Services\TokenService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoadFormController
{
    /**
     * Load a form for a specific project step.
     *
     * @param  Request  $request  The HTTP request
     * @param  ProjectService  $projectService  Service for project operations
     * @param  TokenService  $tokenService  Service for token verification
     * @param  Project  $project  The project instance (route model binding)
     * @param  ProjectStep  $step  The project step enum (route binding)
     * @return Response|RedirectResponse Inertia response or redirect on error
     *
     * @throws Exception If project or token operations fail
     */
    public function __invoke(
        Request $request,
        ProjectService $projectService,
        TokenService $tokenService,
        Project $project,
        ProjectStep $step
    ): Response|RedirectResponse {
        // if no token in query string
        if (! $request->has('token')) {
            // flash error message
            session()->flash('error', 'Token is required.');

            // redirect to create story page
            return to_route('story.create');
        }

        // if any of the required fields are missing, flash an error message
        if (! $tokenService
            ->setToken($request->token)
            ->verifyToken($project)) {
            // flash error message
            session()->flash('error', 'User token is invalid.');

            // redirect to create story page
            return to_route('story.create');
        }

        // get story responses
        $responses = $projectService
            ->setProject($project->public_id)
            ->setSteps($step->value)
            ->getResponsesArray(grouped: true);

        // handle missing step data gracefully
        if (! isset($responses[$step->value])) {
            session()->flash('error', 'No data found for this step.');

            return to_route('story.create');
        }

        // render story page
        return Inertia::render('Story/StoryForm', [
            'project' => ProjectResource::make($project),
            'step' => [
                'id' => $step->value,
                'slug' => $step->slug(),
                'name' => $step->label(),
            ],
            'token' => $request->token,
            'page' => $request->integer('page', 1),
            'direction' => $request->string('direction', 'next')->toString(),
            'story' => $responses[$step->value],
        ]);
    }
}
