<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

class RandomConfirmationNumberGenerator implements ConfirmationNumberGenerator
{
    public function generate(): string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVXYZ';

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}
