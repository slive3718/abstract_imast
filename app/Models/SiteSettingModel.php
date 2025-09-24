<?php
namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];


    function get_current_disclosure_date($field_name = null){
        return $this->where('name', $field_name)->first()['value'];
    }

    function get_current_nonexclusive_date($field_name = null){
        return $this->where('name', $field_name)->first()['value'];
    }
}