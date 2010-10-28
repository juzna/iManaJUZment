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
 * Doctrine_Template_EventLoggable_TestCase
 *
 * @package     Doctrine
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.2
 * @version     $Revision$
 */
class Doctrine_Template_EventLoggable_TestCase extends Doctrine_UnitTestCase
{
    public function prepareTables()
    {
        $this->tables[] = "EventLoggableTest";
        parent::prepareTables();
    }

    public function testEventLoggableTranslatesEnglish()
    {
        $test = new EventLoggableTest();
        $test->name = 'Testing this out';
        $test->save();
        $test->name = 'ok';
        $test->save();
        $test->delete();

        $test = Doctrine::getTable('EventLoggableTest')
            ->createQuery('t')
            ->execute();

        $this->assertTrue(file_exists(dirname(__FILE__).'/test.log'));
        $logs = file_get_contents(dirname(__FILE__).'/test.log');

        $this->assertTrue(strstr($logs, 'preDelete'));
        $this->assertTrue(strstr($logs, 'preQuery'));

        $this->assertFalse(strstr($logs, 'postDelete'));
        $this->assertFalse(strstr($logs, 'commit'));
        $this->assertFalse(strstr($logs, 'postPrepare'));

        unlink(dirname(__FILE__).'/test.log');
    }
}

class EventLoggableTest extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('name', 'string', 255);
    }

    public function setUp()
    {
        $this->actAs('EventLoggable', array(
            'events' => array('preDelete', 'preQuery'),
            'logger' => array(
                'type' => 'file',
                'path' => dirname(__FILE__) . '/test.log'
            )
        ));
    }
}