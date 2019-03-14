<?php

namespace Mhpix\App\Controllers\BackEnd\Auth;
use Session;
trait RedirectsUsers
{
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo()->with('succ_log',' Welcome Back' );
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
