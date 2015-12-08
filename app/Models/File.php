<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \App\Models\File
 *
 * @mixin \Eloquent
 * @property string $hash
 * @property string $disk
 * @property string $path
 * @property string $mime
 * @property integer $size
 * @property string $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $uploaders
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereHash($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereDisk($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereMime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereMetadata($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File ofType($type = 'image')
 */
class File extends Model
{
    protected $table = 'files';

    protected $primaryKey = 'hash';

    public $incrementing = false;

    protected $guarded = ['*'];

    protected $dates = ['deleted_at'];

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string                             $type
     *
     * @return \Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    public function scopeOfType($query, $type = 'image')
    {
        if (!in_array($type, ['plain', 'image', 'audio', 'video', 'application'])) {
            throw new \Exception();
        }

        return $query->where('mime', 'like', $type . '/%');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function uploaders()
    {
        return $this->belongsToMany(User::class, 'files_users', 'file_hash', 'user_id')->withTimestamps()->withPivot(['uuid', 'original_client_name']);
    }
}
