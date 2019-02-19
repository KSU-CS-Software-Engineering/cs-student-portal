<?php

namespace App\Models;

use Illuminate\Support\Collection;

class Semester extends Validatable
{
    protected $dates = ['created_at', 'updated_at'];

    //https://github.com/felixkiss/uniquewith-validator

    protected function rules($params)
    {
        if ($params[0] < 0) {
            return array(
                'name' => 'required|string',
                'ordering' => 'required|integer|unique_with:semesters,plan_id',
                'plan_id' => 'required|exists:plans,id',
            );
        } else {
            return array(
                'name' => 'required|string',
                'ordering' => 'required|integer|unique_with:semesters,plan_id,' . $params[0],
                'plan_id' => 'required|exists:plans,id',
            );
        }
    }

    protected $fillable = ['name', 'ordering', 'plan_id'];

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan')->withTrashed();
    }

    public function requirements()
    {
        return $this->hasMany('App\Models\Planrequirement');
    }

    public function repairRequirementsOrder()
    {
        $requirements = $this->fresh()->requirements->sortBy('ordering');

        if ($requirements->last()->ordering + 1 === $requirements->count()) {
            return;
        }

        foreach ($requirements as $order => $requirement) {
            $requirement->ordering = $order;
            $requirement->save();
        }
    }

    public function reorderRequirements(Collection $newOrder)
    {
        //get all requirements for that semester to reorder
        $requirements = $this->fresh()->requirements;

        if ($requirements->count() !== $newOrder->count()) {
            abort(404);
        }

        $offset = $requirements->count();

        foreach ($newOrder as $key => $order) {
            $requirement = $requirements->where('id', $order['id'])->first();
            $requirement->ordering = $key + $offset;
            $requirement->save();
        }
        foreach ($requirements as $requirement) {
            $requirement->ordering -= $offset;
            $requirement->save();
        }
    }

}
