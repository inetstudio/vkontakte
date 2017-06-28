<?php

namespace InetStudio\Vkontakte\Models;

use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Модель пользователя вконтакте.
 *
 * Class VkontakteUserModel
 */
class VkontakteUserModel extends Model implements HasMediaConversions
{
    use SoftDeletes;
    use HasMediaTrait;

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
        'user_id', 'first_name', 'last_name', 'nickname', 'screen_name',
        'has_photo', 'photo_id', 'photo_50', 'photo_100', 'photo_200', 'photo_200_orig', 'photo_400_orig', 'photo_max', 'photo_max_orig',
        'followers_count', 'common_count',
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
     * Загрузка модели
     * Событие удаления пользователя вконтакте.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->posts()->delete();
            $user->comments()->delete();
        });
    }

    /**
     * Отношение "один ко многим" с моделью поста вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(VkontaktePostModel::class, 'from_id', 'user_id');
    }

    /**
     * Отношение "один ко многим" с моделью комментария вконтакте.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(VkontakteCommentModel::class, 'from_id', 'user_id');
    }

    /**
     * Получаем никнейм пользователя вконтакте.
     *
     * @return string
     */
    public function getUserNicknameAttribute()
    {
        return $this->getUserFullNameAttribute();
    }

    /**
     * Получаем ссылку на профиль пользователя вконтакте.
     *
     * @return string
     */
    public function getUserURLAttribute()
    {
        return 'https://vk.com/'.$this->screen_name;
    }

    /**
     * Получаем имя пользователя.
     *
     * @return mixed
     */
    public function getUserFullNameAttribute()
    {
        return Emoji::shortnameToUnicode(trim($this->first_name.' '.$this->last_name));
    }

    /**
     * Создаем превью при сохранении изображений.
     */
    public function registerMediaConversions()
    {
        $quality = (config('vkontakte.images.quality')) ? config('vkontakte.images.quality') : 75;

        if (config('vkontakte.images.sizes.user')) {
            foreach (config('vkontakte.images.sizes.user') as $name => $size) {
                $this->addMediaConversion($name.'_thumb')
                    ->crop('crop-center', $size['width'], $size['height'])
                    ->quality($quality)
                    ->performOnCollections('images');
            }
        }
    }
}
