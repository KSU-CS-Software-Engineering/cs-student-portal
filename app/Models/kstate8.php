<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kstate8 extends Model {


  protected $table = 'kstate8';

    public function Area() {
        return $this->area_id;
    }


    protected $fillable = ['id', 'course_id', 'area_id'];
}
