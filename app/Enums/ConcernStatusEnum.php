<?php
namespace App\Enums;

enum ConcernStatusEnum: string
{
    case PENDING = 'pending';
    case RESOLVED = 'resolved';
}
