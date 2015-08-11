<?php namespace App\Tests\Models;

use App\Models\NestedEntity;
use App\Tests\IlluminateTestCase;

class NestedEntityModelTest extends IlluminateTestCase
{
    public static function setUpBeforeClass()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        \Artisan::call('db:seed', ['--class' => 'NestedEntitiesTableSeeder']);
    }

    public function data_testInsertForException()
    {
        return [
            [
                ['method' => 'insertIntoAtTheBeginning'],
                ['method' => 'insertIntoAtTheEnd'],
                ['method' => 'prependTo'],
                ['method' => 'appendTo']
            ]
        ];
    }

    /**
     * @dataProvider data_testInsertForException
     *
     * @param $data
     */
    public function testInsertForException($data)
    {
        $this->setExpectedException('\InvalidArgumentException');

        $nestedEntitiesModel = new NestedEntity();
        $nestedEntitiesModel->{$data['method']}('Exception case', 100);
    }

    public function testInsert()
    {
        $nestedEntitiesModel = new NestedEntity();

        //------------------------------------------------------
        // Case 1: Insert an entity into Root and test ranges
        //------------------------------------------------------

        $nestedEntitiesModel->insertInto('Insert At The Beginning - 2', 1);

        /** @var NestedEntity $_rootObject */
        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(4, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectOne */
        $_insertedObjectOne = $nestedEntitiesModel->find(2);
        $this->assertEquals(2, $_insertedObjectOne->left_range);
        $this->assertEquals(3, $_insertedObjectOne->right_range);

        //----------------------------------------------------------------------------
        // Case 2: insertIntoAtTheBeginning a new entity into Root and test ranges
        //----------------------------------------------------------------------------

        $nestedEntitiesModel->insertIntoAtTheBeginning('Insert At The Beginning - 1', 1);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(6, $_rootObject->right_range);

        $_insertedObjectOne = $nestedEntitiesModel->find(2);
        $this->assertEquals(4, $_insertedObjectOne->left_range);
        $this->assertEquals(5, $_insertedObjectOne->right_range);

        /** @var NestedEntity $_insertedObjectTwo */
        $_insertedObjectTwo = $nestedEntitiesModel->find(3);
        $this->assertEquals(2, $_insertedObjectTwo->left_range);
        $this->assertEquals(3, $_insertedObjectTwo->right_range);

        //----------------------------------------------------------------------
        // Case 3: insertIntoAtTheEnd a new entity into Root and test ranges
        //----------------------------------------------------------------------

        $nestedEntitiesModel->insertIntoAtTheEnd('Insert At The End - 1', 1);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(8, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectThree */
        $_insertedObjectThree = $nestedEntitiesModel->find(4);
        $this->assertEquals(6, $_insertedObjectThree->left_range);
        $this->assertEquals(7, $_insertedObjectThree->right_range);

        //-------------------------------------------------------------------------------
        // Case 4: insertIntoAtTheEnd a new entity into a child node and test ranges
        //-------------------------------------------------------------------------------

        $nestedEntitiesModel->insertIntoAtTheEnd('Insert At The End - 2', 4);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(10, $_rootObject->right_range);

        $_insertedObjectThree = $nestedEntitiesModel->find(4);
        $this->assertEquals(6, $_insertedObjectThree->left_range);
        $this->assertEquals(9, $_insertedObjectThree->right_range);

        /** @var NestedEntity $_insertedObjectFour */
        $_insertedObjectFour = $nestedEntitiesModel->find(5);
        $this->assertEquals(7, $_insertedObjectFour->left_range);
        $this->assertEquals(8, $_insertedObjectFour->right_range);

        $nestedEntitiesModel->insertIntoAtTheEnd('Insert At The End - 3', 2);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(12, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectFive */
        $_insertedObjectFive = $nestedEntitiesModel->find(6);
        $this->assertEquals(5, $_insertedObjectFive->left_range);
        $this->assertEquals(6, $_insertedObjectFive->right_range);

        $_insertedObjectOne = $nestedEntitiesModel->find(2);
        $this->assertEquals(4, $_insertedObjectOne->left_range);
        $this->assertEquals(7, $_insertedObjectOne->right_range);

        $_insertedObjectThree = $nestedEntitiesModel->find(4);
        $this->assertEquals(8, $_insertedObjectThree->left_range);
        $this->assertEquals(11, $_insertedObjectThree->right_range);

        //------------------------------------------------------------------------------------
        // Case 5: insertIntoAtTheBeginning a new entity into a child node and test ranges
        //------------------------------------------------------------------------------------

        $nestedEntitiesModel->insertIntoAtTheBeginning('Insert At The Beginning - 3', 3);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(14, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectSix */
        $_insertedObjectSix = $nestedEntitiesModel->find(7);
        $this->assertEquals(3, $_insertedObjectSix->left_range);
        $this->assertEquals(4, $_insertedObjectSix->right_range);

        $_insertedObjectOne = $nestedEntitiesModel->find(2);
        $this->assertEquals(6, $_insertedObjectOne->left_range);
        $this->assertEquals(9, $_insertedObjectOne->right_range);

        $nestedEntitiesModel->insertIntoAtTheBeginning('Insert At The Beginning - 4', 3);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(16, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectSeven */
        $_insertedObjectSeven = $nestedEntitiesModel->find(8);
        $this->assertEquals(3, $_insertedObjectSeven->left_range);
        $this->assertEquals(4, $_insertedObjectSeven->right_range);

        $_insertedObjectSix = $nestedEntitiesModel->find(7);
        $this->assertEquals(5, $_insertedObjectSix->left_range);
        $this->assertEquals(6, $_insertedObjectSix->right_range);

        $_insertedObjectTwo = $nestedEntitiesModel->find(3);
        $this->assertEquals(2, $_insertedObjectTwo->left_range);
        $this->assertEquals(7, $_insertedObjectTwo->right_range);

        //---------------------
        // Case 6: Append to
        //---------------------

        $nestedEntitiesModel->appendTo('Append to - 1', 8);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(18, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectSeven */
        $_insertedObjectSeven = $nestedEntitiesModel->find(8);
        $this->assertEquals(3, $_insertedObjectSeven->left_range);
        $this->assertEquals(4, $_insertedObjectSeven->right_range);

        /** @var NestedEntity $_insertedObjectEight */
        $_insertedObjectEight = $nestedEntitiesModel->find(9);
        $this->assertEquals(5, $_insertedObjectEight->left_range);
        $this->assertEquals(6, $_insertedObjectEight->right_range);

        //----------------------
        // Case 6: Prepend to
        //----------------------

        $nestedEntitiesModel->prependTo('Prepend to - 1', 8);

        $_rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $_rootObject->left_range);
        $this->assertEquals(20, $_rootObject->right_range);

        /** @var NestedEntity $_insertedObjectSeven */
        $_insertedObjectSeven = $nestedEntitiesModel->find(8);
        $this->assertEquals(5, $_insertedObjectSeven->left_range);
        $this->assertEquals(6, $_insertedObjectSeven->right_range);

        /** @var NestedEntity $_insertedObjectEight */
        $_insertedObjectEight = $nestedEntitiesModel->find(9);
        $this->assertEquals(7, $_insertedObjectEight->left_range);
        $this->assertEquals(8, $_insertedObjectEight->right_range);

        /** @var NestedEntity $_insertedObjectNine */
        $_insertedObjectNine = $nestedEntitiesModel->find(10);
        $this->assertEquals(3, $_insertedObjectNine->left_range);
        $this->assertEquals(4, $_insertedObjectNine->right_range);
    }

    public function data_testFetchForException()
    {
        return [
            [
                ['flag' => 5, 'id' => 100],
                ['flag' => 3, 'id' => 100],
                ['flag' => 2, 'id' => null],
                ['flag' => 6, 'id' => 100]
            ]
        ];
    }

    /**
     * @dataProvider data_testFetchForException
     *
     * @param $data
     */
    public function testFetchForException($data)
    {
        $this->setExpectedException('\InvalidArgumentException');

        $nestedEntitiesModel = new NestedEntity();
        $nestedEntitiesModel->fetch($data['flag'], $data['id']);
    }

    public function testFetch()
    {
        //----------------------------------------------------
        // Case 1: Fetch with SELECT_ALL_WITH_MINIMUM_INFO
        //----------------------------------------------------

        $nestedEntitiesModel = new NestedEntity();
        $fetchWithMinimumInfoContent = $nestedEntitiesModel->fetch();
        $this->assertEquals(1, $fetchWithMinimumInfoContent[0]->id);
        $this->assertEquals('Root', $fetchWithMinimumInfoContent[0]->name);
        $this->assertEquals(3, $fetchWithMinimumInfoContent[1]->id);
        $this->assertEquals('Insert At The Beginning - 1', $fetchWithMinimumInfoContent[1]->name);
        $this->assertEquals(10, $fetchWithMinimumInfoContent[2]->id);
        $this->assertEquals('Prepend to - 1', $fetchWithMinimumInfoContent[2]->name);
        $this->assertEquals(8, $fetchWithMinimumInfoContent[3]->id);
        $this->assertEquals('Insert At The Beginning - 4', $fetchWithMinimumInfoContent[3]->name);
        $this->assertEquals(9, $fetchWithMinimumInfoContent[4]->id);
        $this->assertEquals('Append to - 1', $fetchWithMinimumInfoContent[4]->name);
        $this->assertEquals(7, $fetchWithMinimumInfoContent[5]->id);
        $this->assertEquals('Insert At The Beginning - 3', $fetchWithMinimumInfoContent[5]->name);
        $this->assertEquals(2, $fetchWithMinimumInfoContent[6]->id);
        $this->assertEquals('Insert At The Beginning - 2', $fetchWithMinimumInfoContent[6]->name);
        $this->assertEquals(6, $fetchWithMinimumInfoContent[7]->id);
        $this->assertEquals('Insert At The End - 3', $fetchWithMinimumInfoContent[7]->name);
        $this->assertEquals(4, $fetchWithMinimumInfoContent[8]->id);
        $this->assertEquals('Insert At The End - 1', $fetchWithMinimumInfoContent[8]->name);
        $this->assertEquals(5, $fetchWithMinimumInfoContent[9]->id);
        $this->assertEquals('Insert At The End - 2', $fetchWithMinimumInfoContent[9]->name);
        unset($nestedEntitiesModel);

        //----------------------------------------------
        // Case 2: Fetch with SELECT_WITH_DEPTH_INFO
        //----------------------------------------------

        $nestedEntitiesModel = new NestedEntity();
        $fetchWithDepthInfo = $nestedEntitiesModel->fetch(NestedEntity::SELECT_WITH_DEPTH_INFO);
        $this->assertEquals(1, $fetchWithDepthInfo[0]->id);
        $this->assertEquals(0, $fetchWithDepthInfo[0]->depth);
        $this->assertEquals(3, $fetchWithDepthInfo[1]->id);
        $this->assertEquals(1, $fetchWithDepthInfo[1]->depth);
        $this->assertEquals(10, $fetchWithDepthInfo[2]->id);
        $this->assertEquals(2, $fetchWithDepthInfo[2]->depth);
        $this->assertEquals(8, $fetchWithDepthInfo[3]->id);
        $this->assertEquals(2, $fetchWithDepthInfo[3]->depth);
        $this->assertEquals(9, $fetchWithDepthInfo[4]->id);
        $this->assertEquals(2, $fetchWithDepthInfo[4]->depth);
        $this->assertEquals(7, $fetchWithDepthInfo[5]->id);
        $this->assertEquals(2, $fetchWithDepthInfo[5]->depth);
        $this->assertEquals(2, $fetchWithDepthInfo[6]->id);
        $this->assertEquals(1, $fetchWithDepthInfo[6]->depth);
        $this->assertEquals(6, $fetchWithDepthInfo[7]->id);
        $this->assertEquals(2, $fetchWithDepthInfo[7]->depth);
        $this->assertEquals(4, $fetchWithDepthInfo[8]->id);
        $this->assertEquals(1, $fetchWithDepthInfo[8]->depth);
        $this->assertEquals(5, $fetchWithDepthInfo[9]->id);
        $this->assertEquals(2, $fetchWithDepthInfo[9]->depth);
        unset($nestedEntitiesModel);

        //----------------------------------------------
        // Case 3: Fetch with SELECT_SINGLE_PATH_ONLY
        //----------------------------------------------

        $nestedEntitiesModel = new NestedEntity();
        $fetchSinglePathOnly = $nestedEntitiesModel->fetch(NestedEntity::SELECT_SINGLE_PATH_ONLY, 7);
        $this->assertCount(3, $fetchSinglePathOnly);
        $this->assertEquals(3, $fetchSinglePathOnly[1]->id);
        $this->assertEquals('Insert At The Beginning - 1', $fetchSinglePathOnly[1]->name);
        unset($nestedEntitiesModel);

        //----------------------------------------------
        // Case 4: Fetch with SELECT_LEAVES_ONLY
        //----------------------------------------------

        $nestedEntitiesModel = new NestedEntity();
        $fetchLeavesOnly = $nestedEntitiesModel->fetch(NestedEntity::SELECT_LEAVES_ONLY);
        $this->assertCount(6, $fetchLeavesOnly);
        $this->assertEquals(10, $fetchLeavesOnly[0]->id);
        $this->assertEquals(8, $fetchLeavesOnly[1]->id);
        $this->assertEquals(9, $fetchLeavesOnly[2]->id);
        $this->assertEquals(7, $fetchLeavesOnly[3]->id);
        $this->assertEquals(6, $fetchLeavesOnly[4]->id);
        $this->assertEquals(5, $fetchLeavesOnly[5]->id);
        unset($nestedEntitiesModel);

        $nestedEntitiesModel = new NestedEntity();
        $fetchLeavesOnlyWithinDefiniteRange = $nestedEntitiesModel->fetch(NestedEntity::SELECT_LEAVES_ONLY, 3);
        $this->assertCount(4, $fetchLeavesOnlyWithinDefiniteRange);
        $this->assertEquals(10, $fetchLeavesOnlyWithinDefiniteRange[0]->id);
        $this->assertEquals(8, $fetchLeavesOnlyWithinDefiniteRange[1]->id);
        $this->assertEquals(9, $fetchLeavesOnlyWithinDefiniteRange[2]->id);
        $this->assertEquals(7, $fetchLeavesOnlyWithinDefiniteRange[3]->id);
        unset($nestedEntitiesModel);
    }

    public function testRemoveForException()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $nestedEntitiesModel = new NestedEntity();
        $nestedEntitiesModel->remove(100);
    }

    public function testRemove()
    {
        $nestedEntitiesModel = new NestedEntity();
        $_timeNow = time();

        //------------------------------------------
        // Case 1: Soft-delete of a leaf entity
        //------------------------------------------

        $nestedEntitiesModel->remove(7);
        $softDeletedObject = $nestedEntitiesModel->withTrashed()->find(7);
        $this->assertNotNull($softDeletedObject->deleted_at);
        $this->assertTrue($softDeletedObject->deleted_at >= $_timeNow);

        //------------------------------------------
        // Case 2: Hard-delete of a leaf entity
        //------------------------------------------

        $nestedEntitiesModel->remove(7, false);
        /** @var NestedEntity $hardDeletedObject */
        $hardDeletedObject = $nestedEntitiesModel->withTrashed()->find(7);
        $this->assertNull($hardDeletedObject);
        /** @var NestedEntity $parentOfDeletedObject */
        $parentOfDeletedObject = $nestedEntitiesModel->find(3);
        $this->assertEquals(2, $parentOfDeletedObject->left_range);
        $this->assertEquals(9, $parentOfDeletedObject->right_range);
        /** @var NestedEntity $rightSiblingOfDeletedObject */
        $rightSiblingOfDeletedObject = $nestedEntitiesModel->find(2);
        $this->assertEquals(10, $rightSiblingOfDeletedObject->left_range);
        $this->assertEquals(13, $rightSiblingOfDeletedObject->right_range);
        /** @var NestedEntity $rootObject */
        $rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $rootObject->left_range);
        $this->assertEquals(18, $rootObject->right_range);

        //------------------------------------------
        // Case 3: Soft-delete of a parent entity
        //------------------------------------------

        $nestedEntitiesModel->remove(2);
        /** @var NestedEntity $softDeletedObjectOne */
        $softDeletedObjectOne = $nestedEntitiesModel->withTrashed()->find(2);
        /** @var NestedEntity $softDeletedObjectTwo */
        $softDeletedObjectTwo = $nestedEntitiesModel->withTrashed()->find(6);
        $this->assertNotNull($softDeletedObjectOne->deleted_at);
        $this->assertNotNull($softDeletedObjectTwo->deleted_at);
        $this->assertTrue($softDeletedObjectOne->deleted_at >= $_timeNow);
        $this->assertTrue($softDeletedObjectTwo->deleted_at >= $_timeNow);

        //------------------------------------------
        // Case 4: Hard-delete of a parent entity
        //------------------------------------------

        $nestedEntitiesModel->remove(2, false);
        $hardDeletedObject = $nestedEntitiesModel->withTrashed()->find(2);
        $this->assertNull($hardDeletedObject);
        $rightSiblingOfDeletedObject = $nestedEntitiesModel->find(4);
        $this->assertEquals(10, $rightSiblingOfDeletedObject->left_range);
        $this->assertEquals(13, $rightSiblingOfDeletedObject->right_range);
        $rootObject = $nestedEntitiesModel->find(1);
        $this->assertEquals(1, $rootObject->left_range);
        $this->assertEquals(14, $rootObject->right_range);
    }
}
