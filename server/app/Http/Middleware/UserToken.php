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
        $token  = $request->input('token',"");
        if($token){
            $userId = DB::table('token')->where('mp_token',$token)->value('user_id');
            if($userId){

                $request->offsetSet('userId',$userId);
            }
        }
        $request->offsetSet('userId',34);
        return $next($request);
    }
}
