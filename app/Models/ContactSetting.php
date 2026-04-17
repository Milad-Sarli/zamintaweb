<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $guarded = [];

    protected static ?self $instance = null;

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = static::first() ?? new static;
        }

        return static::$instance;
    }
}
