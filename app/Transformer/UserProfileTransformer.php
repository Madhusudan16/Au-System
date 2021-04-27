<?php
namespace App\Transformer;

use App\Models\User;
use League\Fractal;

class UserProfileTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
	{
	    return [
	        'name'   => $user->name,
            'user_name' => $user->user_name,
            'avatar' => !empty($user->avatar) ? asset("storage/".$user->avatar) : '',
        ];
	}

}
