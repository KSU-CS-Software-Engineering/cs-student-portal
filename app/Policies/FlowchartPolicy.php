<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlowchartPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is advisor.
     *
     * @param  \App\Models\User $user
     * @return bool|null
     */
    public function before(User $user)
    {
        if ($user->is_advisor) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return bool
     */
    public function read(User $user, Plan $plan)
    {
        return $user->student->id === $plan->student->id;
    }

    /**
     * Determine whether the user can update the plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return bool
     */
    public function modify(User $user, Plan $plan)
    {
        return $this->read($user, $plan);
    }
}
