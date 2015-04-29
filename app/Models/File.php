<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\File
 *
 * @property integer $id
 * @property string $hash
 * @property string $mime
 * @property integer $size
 * @property string $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereHash($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereMime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereMetadata($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\File whereDeletedAt($value)
 * @method static \App\Models\File ofType($type = 'image')
 */
class File extends \Eloquent
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * All fields are 'guarded' (protected against mass-assignment)
     *
     * @var array
     */
    protected $guarded = array('*');

    /**
     * Soft-deletes enabled.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Model uses Unix-timestamp date-format.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return 'U';
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string                             $type
     *
     * @return \Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    public function scopeOfType($query, $type = 'image')
    {
        if (!in_array($type, array('plain', 'image', 'audio', 'video', 'application'))) {
            throw new \Exception();
        }

        return $query->where('mime', 'like', $type . '/%');
    }
}
