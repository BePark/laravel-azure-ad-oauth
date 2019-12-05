<?php

namespace Metrogistics\AzureSocialite;

use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToOauthProvider()
    {
        return Socialite::driver('azure-oauth')->redirect();
    }

    public function handleOauthResponse()
    {
        $user = Socialite::driver('azure-oauth')->user();

        $user = UserFactory::mapUserData($user);

        $authUser = $this->findOrCreateUser($user);

        auth()->login($authUser, true);

        return redirect(
            config('azure-oath.redirect_on_login')
        );
    }

    protected function findOrCreateUser($user)
    {
        $user_class = config('azure-oath.user_class');
        $azureUserId = config('azure-oath.user_id_field');

        $authUser = $user_class::where($azureUserId, $user[$azureUserId])->first();

        if ($authUser)
        {
            return $authUser;
        }

        $userFactory = new UserFactory();
        return $userFactory->convertAzureUser($user);
    }
}
