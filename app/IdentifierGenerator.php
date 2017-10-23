<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

interface IdentifierGenerator
{
    public function generateConfirmationNumber(): string;

    public function generateCodeFor(Ticket $ticket): string;
}
