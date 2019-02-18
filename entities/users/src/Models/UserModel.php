<?php

namespace InetStudio\Vkontakte\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Vkontakte\Users\Contracts\Models\UserModelContract;

/**
 * Class UserModel.
 */
class UserModel extends Model implements UserModelContract, HasMedia
{
    use HasImages;
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'vkontakte_user';

    /**
     * Конфиг изображений.
     *
     * @var array
     */
    protected $images = [
        'config' => 'vkontakte_users',
        'model' => 'user',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'vkontakte_users';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'additional_info',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы к базовым типам.
     *
     * @var array
     */
    protected $casts = [
        'additional_info' => 'array',
    ];

    /**
     * Сеттер атрибута user_id.
     *
     * @param $value
     */
    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута additional_info.
     *
     * @param $value
     */
    public function setAdditionalInfoAttribute($value)
    {
        $this->attributes['additional_info'] = json_encode((array) $value);
    }

    /**
     * Геттер атрибута url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return 'https://vk.com/id'.$this->user_id;
    }

    /**
     * Геттер атрибута nickname.
     *
     * @return mixed
     */
    public function getNicknameAttribute()
    {
        return $this->full_name;
    }

    /**
     * Геттер атрибута full_name.
     *
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return trim(($this->additional_info['first_name'] ?? '').' '.($this->additional_info['last_name'] ?? ''));
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Отношение "один ко многим" с моделью поста в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(
            app()->make('InetStudio\Vkontakte\Posts\Contracts\Models\PostModelContract'),
            'user_id',
            'user_id'
        );
    }

    /**
     * Отношение "один ко многим" с моделью комментария в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(
            app()->make('InetStudio\Vkontakte\Comments\Contracts\Models\CommentModelContract'),
            'user_id',
            'user_id'
        );
    }
}
