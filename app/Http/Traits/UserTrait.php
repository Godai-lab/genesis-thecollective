<?php 

namespace App\Http\Traits;

trait UserTrait{
    public function haveFullAccess(){
        if($this->status)
            foreach($this->roles as $role){
                if($role['full_access'] ){
                    return true;
                }
            }
        return false;
    }

    public function havePermission($permission){
        if($this->status)
            foreach($this->roles as $role){
                foreach($role->permissions as $perm){ 
                    $arrayPer = explode("|", $permission);
                    if(count($arrayPer)>1){
                        if(in_array($perm->slug, $arrayPer)){
                            return true;
                        }
                    }else{
                        if($perm->slug == $permission){
                            return true;
                        }
                    }
                }
            }
        return false;
    }
}