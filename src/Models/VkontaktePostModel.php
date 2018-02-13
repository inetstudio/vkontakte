<?php

namespace InetStudio\Vkontakte\Models;

use Spatie\MediaLibrary\Media;
use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Exceptions\InvalidManipulation;
use InetStudio\Vkontakte\Models\Attachments\LinkModel;
use InetStudio\Vkontakte\Models\Attachments\PhotoModel;
use InetStudio\Vkontakte\Models\Attachments\VideoModel;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Модель поста вконтакте.
 *
 * Class VkontaktePostModel
 *
 * @property int $id
 * @property string $post_id
 * @property string $from_id
 * @property string $owner_id
 * @property string $post_source
 * @property string $post_type
 * @property string $text
 * @property \Illuminate\Database\Eloquent\Collection|\InetStudio\Vkontakte\Models\VkontakteCommentModel[] $comments
 * @property int $likes
 * @property int $reposts
 * @property int $views
 * @property \Carbon\Carbon|null $date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read mixed $caption
 * @property-read string $post_url
 * @property-read string $social_name
 * @property-read string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \InetStudio\Vkontakte\Models\VkontakteUserModel $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel wherePostSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereReposts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel whereViews($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Vkontakte\Models\VkontaktePostModel withoutTrashed()
 * @mixin \Eloquent
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * Отношение "один ко многим" с моделью вложений ссылок.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(LinkModel::class, 'post_id', 'id');
    }

    /**
     * Отношение "один ко многим" с моделью вложений фото.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(PhotoModel::class, 'post_id', 'id');
    }

    /**
     * Отношение "один ко многим" с моделью вложений видео.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos()
    {
        return $this->hasMany(VideoModel::class, 'post_id', 'id');
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
     *
     * @return string
     */
    public function getCaptionAttribute()
    {
        return Emoji::shortnameToUnicode($this->text);
    }

    /**
     * Получаем имя социальной сети.
     *
     * @return string
     */
    public function getSocialNameAttribute()
    {
        return $this::NETWORK;
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

        if (config('vkontakte.images.posts.conversions')) {
            foreach (config('vkontakte.images.posts.conversions') as $collection => $image) {
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
