<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      
            return [
                'name' => $this->name,
                'email' => $this->email,
                'address' => $this->address,
                'city' => $this->city,
                'phone' => $this->phone,
                'role'=> $this->role,
            ];
        }
    
    }

