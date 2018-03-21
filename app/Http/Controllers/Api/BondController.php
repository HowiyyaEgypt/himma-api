<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use JWTAuth;
use App\Services\APIAuthTrait;

use App\User;
use App\Bond;
use App\PendingBond;

use App\Http\Resources\Bond\UserBonds;
use App\Http\Resources\Bond\UserPendingBonds;

use App\Exceptions\Api\ValidationException;

class BondController extends Controller
{
    use APIAuthTrait;

    /**
     * Listing all bonds for the auth user
     * 
     * @returns a paginated collection of BondResource
     */
    public function listMyBonds(Request $request)
    {
        $user = $this->APIAuthenticate();
        return UserBonds::Collection($user->bonds()->with('users')->paginate(10));
    }

     /**
     * Listing all pending bonds for the auth user
     * 
     * @returns a paginated collection of BondResource
     */
    public function listMyPendingBonds(Request $request)
    {
        $user = $this->APIAuthenticate();
        return UserPendingBonds::Collection($user->receivedPendingBonds()->paginate(10));
    }

    /**
     * User A (auth user) request to form a new bond with user B
     * 
     * @returns - 450, 451 or 200 json response {success, message}
     */
    public function addNewBond(Request $request)
    {
        $user = $this->APIAuthenticate();
        
        $validator = Validator::make($request->all(), [
            'bond_partner_id' => 'required|numeric|exists:users,id'
        ]);

        if ( $validator->fails() ){
            throw new ValidationException($validator->errors()->first());       
        }

        $bond_partners_array = $user->bond_partners;
        $bond_partner_id = $request->get('bond_partner_id');
        $bond_partner = User::find($bond_partner_id);

        // checking if the user already has a bond with the other part
        if (in_array($bond_partner_id, $bond_partners_array)) {
            throw new ValidationException('There is already a bond between you and ' . $bond_partner->name);       
        }

        // checking if the user already sent a previous request
        if (count($user->sentPendingBonds->where('receiver_id', $bond_partner_id)) > 0) {
            return response()
            ->json(['success' => false, 'message' => 'You already sent a bond request to ' . $bond_partner->name])
            ->setStatusCode(450);
        }

        // checking if the other part already sent a previous request to the user
        if (count($user->receivedPendingBonds->where('sender_id', $bond_partner_id > 0))) {
            return response()
            ->json(['success' => false, 'message' => $bond_partner->name .' already sent you a bond request'])
            ->setStatusCode(451);
        }

        // all is OK .. creating the new pending bond object
        $pending_bond = PendingBond::create(['sender_id' => $user->id, 'receiver_id' => $bond_partner_id]);
        $user->sentPendingBonds()->save($pending_bond);

        // TODO: fire event to other part letting hom know about the pending request

        return response()
        ->json(['success' => true, 'message' => 'A bond request has been sent to ' . $bond_partner->name])
        ->setStatusCode(200);
    }

    /**
     * User B (auth user) accepts the bond request from User A
     * 
     * @returns - 450 or 200 json response {success, message}
     */
    public function acceptNewBond(Request $request)
    {
        $user = $this->APIAuthenticate();
        
        $validator = Validator::make($request->all(), [
            'bond_partner_id' => 'required|numeric|exists:users,id'
        ]);

        if ( $validator->fails() ){
            throw new ValidationException($validator->errors()->first());       
        }

        $bond_partners_array = $user->bond_partners;
        $bond_partner_id = $request->get('bond_partner_id');
        $bond_partner = User::find($bond_partner_id);

        // checking if the user already has a bond with the other part
        if (in_array($bond_partner_id, $bond_partners_array)) {
            throw new ValidationException('There is already a bond between you and ' . $bond_partner->name);       
        }

        // checking if the other part already sent a previous request to the user
        if (count($user->receivedPendingBonds->where('sender_id', $bond_partner_id)) == 0) {
            return response()
            ->json(['success' => false, 'message' => $bond_partner->name . " didn't send you a bond request"])
            ->setStatusCode(451);
        }

        // all is OK .. creating a new bond
        $bond = Bond::create(['sender_id' => $bond_partner_id , 'receiver_id' => $user->id]);

        // attaching bonds to bond_user pivot to both users
        $bond_partner->bonds()->syncWithoutDetaching($bond->id);
        $user->bonds()->syncWithoutDetaching($bond->id);

        // deattaching the pending bond record, we can remove it from the user (receiver) or the other part (sender)
        $user->receivedPendingBonds->where('sender_id', $bond_partner_id)->first()->delete();

        // TODO: fire event to other part letting hom know about the accepted request
        
        return response()
        ->json(['success' => true, 'message' => 'You are now connected with ' . $bond_partner->name])
        ->setStatusCode(200);
    }

    /**
     * Breaking an existing bond
     * 
     * @returns ??
     */
    public function breakBond(Bond $bond, Request $request)
    {
        $user = $this->APIAuthenticate();
        
        $validator = Validator::make($request->all(), [
            'bond_partner_id'   => 'required|numeric|exists:users,id'
        ]);

        if ( $validator->fails() ){
            throw new ValidationException($validator->errors()->first());       
        }

        $bond_partners_array = $user->bond_partners;
        $bond_partner_id = $request->get('bond_partner_id');
        $bond_partner = User::find($bond_partner_id);

        // checking if the user already has a bond with the other part
        if (!in_array($bond_partner_id, $bond_partners_array)) {
            throw new ValidationException( "You don't have a bond with " . $bond_partner->name);       
        }

        // the user may have a bond but we need to validate it is the exact
        if ($bond->partner->id != $bond_partner_id || $bond->user->id != $user->id) {
            return response()
            ->json(['success' => false, 'message' => "Invalid bond"])
            ->setStatusCode(450);
        }

        // all is Ok
        $user->bonds()->detach($bond->id);
        $bond_partner->bonds()->detach($bond->id);

        $bond->delete();

        // TODO: notify partner of break up

        return response()
        ->json(['success' => true, 'message' => 'The bond with ' . $bond_partner->name . ' has been deleted'])
        ->setStatusCode(200);
    }
}
