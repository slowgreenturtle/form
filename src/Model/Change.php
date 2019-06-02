<?php

namespace SGT\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Change extends Model
{

    const UPDATED_AT = null;

    protected $table = 'model_changes';

    protected $fillable = [
        'reportable_id', # the model id which is being tracked.
        'reportable_type', # the model type which is being mapped to
        'field',
        'value',
        'user_id'
    ];

    public function reportable()
    {

        return $this->morphTo();
    }

    public static function boot()
    {

        parent::boot();

        self::saving(function ($history)
        {

            $user = auth()->user();

            if ($user)
            {
                $history->user_id = $user->id;
            }
        });

    }

    public function user()
    {

        return $this->belongsTo(User::class);
    }

}
