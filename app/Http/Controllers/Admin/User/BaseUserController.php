<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Account\Profile;
use App\Models\User;
use App\Services\AccountService;
use App\Services\ProjectService;
use App\Services\TokenService;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class BaseUserController extends Controller
{
    /**
     * @throws Exception
     */
    public function __construct(
        protected readonly AccountService $accountService,
        protected readonly ProjectService $projectService,
        protected readonly TokenService $tokenService,
        protected User $user,
        protected Collection $projects,
        protected Collection $tokens,

    ) {
        //
    }

    /**
     * Set up the user from a Profile model (route model binding).
     *
     * @param  Profile  $profile  The profile instance from route binding
     *
     * @throws Exception
     */
    protected function setupUser(Profile $profile): void
    {
        // get the user from the profile relationship (no query needed)
        $this->user = $profile->user;

        // get the projects that belong to the user's team
        $this->projects = $this->projectService->getProjectsByTeam($this->user->currentTeam());

        // get the tokens that belong to the user
        $this->projects->each(function ($project): void {
            // push existing tokens to the tokens collection
            if ($existingTokens = $project->tokens()->where('user_id', auth()->id())->first()) {
                $this->tokens->push($existingTokens);
            }

            // push new tokens to the tokens collection for user that is not the current user
            if ($this->user !== auth()->user() && (! $this->tokenService->hasToken($project) && $this->tokenService->hasToken($project, $this->user))) {
                $this->tokens->push($this->tokenService->createToken($project));
            }

        });

    }

    /**
     * Set up the user by identifier (email or slug).
     *
     * Used when Profile is not available via route binding (e.g., search by email).
     *
     * @param  string  $identifier  Email address or profile slug
     *
     * @throws Exception
     */
    protected function setupUserByIdentifier(string $identifier): void
    {
        // get the user by slug or email
        $this->user = $this->accountService->setUser($identifier)->getUser();

        // get the projects that belong to the user's team
        $this->projects = $this->projectService->getProjectsByTeam($this->user->currentTeam());

        // get the tokens that belong to the user
        $this->projects->each(function ($project): void {
            // push existing tokens to the tokens collection
            if ($existingTokens = $project->tokens()->where('user_id', auth()->id())->first()) {
                $this->tokens->push($existingTokens);
            }

            // push new tokens to the tokens collection for user that is not the current user
            if ($this->user !== auth()->user() && (! $this->tokenService->hasToken($project) && $this->tokenService->hasToken($project, $this->user))) {
                $this->tokens->push($this->tokenService->createToken($project));
            }

        });

    }
}
