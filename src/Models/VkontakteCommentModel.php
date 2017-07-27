<?php

namespace InetStudio\Vkontakte\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель комментария вконтакте.
 * 
 * Class VkontakteCommentModel
 *
 * @property int $id
 * @property string $comment_id
 * @property string $post_id
 * @property string $from_id
 * @property string $text
 * @property int $likes
 * @property \Carbon\Carbon $date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \InetStudio\Vkontakte\Models\VkontaktePostModel $post
 * @property-read \InetStudio\Vkontakte\Models\VkontakteUserModel $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontakteCommentModel withoutTrashed()
 * @mixin \Eloquent
 */
class VkontakteCommentModel extends Model
{
    use SoftDeletes;

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
        'comment_id', 'post_id', 'from_id', 'text', 'likes', 'date',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Обратное отношение "один ко многим" с моделью пользователя вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(VkontakteUserModel::class, 'from_id', 'user_id');
    }

    /**
     * Обратное отношение "один ко многим" с моделью поста вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(VkontaktePostModel::class);
    }
}
