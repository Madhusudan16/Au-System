<?php
namespace App\Transformer;

use App\Models\User;
use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
	{
	    return [
	        'id'      => (int) $user->id,
	        'name'   => $user->name,
            'access_token' => $user->access_token,
        ];
	}

}
