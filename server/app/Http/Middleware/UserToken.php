<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class UserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token  = $request->input('token',0);
        if($token){
            $userId = DB::table('token')->where('mp_token',$token)->value('user_id');
            mylogger($userId);
            if($userId){

                $request->offsetSet('userId',$userId);


            }
        }

        return $next($request);
    }
}
