<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = "category";
    protected $primaryKey = "category_id";

    /**
     * @param $pid integer
     * */
    public static function get_categories($pid = 0){

        return self::where('parent_id',$pid)->get();

    }
}
