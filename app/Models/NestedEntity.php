<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;

class NestedEntity extends \Eloquent
{
    use SoftDeletes;

    protected $table = "nested_entities";

    protected $guarded = array("left_range", "right_range");

    const SELECT_ALL_WITH_MINIMUM_INFO = 1;

    const SELECT_SINGLE_PATH_ONLY = 2;

    const SELECT_WITH_DEPTH_INFO = 4;

    const SELECT_LEAVES_ONLY = 8;


    protected function getDateFormat()
    {
        return 'U';
    }


    /**
     * @param        $newEntityName
     * @param int    $referenceEntityId
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function insertIntoAtTheBeginning($newEntityName, $referenceEntityId)
    {
        # Fetch reference entity
        $referenceEntity = \DB::table($this->table)->where('id', $referenceEntityId)->first();
        if (is_null($referenceEntity)) {
            throw new \InvalidArgumentException("Reference entity with id: " . $referenceEntityId . "not found!");
        }

        \DB::transaction(
            function () use ($newEntityName, $referenceEntity) {
                # Create new entity
                $newEntity = \DB::table($this->table);

                # Update ranges in preparation of insertion
                \DB::table($this->table)
                    ->where('right_range', '>', $referenceEntity->left_range)
                    ->update(array('right_range' => \DB::raw('right_range + 2')));
                \DB::table($this->table)
                    ->where('left_range', '>', $referenceEntity->left_range)
                    ->update(array('left_range' => \DB::raw('left_range + 2')));

                # Insert now
                return $newEntity->insert(array('name' => $newEntityName, 'left_range' => $referenceEntity->left_range + 1, 'right_range' => $referenceEntity->left_range + 2, 'created_at' => time()));
            }
        );
    }


    /**
     * @param        $newEntityName
     * @param int    $referenceEntityId
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function insertIntoAtTheEnd($newEntityName, $referenceEntityId)
    {
        # Fetch reference entity
        $referenceEntity = \DB::table($this->table)->where('id', $referenceEntityId)->first();
        if (is_null($referenceEntity)) {
            throw new \InvalidArgumentException("Reference entity with id: " . $referenceEntityId . "not found!");
        }

        \DB::transaction(
            function () use ($newEntityName, $referenceEntity) {
                # Create new entity
                $newEntity = \DB::table($this->table);

                # Update ranges in preparation of insertion
                \DB::table($this->table)
                    ->where('right_range', '>=', $referenceEntity->right_range)
                    ->update(array('right_range' => \DB::raw('right_range + 2')));
                \DB::table($this->table)
                    ->where('left_range', '>', $referenceEntity->right_range)
                    ->update(array('left_range' => \DB::raw('left_range + 2')));

                # Insert now
                return $newEntity->insert(array('name' => $newEntityName, 'left_range' => $referenceEntity->right_range, 'right_range' => $referenceEntity->right_range + 1, 'created_at' => time()));
            }
        );
    }


    /**
     * Alias to insertIntoAtTheEnd()
     *
     * @param        $newEntityName
     * @param int    $referenceEntityId
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function insertInto($newEntityName, $referenceEntityId)
    {
        return $this->insertIntoAtTheEnd($newEntityName, $referenceEntityId);
    }


    /**
     * @param string $newEntityName
     * @param int    $referenceEntityId
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function prependTo($newEntityName, $referenceEntityId)
    {
        # Fetch reference entity
        $referenceEntity = \DB::table($this->table)->where('id', $referenceEntityId)->first();
        if (is_null($referenceEntity)) {
            throw new \InvalidArgumentException("Reference entity with id: " . $referenceEntityId . "not found!");
        }

        \DB::transaction(
            function () use ($newEntityName, $referenceEntity) {
                # Create new entity
                $newEntity = \DB::table($this->table);

                # Update ranges in preparation of insertion
                \DB::table($this->table)
                    ->where('right_range', '>', $referenceEntity->left_range)
                    ->update(array('right_range' => \DB::raw('right_range + 2')));
                \DB::table($this->table)
                    ->where('left_range', '>=', $referenceEntity->left_range)
                    ->update(array('left_range' => \DB::raw('left_range + 2')));

                # Insert now
                return $newEntity->insert(array('name' => $newEntityName, 'left_range' => $referenceEntity->left_range, 'right_range' => $referenceEntity->right_range, 'created_at' => time()));
            }
        );
    }


    /**
     * @param string $newEntityName
     * @param int    $referenceEntityId
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function appendTo($newEntityName, $referenceEntityId)
    {
        # Fetch reference entity
        $referenceEntity = \DB::table($this->table)->where('id', $referenceEntityId)->first();
        if (is_null($referenceEntity)) {
            throw new \InvalidArgumentException("Reference entity with id: " . $referenceEntityId . "not found!");
        }

        \DB::transaction(
            function () use ($newEntityName, $referenceEntity) {
                # Create new entity
                $newEntity = \DB::table($this->table);

                # Update ranges in preparation of insertion
                \DB::table($this->table)
                    ->where('right_range', '>', $referenceEntity->right_range)
                    ->update(array('right_range' => \DB::raw('right_range + 2')));
                \DB::table($this->table)
                    ->where('left_range', '>', $referenceEntity->right_range)
                    ->update(array('left_range' => \DB::raw('left_range + 2')));

                # Insert now
                return $newEntity->insert(array(
                    'name' => $newEntityName,
                    'left_range' => $referenceEntity->right_range + 1,
                    'right_range' => $referenceEntity->right_range + 2,
                    'created_at' => time()
                ));
            }
        );
    }


    public function remove($id, $doSoftDelete = true)
    {
        # Round up delete-ables
        $referenceEntity = \DB::table($this->table)->select('left_range', 'right_range', \DB::raw('right_range - left_range + 1 as range_width'))->where('id', $id)->first();
        if (is_null($referenceEntity)) {
            throw new \InvalidArgumentException("Reference entity with id: " . $id . "not found!");
        }
        $completeListOfEntitiesToDeleteIncludingOrphans = \DB::table($this->table)
            ->where('left_range', '>=', $referenceEntity->left_range)
            ->where('left_range', '<=', $referenceEntity->right_range);

        # Perform either a soft-delete or hard-delete
        return \DB::transaction(
            function () use ($referenceEntity, $doSoftDelete, $completeListOfEntitiesToDeleteIncludingOrphans) {
                if ($doSoftDelete) {
                    # Soft delete
                    $removeResult = $completeListOfEntitiesToDeleteIncludingOrphans->update(array('deleted_at' => time()));
                } else {
                    # Hard delete
                    $removeResult = $completeListOfEntitiesToDeleteIncludingOrphans->delete();

                    # Update ranges
                    \DB::table($this->table)
                        ->where('right_range', '>', $referenceEntity->right_range)
                        ->update(array('right_range' => \DB::raw('right_range - ' . $referenceEntity->range_width)));
                    \DB::table($this->table)
                        ->where('left_range', '>', $referenceEntity->right_range)
                        ->update(array('left_range' => \DB::raw('left_range - ' . $referenceEntity->range_width)));
                }

                return $removeResult;
            }
        );

    }


    /**
     * @param  int    $flag Parameters of Select, which are defined bitwise (see self:SELECT__* constants)
     * @param  string $id   Path information: used only if anything path related is requested.
     *
     * @return array|static[]
     * @throws \InvalidArgumentException
     */
    public function fetch($flag = self::SELECT_ALL_WITH_MINIMUM_INFO, $id = null)
    {
        # Error scenarios
        if ($flag & self::SELECT_ALL_WITH_MINIMUM_INFO && ($flag & self::SELECT_WITH_DEPTH_INFO || $flag & self::SELECT_SINGLE_PATH_ONLY)) {
            throw new \InvalidArgumentException("SELECT_ALL_WITH_MINIMUM_INFO bit isn't compatible with other bits. Use it alone!");
        } elseif ($flag & self::SELECT_SINGLE_PATH_ONLY && empty($id)) {
            throw new \InvalidArgumentException("SELECT_SINGLE_PATH_ONLY requires leaf category ID!");
        } elseif ($flag & self::SELECT_SINGLE_PATH_ONLY && $flag & self::SELECT_WITH_DEPTH_INFO) {
            throw new \InvalidArgumentException("SELECT_SINGLE_PATH_ONLY bit isn't compatible with SELECT_WITH_DEPTH_INFO - their results are mutually restrictive from opposing ends!");
        }

        # Prelim
        empty($id) && $id = 1;
        $nestedEntities = \DB::table($this->table . ' as node')
            ->select('node.id', 'node.name')
            ->leftJoin(
                $this->table . ' as parent',
                function (JoinClause $join) {
                    $join->on('node.left_range', '<=', 'parent.right_range')
                        ->on('node.left_range', '>=', 'parent.left_range');
                });

        # Scenario-1: Select'ing *single path only* with leaf node at the end of that path
        $flag == self::SELECT_SINGLE_PATH_ONLY && $nestedEntities->select('parent.id', 'parent.name')->where('node.id', '=', $id)->orderBy('parent.left_range');

        # Scenario-2: Select'ing *descendents* of provided parent-entity, with the bare minumum
        $flag == self::SELECT_ALL_WITH_MINIMUM_INFO && $nestedEntities->where('parent.id', '=', $id)->orderBy('node.left_range');

        # Scenario-3: Select'ing *everything* with depth information
        $flag == self::SELECT_WITH_DEPTH_INFO && $nestedEntities->addSelect('node.name', \DB::raw('(COUNT(parent.name)-1) as depth'))->groupBy('node.id')->orderBy('node.left_range');

        # Scenario-4: Fetches leaves only
        $flag == self::SELECT_LEAVES_ONLY && $nestedEntities = \DB::table($this->table)->select('id', 'name')->where('right_range', '=', \DB::raw('left_range + 1'))->orderBy('left_range');
        if ($flag == self::SELECT_LEAVES_ONLY && $id !== 1) {
            $parentEntity = \DB::table($this->table)->select('left_range', 'right_range')->where('id', $id)->first();
            $nestedEntities->whereBetween('left_range', array($parentEntity->left_range, $parentEntity->right_range));
        }

        return $nestedEntities->get();
    }
}
