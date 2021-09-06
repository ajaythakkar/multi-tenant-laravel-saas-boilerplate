<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Stancl\Tenancy\Tenant;

class Controller extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(){

        //to create new Tenant
        //Tenant::new()->withDomains(['tenant1.localhost'])->withData(['plan' => 'free'])->save();
        echo"Test";
    }
}
