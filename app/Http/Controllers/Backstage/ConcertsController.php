<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;

class ConcertsController extends Controller
{
    public function create()
    {
        return view('backstage/concerts/create');
    }
}
