<?php

namespace InetStudio\Vkontakte\Models\Attachments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Vkontakte\Models\VkontaktePostModel;

class PhotoModel extends Model
{
    use SoftDeletes;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'vkontakte_attachments_photos';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'pid', 'aid', 'owner_id', 'src', 'src_big', 'src_small',
        'src_xbig', 'src_xxbig', 'src_xxxbig', 'width', 'height', 'text',
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
     * Обратное отношение "один ко многим" с моделью поста вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(VkontaktePostModel::class, 'id', 'post_id');
    }
}
