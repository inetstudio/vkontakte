<?php

namespace InetStudio\Vkontakte\Models;

use Spatie\MediaLibrary\Media;
use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Модель пользователя вконтакте.
 *
 * Class VkontakteUserModel
 *
 * @property int $id
 * @property string $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $nickname
 * @property string $screen_name
 * @property int $has_photo
 * @property string $photo_id
 * @property string $photo_50
 * @property string $photo_100
 * @property string $photo_200
 * @property string $photo_200_orig
 * @property string $photo_400_orig
 * @property string $photo_max
 * @property string $photo_max_orig
 * @property int $followers_count
 * @property int $common_count
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Vkontakte\Models\VkontakteCommentModel[] $comments
 * @property-read mixed $user_full_name
 * @property-read string $user_nickname
 * @property-read string $user_u_r_l
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Vkontakte\Models\VkontaktePostModel[] $posts
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereCommonCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereHasPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhoto100($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhoto200($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhoto200Orig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhoto400Orig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhoto50($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhotoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhotoMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel wherePhotoMaxOrig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereScreenName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontakteUserModel withoutTrashed()
 * @mixin \Eloquent
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
     * Регистрируем преобразования изображений.
     *
     * @param Media|null $media
     *
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $quality = (config('vkontakte.images.quality')) ? config('vkontakte.images.quality') : 75;

        if (config('vkontakte.images.users.conversions')) {
            foreach (config('vkontakte.images.users.conversions') as $collection => $image) {
                foreach ($image as $crop) {
                    foreach ($crop as $conversion) {
                        $imageConversion = $this->addMediaConversion($conversion['name'])->nonQueued();

                        if (isset($conversion['size']['width'])) {
                            $imageConversion->width($conversion['size']['width']);
                        }

                        if (isset($conversion['size']['height'])) {
                            $imageConversion->height($conversion['size']['height']);
                        }

                        if (isset($conversion['fit']['width']) && isset($conversion['fit']['height'])) {
                            $imageConversion->fit('max', $conversion['fit']['width'], $conversion['fit']['height']);
                        }

                        if (isset($conversion['quality'])) {
                            $imageConversion->quality($conversion['quality']);
                            $imageConversion->optimize();
                        } else {
                            $imageConversion->quality($quality);
                        }

                        $imageConversion->performOnCollections($collection);
                    }
                }
            }
        }
    }
}
