<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
use App\Concert;

class ConcertFactory
{
    public static function createPublished(array $overrides = []): Concert
    {
        $concert = factory(Concert::class)->create($overrides);
        $concert->publish();

        return $concert;
    }
}