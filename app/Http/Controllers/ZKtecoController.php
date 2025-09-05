<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jmrashed\Zkteco\Lib\ZKTeco;

class ZKtecoController extends Controller
{
    public function zkteco()
    {
        $zk = new ZKTeco('192.168.1.201');
       dd($zk);
    }
}
