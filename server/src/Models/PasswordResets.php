<?php

    namespace Gernzy\Server\Models;

    use Illuminate\Database\Eloquent\Model;

    class PasswordResets extends Model
    {
        public $timestamps = false;

        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'gernzy_password_resets';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'email',
            'token',
            'password',
            'password_confirmation',
            'created_at'
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
        ];
    }
