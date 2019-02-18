<?php

namespace InetStudio\Vkontakte\Posts\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Vkontakte\Posts\Contracts\Models\PostModelContract;

/**
 * Class PostModel.
 */
class PostModel extends Model implements PostModelContract, HasMedia
{
    use HasImages;
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'vkontakte_post';

    /**
     * Имя социальной сети.
     */
    const NETWORK = 'vkontakte';

    /**
     * Конфиг изображений.
     *
     * @var array
     */
    protected $images = [
        'config' => 'vkontakte_posts',
        'model' => 'post',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'vkontakte_posts';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'user_id', 'additional_info',
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
     * Сеттер атрибута post_id.
     *
     * @param $value
     */
    public function setPostIdAttribute($value)
    {
        $this->attributes['post_id'] = trim(strip_tags($value));
    }

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
     * Геттер атрибута caption.
     *
     * @return mixed
     */
    public function getCaptionAttribute()
    {
        return $this->additional_info['text'];
    }

    /**
     * Геттер атрибута url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return 'https://vk.com/id'.$this->additional_info['user']['id'].'?w=wall'.$this->post_id;
    }

    /**
     * Геттер атрибута media_type.
     *
     * @return string
     */
    public function getMediaTypeAttribute()
    {
        $type = '';

        foreach ($this->additional_info['attachments'] as $attachment) {
            if (! $type) {
                $type = $attachment['type'];
            } else {
                $type = 'carousel';
            }
        }

        return $type;
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
     * Геттер атрибута social_name.
     *
     * @return string
     */
    public function getSocialNameAttribute()
    {
        return self::NETWORK;
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            app()->make('InetStudio\Vkontakte\Users\Contracts\Models\UserModelContract'),
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
            'post_id',
            'post_id'
        );
    }
}
