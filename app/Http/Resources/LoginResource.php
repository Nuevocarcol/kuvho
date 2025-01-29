<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     
     
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'fullname' => (string)$this->fullname,
            'username' => (string)$this->username,
            'email' => (string)$this->email,
            'phone' => (string)$this->phone,
            'salt' => (string)$this->salt,
            'password' => (string)$this->password,
            // 'email_verified_at' => (string)$this->email_verified_at,
            'login_type' => (string)$this->login_type,
            'google_id' => (string)$this->google_id,
            'profile_pic' => $this->profile_pic ?  url('public/images/user/'. $this->profile_pic) : "",
            'cover_pic' => $this->cover_pic ? url('public/images/user/'.$this->cover_pic) : "",
            // 'dob' => (string)$this->dob,
            'age' => (string)$this->age,
            'gender' => (string)$this->gender,
            'country' => (string)$this->country,
            'state' => (string)$this->state,
            'city' => (string)$this->city,
            'bio' => (string)$this->bio,
            'interests_id' => [],
            'device_token' => (string)$this->device_token,
            'created_at' => $this->created_at,
            'join_month' => $this->join_month ?? "",
            // 'country_id' => (int)$this->country_id,
            // 'state_id' => (int)$this->state_id,
            // 'city_id' => (int)$this->city_id,
           
           
            
            // 'is_Private' => (string)$this->is_Private,
            // 'create_date' => (string)$this->create_date,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
