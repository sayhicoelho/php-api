<?php

namespace App\Http\Controllers;

use Core\Request;

class ExampleController extends Controller
{
    /**
     * Example action.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function example (Request $request)
    {
        return response (['success' => true], 200);
    }
}
