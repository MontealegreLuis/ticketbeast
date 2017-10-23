<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

use Hashids\Hashids;

class RandomIdentifierGenerator implements IdentifierGenerator
{
    /** @var Hashids */
    private $encoder;

    public function __construct(string $salt)
    {
        $this->encoder = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQURSTUVWXYZ');
    }

    public function generateConfirmationNumber(): string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVXYZ';

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }

    public function generateCodeFor(Ticket $ticket): string
    {
        return $this->encoder->encode($ticket->id);
    }
}
