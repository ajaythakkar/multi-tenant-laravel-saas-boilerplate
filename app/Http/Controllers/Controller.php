<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Stancl\Tenancy\Tenant;

class Controller extends BaseController
{

    public function register(){

        return view('register');
    }

    public function registerSave(Request $request){

        if($request->subdomain) {

            try {
                //to create new Tenant

                $subdomain = $request->subdomain . "." . env('CENTRAL_DOMAINM');
                Tenant::new()
                    ->withDomains([$subdomain])
                    ->withData(['plan' => 'free'])->save();

                $url = "http://".$subdomain;
                echo "Created! <br> <a href='$url'>$subdomain</a>";

            } catch(\Exception $e) {

                echo $e->getMessage();
            }

        }else{

            echo "No Domain Created!";
        }
    }
}
