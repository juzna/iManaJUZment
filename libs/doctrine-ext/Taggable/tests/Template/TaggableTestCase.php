<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Template_Taggable_TestCase
 *
 * @package     Doctrine
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.2
 * @version     $Revision$
 */
class Doctrine_Template_Taggable_TestCase extends Doctrine_UnitTestCase
{
    public function prepareTables()
    {
        $this->tables[] = 'ArticleTaggableTest';
        $this->tables[] = 'ArticleTaggableTestTaggableTag';
        $this->tables[] = 'ArticleTaggableTest2';
        $this->tables[] = 'ArticleTaggableTest2TaggableTag';
        $this->tables[] = 'TaggableTag';

        parent::prepareTables();
    }

    public function prepareData()
    {
        $test1 = new ArticleTaggableTest();
        $test1->name = 'test1';
        $test1->setTags('tag1, tag2');
        $test1->save();

        $test2 = new ArticleTaggableTest();
        $test2->name = 'test2';
        $test2->setTags('tag1');
        $test2->save();

        $test3 = new ArticleTaggableTest();
        $test3->name = 'test3';
        $test3->setTags('tag1');
        $test3->save();

        $test = new ArticleTaggableTest2();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $test->save();
    }

    public function testArticleTaggableTestHasRelations()
    {
        $test = Doctrine::getTable('ArticleTaggableTest');
        
        $this->assertTrue($test->hasRelation('Tags'));

        $relation = $test->getRelation('Tags');
        $this->assertEqual($relation->getType(), Doctrine_Relation::MANY);
        $this->assertEqual($relation['refTable'], Doctrine::getTable('ArticleTaggableTestTaggableTag'));

        $relation = $test->getRelation('ArticleTaggableTestTaggableTag');
        $this->assertEqual($relation->getType(), Doctrine_Relation::MANY);
    }

    public function testArticleTaggableTestTaggableTagHasRelations()
    {
        $test = Doctrine::getTable('ArticleTaggableTestTaggableTag');

        $this->assertTrue($test->hasRelation('ArticleTaggableTest'));
        $relation = $test->getRelation('ArticleTaggableTest');
        $this->assertEqual($relation->getType(), Doctrine_Relation::ONE);
        $this->assertEqual($relation['onDelete'], 'CASCADE');
        $this->assertEqual($relation['onUpdate'], 'CASCADE');

        $this->assertTrue($test->hasRelation('TaggableTag'));
        $relation = $test->getRelation('TaggableTag');
        $this->assertEqual($relation->getType(), Doctrine_Relation::ONE);
        $this->assertEqual($relation['onDelete'], 'CASCADE');
        $this->assertEqual($relation['onUpdate'], 'CASCADE');
    }

    public function testTaggableTagHasRelations()
    {
        $test = Doctrine::getTable('TaggableTag');

        $this->assertTrue($test->hasRelation('ArticleTaggableTest'));

        $relation = $test->getRelation('ArticleTaggableTest');
        $this->assertEqual($relation->getType(), Doctrine_Relation::MANY);
        $this->assertEqual($relation['refTable'], Doctrine::getTable('ArticleTaggableTestTaggableTag'));

        $relation = $test->getRelation('ArticleTaggableTestTaggableTag');
        $this->assertEqual($relation->getType(), Doctrine_Relation::MANY);
    }

    public function testGetTagIdsFromString()
    {
        $test = new ArticleTaggableTest();
        $tagIds = $test->getTagIds('test1, test2');
        $this->assertEqual(count($tagIds), 2);

        $tags = Doctrine::getTable('TaggableTag')->findByNameOrName('test1', 'test2', Doctrine::HYDRATE_ARRAY);
        $this->assertEqual(array_keys($tags), array('test1', 'test2'));
    }

    public function testTaggableSupportsAddingTags()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test';
        $test->Tags[]->name = 'test';
        $test->Tags[]->name = 'testing';

        $this->assertEqual($test->Tags->count(), 2);
    }

    public function testSetTagsStringHandlesDuplicates()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test';
        $test->setTags('test, ok, testing, ok, ok, testing');
        $this->assertEqual($test->Tags->count(), 3);
    }

    public function testSetTagsStringWithCommaOrSpaces()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test';
        $test->setTags('whatever, blah, ya, ok, jwage');
        $this->assertEqual($test->Tags->count(), 5);
        $test->setTags('whatever blah ya ok jwage');
        $this->assertEqual($test->Tags->count(), 5);
    }

    public function testSetTagStringIndexByName()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test';
        $test->setTags('cool, keys');
        $tags = $test->Tags;
        $this->assertEqual(array_keys($tags->toArray()), array('cool', 'keys'));
    }

    public function testGetRelatedRecords()
    {
        $test1 = Doctrine::getTable('ArticleTaggableTest')->findOneByName('test1');
        $relatedRecords = $test1->getRelatedRecords();
        $this->assertEqual($relatedRecords->count(), 2);
        $this->assertEqual($relatedRecords[0]['name'], 'test2');
        $this->assertEqual($relatedRecords[1]['name'], 'test3');
    }

    public function testAddTags()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $test->addTags('tag3');
        $this->assertEqual($test->Tags->count(), 3);
    }

    public function testRemoveTags()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $test->removeTags('tag2');
        $this->assertEqual($test->Tags->count(), 1);
    }

    public function testAddTagsWithArray()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags(array('tag1', 'tag2'));
        $this->assertEqual($test->Tags->count(), 2);
    }

    public function testSetTagsWithCollection()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $test->removeTags($test->Tags);
        $this->assertEqual($test->Tags->count(), 0);
    }

    public function testSetTagsWithArrayOfIds()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $test->removeTags($test->Tags->getPrimaryKeys());
        $this->assertEqual($test->Tags->count(), 0);
    }

    public function testGetTagNames()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $this->assertEqual($test->getTagNames(), array('tag1', 'tag2'));
    }

    public function testGetTagsString()
    {
        $test = new ArticleTaggableTest();
        $test->name = 'test1';
        $test->setTags('tag1, tag2');
        $this->assertEqual($test->getTagsString(), 'tag1, tag2');
        $this->assertEqual($test->getTagsString('|'), 'tag1|tag2');
    }

    public function testGetPopularTags()
    {
        $test = Doctrine::getTable('TaggableTag')->getPopularTags(array('ArticleTaggableTest', 'ArticleTaggableTest2'));
        $this->assertEqual($test['tag1']['total_num'], 4);
        $this->assertEqual($test['tag1']['num_article_taggable_test'], 3);
        $this->assertEqual($test['tag1']['num_article_taggable_test2'], 1);
    }

    public function testGetPopularTagsArray()
    {
        $test = Doctrine::getTable('TaggableTag')->getPopularTagsArray();
        $this->assertEqual($test, array('tag1' => 4, 'tag2' => 2));
    }

    public function testGetPopularTagsLimit()
    {
        $test = Doctrine::getTable('TaggableTag')->getPopularTagsArray(null, 1);
        $this->assertEqual(count($test), 1);
    }
}

class ArticleTaggableTest extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name', 'string', 255);
    }

    public function setUp()
    {
        $this->actAs('Taggable');
    }
}

class ArticleTaggableTest2 extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name', 'string', 255);
    }

    public function setUp()
    {
        $this->actAs('Taggable');
    }
}