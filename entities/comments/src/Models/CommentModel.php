<?php

namespace InetStudio\Vkontakte\Comments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Vkontakte\Comments\Contracts\Models\CommentModelContract;

/**
 * Class CommentModel.
 */
class CommentModel extends Model implements CommentModelContract
{
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'vkontakte_comment';

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'vkontakte_comments';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'comment_id', 'post_id', 'user_id', 'additional_info',
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
     * Сеттер атрибута comment_id.
     *
     * @param $value
     */
    public function setCommentIdAttribute($value)
    {
        $this->attributes['comment_id'] = trim(strip_tags($value));
    }

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
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return self::ENTITY_TYPE;
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
     * Обратное отношение "один ко многим" с моделью поста в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(
            app()->make('InetStudio\Vkontakte\Posts\Contracts\Models\PostModelContract'),
            'post_id',
            'post_id'
        );
    }
}
