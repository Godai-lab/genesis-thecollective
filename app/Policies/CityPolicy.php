<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\City  $city
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, City $city)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, $perm, $account)
    {
        if(!$user->haveFullAccess())
            if($user->havePermission($perm)){
                $userAccounts = $user->accounts->pluck('id')->toArray();
                if(in_array($account,$userAccounts)){
                    return true;
                }else{
                    return false;
                }
            }
            else
                return false;
        else
            return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\City  $city
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, City $city, $perm, $account = null)
    {
        if(!$user->haveFullAccess())
            if($user->havePermission($perm)){
                $cityAccount = $city->account_id;
                $userAccounts = $user->accounts->pluck('id')->toArray();
                if(in_array($cityAccount,$userAccounts)){
                    if($account!=null){
                        if(in_array($account,$userAccounts)){
                            return true;
                        }else{
                            return false;
                        }
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }else
                return false;
        else
            return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\City  $city
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, City $city, $perm)
    {
        if(!$user->haveFullAccess())
            if($user->havePermission($perm)){
                $userAccounts = $user->accounts->pluck('id')->toArray();
                if(in_array($city->account_id,$userAccounts)){
                    return true;
                }else{
                    return false;
                }
            }
            else
                return false;
        else
            return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\City  $city
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, City $city)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\City  $city
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, City $city)
    {
        //
    }
}
