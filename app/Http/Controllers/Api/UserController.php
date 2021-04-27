<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\UserType;
use App\Repositories\Repositories\UserRepositoryEloquent;
use App\Transformer\UserTransformer;
use App\Transformer\UserProfileTransformer;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\InviteRequest;
use App\Http\Requests\Api\NewUserRequest;
use App\Http\Requests\Api\VerifyAccountRequest;
use App\Http\Requests\Api\UserProfileRequest;
use App\Notifications\InvitationMail;
use App\Notifications\AccountVerificationCodeMail;

use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Set Repository
     */
    public $repository;

    /**
     * Define default settings
     */
    public function  __construct(UserRepositoryEloquent $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create api for login user
     * @param  LoginRequest LoginRequest
     * @return JSON
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = request(['user_name', 'password']);
            if (!auth()->attempt($credentials)) {
                return respond(config("messages.API_INVALID_LOGIN"),[],401);
            }
            $user = $request->user();
            if(empty($user->email_verified_at)) {
                $msg = config("messages.API_NEED_VERIFICATION");
                return respond($msg,[],402);
            }
            $user->access_token =$user->createToken('AU')->accessToken;

            $user = fractal($user,new UserTransformer())->toArray();
            return respond(config("messages.API_LOGIN_SUCCESS"),$user);
        } catch (\Exception $exception) {
            return respond(config("messages.API_INTERNAL_SERVER_ERROR"),[],500);
        }
    }

    /**
     * Invite User
     * @param  InviteRequest $request
     * @return JSON
     */
    public function inviteUser(InviteRequest $request)
    {
        try {
            $user = [
                'email' => $request->email
            ];
            if($user = $this->repository->create($user)) {
                $joinLink = route("join",['id'=>encrypt($user->id)]);
                $user->notify(new InvitationMail($joinLink));
                return respond(config("messages.API_INVITATION_SENT"),[]);
            }
            return respond(config("messages.API_INVITATION_FAIL"),[],404);
        } catch (\Exception $exception) {
            return respond(config("messages.API_INTERNAL_SERVER_ERROR"),[],500);
        }
    }

    /**
     * Add New User
     * @param NewUserRequest $request
     * @return JSON
     */
    public function join(NewUserRequest $request, $id)
    {
        try {
            $user = $this->repository->find(decrypt($id));
            if(empty($user) || !empty($user->user_name)) {
                return respond(config("messages.API_ACCOUNT_FAIL"),[],404);
            }
            $user->user_name = $request->user_name;
            $user->password = Hash::make($request->password);
            $user->verification_code = rand(100000,999999);
            if($user->save()) {
                $user->notify(new AccountVerificationCodeMail($user->verification_code));
                return respond(config("messages.API_ACCOUNT_SUCCESS"),[]);
            }
            return respond(config("messages.API_ACCOUNT_FAIL"),[]);
        } catch (\Exception $exception) {
            return respond(config("messages.API_INTERNAL_SERVER_ERROR"),[],500);
        }
    }

    /**
     * Verify Account
     * @param VerifyAccountRequest $request
     * @return JSON
     */
    public function verify(VerifyAccountRequest $request)
    {
        try {
            $user = $this->repository->where('email',$request->email)->first();
            if(empty($user) || empty($user->user_name)) {
                return respond(config("messages.API_INVALID_EMAIL"),[],404);
            }
            if(!empty($user->email_verified_at)) {
                return respond(config("messages.API_ACCOUNT_ALREADY_VERIFIED"),[],404);
            }
            if($user->verification_code == $request->verification_code) {
                $user->email_verified_at = Carbon::now();
                if($user->save()) {
                    return respond(config("messages.API_ACCOUNT_VERIFY_SUCCESS"),[]);
                }
            }
            return respond(config("messages.API_ACCOUNT_INVALID_CODE"),[],404);
        } catch (\Exception $exception) {
            return respond(config("messages.API_INTERNAL_SERVER_ERROR"),[],500);
        }
    }

    /**
     * Update Profile
     * @param UserProfileRequest $request
     * @return JSON
     */
    public function updateProfile(UserProfileRequest $request)
    {
        try {
            $profileData = $request->only(['name','user_name']);
            if(!empty($request->password)) {
                $profileData['password'] = Hash::make($request->password);
            }
            if(!empty($request->hasFile("avatar"))) {
                $profileData['avatar'] = $request->file('avatar')->store(env('PROFILE_PATH','profiles'),'public');
                $oldAvatar = auth()->user()->avatar;
            }
            if($this->repository->where("id",auth()->user()->id)->update($profileData)) {
                if(!empty($oldAvatar) && file_exists(public_path("storage/".$oldAvatar))) {
                    unlink(public_path("storage/".$oldAvatar));
                }
                $user = fractal(auth()->user(),new UserProfileTransformer())->toArray();
                return respond(config("messages.API_PROFILE_SUCCESS"),$user);
            }
            return respond(config("messages.API_PROFILE_ERROR"),[], 404);
        } catch (\Exception $exception) {
            return respond(config("messages.API_INTERNAL_SERVER_ERROR"),[],500);
        }
    }

    /**
     * Logout current login user
     * @param  Request $request
     * @return JSON
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return respond(config("messages.API_LOGOUT"),[],200);
        } catch (\Throwable $th) {
            return respond(config("messages.API_INTERNAL_SERVER_ERROR"),[],500);
        }
    }
}
