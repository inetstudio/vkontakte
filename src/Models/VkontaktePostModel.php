<?php

namespace InetStudio\Vkontakte\Models;

use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Модель поста вконтакте.
 *
 * Class VkontaktePostModel
 */
class VkontaktePostModel extends Model implements HasMediaConversions
{
    use SoftDeletes;
    use HasMediaTrait;

    /**
     * Имя социальной сети.
     */
    const NETWORK = 'vkontakte';

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
        'post_id', 'from_id', 'owner_id', 'post_source', 'post_type',
        'text', 'comments', 'likes', 'reposts', 'views', 'date',
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
     * Загрузка модели
     * Событие удаления поста вконтакте.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            $post->comments()->delete();
        });
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->belongsTo(VkontakteUserModel::class, 'from_id', 'user_id');
    }

    /**
     * Отношение "один ко многим" с моделью комментария вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(VkontakteCommentModel::class);
    }

    /**
     * Получаем ссылку на пост вконтакте.
     *
     * @return string
     */
    public function getPostURLAttribute()
    {
        return 'https://vk.com/id'.$this->from_id.'?w=wall'.$this->post_id;
    }

    /**
     * Получаем тип поста вконтакте.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return $this->post_type;
    }

    /**
     * Получаем текст поста.
     */
    public function getCaptionAttribute()
    {
        return Emoji::shortnameToUnicode($this->text);
    }

    /**
     * Создаем превью при сохранении изображений.
     */
    public function registerMediaConversions()
    {
        $quality = (config('vkontakte.images.quality')) ? config('vkontakte.images.quality') : 75;

        $this->addMediaConversion('edit_thumb')
            ->crop('crop-center', 96, 96)
            ->quality($quality)
            ->performOnCollections('images');

        $this->addMediaConversion('index_thumb')
            ->crop('crop-center', 320, 320)
            ->quality($quality)
            ->performOnCollections('images');

        foreach (config('vkontakte.images.sizes') as $size) {
            $this->addMediaConversion($size['width'].'x'.$size['height'].'_thumb')
                ->crop('crop-center', $size['width'], $size['height'])
                ->quality($quality)
                ->performOnCollections('images');
        }
    }
}
