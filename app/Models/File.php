<?php namespace App\Models;

use \Illuminate\Database\Eloquent\SoftDeletes;

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
