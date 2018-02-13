<?php

namespace InetStudio\Vkontakte\Models\Attachments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Vkontakte\Models\VkontaktePostModel;

class LinkModel extends Model
{
    use SoftDeletes;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'vkontakte_attachments_links';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'url', 'title', 'description', 'target', 'image_src', 'image_big',
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
     * Обратное отношение "один ко многим" с моделью поста вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(VkontaktePostModel::class, 'id', 'post_id');
    }
}
