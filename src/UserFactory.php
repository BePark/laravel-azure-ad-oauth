<?php

namespace Metrogistics\AzureSocialite;

class UserFactory
{
	/** @var array */
    protected $config;

	/** @var  */
    protected static $user_callback;

    public function __construct()
    {
        $this->config = config('azure-oath');
    }

    public function convertAzureUser($user)
    {
        $user_class = config('azure-oath.user_class');

        $new_user = new $user_class($user);

        $callback = static::$user_callback;

        if($callback && is_callable($callback)){
            $callback($new_user);
        }

        $new_user->save();

        return $new_user;
    }

	/**
	 * @param $azure_user
	 * @return array
	 */
    public static function mapUserData($azure_user): array
    {
	    $user_map = config('azure-oath.user_map');
	    $id_field = config('azure-oath.user_id_field');
		$data = [];

		$data[$id_field] = $azure_user->id;

	    foreach($user_map as $azure_field => $user_field){
		    $data[$user_field] = $azure_user->$azure_field;
	    }

	    return $data;
    }

	/**
	 * @param $callback
	 * @throws \Exception
	 */
    public static function userCallback($callback)
    {
        if(! is_callable($callback)){
            throw new \Exception("Must provide a callable.");
        }

        static::$user_callback = $callback;
    }
}
