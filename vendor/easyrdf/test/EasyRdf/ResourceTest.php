<?php

/**
 * EasyRdf
 *
 * LICENSE
 *
 * Copyright (c) 2009-2010 Nicholas J Humfrey.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. The name of the author 'Nicholas J Humfrey" may be used to endorse or
 *    promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    EasyRdf
 * @copyright  Copyright (c) 2009-2010 Nicholas J Humfrey
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id$
 */

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'TestHelper.php';

class EasyRdf_ResourceTest extends EasyRdf_TestCase
{
    protected $_resource = null;

    /**
     * Set up the test suite before each test
     */
    public function setUp()
    {
        // NOTE: this is not the right way to create resources
        // but this is just for testing...
        $this->_type = new EasyRdf_Resource('http://xmlns.com/foaf/0.1/Person');
        $this->_resource = new EasyRdf_Resource('http://example.com/#me');
        $this->_resource->set('rdf:type', $this->_type);
        $this->_resource->add('rdf:test', 'Test A');
        $this->_resource->add('rdf:test', new EasyRdf_Literal('Test B', 'en'));
    }

    public function testConstructNull()
    {
        $this->setExpectedException('InvalidArgumentException');
        $res = new EasyRdf_Resource(null);
    }

    public function testConstructEmpty()
    {
        $this->setExpectedException('InvalidArgumentException');
        $res = new EasyRdf_Resource('');
    }

    public function testConstructNonString()
    {
        $this->setExpectedException('InvalidArgumentException');
        $res = new EasyRdf_Resource(array());
    }

    public function testGetUri()
    {
        $this->assertEquals(
            'http://example.com/#me',
            $this->_resource->getUri()
        );
    }

    public function testGet()
    {
        $this->assertStringEquals(
            'Test A',
            $this->_resource->get('rdf:test')
        );
    }

    public function testGetWithUri()
    {
        $this->assertStringEquals(
            'Test A',
            $this->_resource->get(
                'http://www.w3.org/1999/02/22-rdf-syntax-ns#test'
            )
        );
    }

    public function testGetWithLanguage()
    {
        $this->assertStringEquals(
            'Test B',
            $this->_resource->get('rdf:test', 'en')
        );
    }

    public function testGetInverse()
    {
        $homepage = new EasyRdf_Resource('http://example.com/');
        $this->_resource->add('foaf:homepage', $homepage);
        $this->assertEquals($this->_resource, $homepage->get('-foaf:homepage'));
    }

    public function testGetArray()
    {
        $this->assertStringEquals(
            'Test A',
            $this->_resource->get(array('rdf:test', 'rdf:foobar'))
        );
    }

    public function testGetArray2()
    {
        $this->assertStringEquals(
            'Test A',
            $this->_resource->get(array('rdf:foobar', 'rdf:test'))
        );
    }

    public function testGetArrayWithLang()
    {
        $this->assertStringEquals(
            'Test B',
            $this->_resource->get(array('rdf:foobar', 'rdf:test'), 'en')
        );
    }

    public function testGetEmptyArray()
    {
        $this->assertEquals(
            null,
            $this->_resource->get(array())
        );
    }

    public function testGetNonExistantProperty()
    {
        $this->assertNull($this->_resource->get('foo:bar'));
    }

    public function testGetNullKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->get(null);
    }

    public function testGetEmptyKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->get('');
    }

    public function testGetNonStringKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->get($this);
    }

    public function testAll()
    {
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(2, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
    }

    public function testAllWithUri()
    {
        $all = $this->_resource->all(
            'http://www.w3.org/1999/02/22-rdf-syntax-ns#test'
        );
        $this->assertEquals(2, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
    }

    public function testAllWithLang()
    {
        $all = $this->_resource->all('rdf:test', 'en');
        $this->assertEquals(1, count($all));
        $this->assertStringEquals('Test B', $all[0]);
    }

    public function testAllInverse()
    {
        $all = $this->_type->all('-rdf:type');
        $this->assertEquals(1, count($all));
        $this->assertEquals($this->_resource, $all[0]);
    }

    public function testAllNonExistantProperty()
    {
        $this->assertEquals(
            array(),
            $this->_resource->all('foo:bar')
        );
    }

    public function testAllNullKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->all(null);
    }

    public function testAllEmptyKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->all('');
    }

    public function testAllNonStringKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->all(array());
    }

    public function testSet()
    {
        $this->_resource->set('rdf:test', 'Test C');
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(1, count($all));
        $this->assertStringEquals('Test C', $all[0]);
    }

    public function testSetWithUri()
    {
        $this->_resource->set(
            'http://www.w3.org/1999/02/22-rdf-syntax-ns#test',
            'Test C'
        );
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(1, count($all));
        $this->assertStringEquals('Test C', $all[0]);
    }

    public function testSetInverse()
    {
        $homepage1 = new EasyRdf_Resource('http://example.com/1');
        $homepage2 = new EasyRdf_Resource('http://example.com/2');
        $this->_resource->set('foaf:homepage', $homepage1);
        $this->assertEquals(
            $this->_resource,
            $homepage1->get('-foaf:homepage')
        );
        $this->assertEquals(
            null,
            $homepage2->get('-foaf:homepage')
        );

        $this->_resource->set('foaf:homepage', $homepage2);
        $this->assertEquals(
            null,
            $homepage1->get('-foaf:homepage')
        );
        $this->assertEquals(
            $this->_resource,
            $homepage2->get('-foaf:homepage')
        );
    }

    public function testSetNullKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->set(null, 'Test C');
    }

    public function testSetEmptyKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->set('', 'Test C');
    }

    public function testSetNonStringKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->set(array(), 'Test C');
    }

    public function testSetNull()
    {
        $this->_resource->set('rdf:test', null);
        $this->assertEquals(
            array(),
            $this->_resource->all('rdf:test')
        );
    }

    public function testAdd()
    {
        $this->_resource->add('rdf:test', 'Test C');
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(3, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
        $this->assertStringEquals('Test C', $all[2]);
    }

    public function testAddWithUri()
    {
        $this->_resource->add(
            'http://www.w3.org/1999/02/22-rdf-syntax-ns#test',
            'Test C'
        );
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(3, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
        $this->assertStringEquals('Test C', $all[2]);
    }

    public function testAddMultipleValues()
    {
        $this->_resource->add('rdf:test', array('Test C', 'Test D'));
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(4, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
        $this->assertStringEquals('Test C', $all[2]);
        $this->assertStringEquals('Test D', $all[3]);
    }

    public function testAddMultipleProperties()
    {
        $this->_resource->add(array('rdf:test1', 'rdf:test2'), 'Test');

        $all = $this->_resource->all('rdf:test1');
        $this->assertEquals(1, count($all));
        $this->assertStringEquals('Test', $all[0]);

        $all = $this->_resource->all('rdf:test2');
        $this->assertEquals(1, count($all));
        $this->assertStringEquals('Test', $all[0]);
    }

    public function testAddAssociateProperties()
    {
        $this->_resource->add(array('rdf:test' => 'Test C'));
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(3, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
        $this->assertStringEquals('Test C', $all[2]);
    }

    public function testAddLiteralMultipleTimes()
    {
        $this->_resource->set('rdf:test', 'foobar');
        $this->_resource->add('rdf:test', 'foobar');
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(1, count($all));
        $this->assertStringEquals('foobar', $all[0]);
    }

    public function testAddLiteralDifferentLanguages()
    {
        $this->_resource->set('rdf:test', new EasyRdf_Literal('foobar', 'en'));
        $this->_resource->add('rdf:test', new EasyRdf_Literal('foobar', 'fr'));
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(2, count($all));
        $this->assertStringEquals('foobar', $all[0]);
        $this->assertStringEquals('foobar', $all[1]);
    }

    public function testAddNull()
    {
        $this->_resource->add('rdf:test', null);
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(2, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
    }

    public function testAddNullKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->add(null, 'Test C');
    }

    public function testAddEmptyKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->add('', 'Test C');
    }

    public function testAddNonStringKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->add(array(), 'Test C');
    }

    public function testDelete()
    {
        $this->assertStringEquals('Test A', $this->_resource->get('rdf:test'));
        $this->_resource->delete('rdf:test');
        $this->assertEquals(array(), $this->_resource->all('rdf:test'));
    }

    public function testDeleteWithUri()
    {
        $this->assertStringEquals('Test A', $this->_resource->get('rdf:test'));
        $this->_resource->delete(
            'http://www.w3.org/1999/02/22-rdf-syntax-ns#test'
        );
        $this->assertEquals(array(), $this->_resource->all('rdf:test'));
    }

    public function testDeleteNullKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->delete(null);
    }

    public function testDeleteEmptyKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->delete('');
    }

    public function testDeleteNonStringKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->delete(array());
    }

    public function testDeleteValue()
    {
        $testa = $this->_resource->get('rdf:test');
        $this->_resource->delete('rdf:test', $testa);
        $all = $this->_resource->all('rdf:test');
        $this->assertEquals(1, count($all));
    }

    public function testJoinDefaultGlue()
    {
        $this->assertEquals(
            'Test A Test B',
            $this->_resource->join('rdf:test')
        );
    }

    public function testJoinWithUri()
    {
        $this->assertEquals(
            'Test A Test B',
            $this->_resource->join(
                'http://www.w3.org/1999/02/22-rdf-syntax-ns#test'
            )
        );
    }

    public function testJoinWithLang()
    {
        $this->assertEquals(
            'Test B',
            $this->_resource->join('rdf:test', ' ', 'en')
        );
    }

    public function testJoinNonExistantProperty()
    {
        $this->assertEquals('', $this->_resource->join('foo:bar'));
    }

    public function testJoinCustomGlue()
    {
        $this->assertEquals(
            'Test A:Test B',
            $this->_resource->join('rdf:test', ':')
        );
    }

    public function testJoinNullKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->join(null, 'Test C');
    }

    public function testJoinEmptyKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->join('', 'Test C');
    }

    public function testJoinNonStringKey()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_resource->join(array(), 'Test C');
    }

    public function testIsBnode()
    {
        $bnode = new EasyRdf_Resource('_:foobar');
        $this->assertEquals(true, $bnode->isBnode());
    }

    public function testIsNotBnode()
    {
        $this->assertEquals(false, $this->_resource->isBnode());
    }

    public function testGetNodeId()
    {
        $bnode = new EasyRdf_Resource('_:foobar');
        $this->assertEquals('foobar', $bnode->getNodeId());
    }

    public function testInvalidGetNodeId()
    {
        $this->assertEquals(null, $this->_resource->getNodeId());
    }

    public function testProperties()
    {
        $this->assertEquals(
            array('rdf:type', 'rdf:test'),
            $this->_resource->properties()
        );
    }

    public function testPropertyUris()
    {
        $this->assertEquals(
            array(
                'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
                'http://www.w3.org/1999/02/22-rdf-syntax-ns#test'
            ),
            $this->_resource->propertyUris()
        );
    }

    public function testMatchesLiteralValue()
    {
        $this->_resource->set('rdf:test', 'foobar');
        $this->assertTrue(
            $this->_resource->matches('rdf:test', 'foobar')
        );
    }

    public function testMatchesDifferentLiteralObjects()
    {
        $lit1 = new EasyRdf_Literal('foobar');
        $lit2 = new EasyRdf_Literal('foobar');
        $this->_resource->set('rdf:test', $lit1);
        $this->assertTrue(
            $this->_resource->matches('rdf:test', $lit2)
        );
    }

    public function testNotMatchesDifferentLanguages()
    {
        $lit1 = new EasyRdf_Literal('foobar', 'en');
        $lit2 = new EasyRdf_Literal('foobar', 'fr');
        $this->_resource->set('rdf:test', $lit1);
        $this->assertFalse(
            $this->_resource->matches('rdf:test', $lit2)
        );
    }

    public function testNotMatchesDifferentDatatypes()
    {
        $lit1 = new EasyRdf_Literal('1');
        $lit2 = new EasyRdf_Literal(1);
        $this->_resource->set('rdf:test', $lit1);
        $this->assertFalse(
            $this->_resource->matches('rdf:test', $lit2)
        );
    }
    
    public function testMatchesSameResourceObject()
    {
        $res = new EasyRdf_Resource('http://example.co/foo');
        $this->_resource->set('rdf:test', $res);
        $this->assertTrue(
            $this->_resource->matches('rdf:test', $res)
        );
    }
    
    public function testNotMatchesDifferentResourceObjects()
    {
        $res1 = new EasyRdf_Resource('http://example.co/foo');
        $res2 = new EasyRdf_Resource('http://example.co/foo');
        $this->_resource->set('rdf:test', $res1);
        $this->assertFalse(
            $this->_resource->matches('rdf:test', $res2)
        );
    }

    public function testNotMatches()
    {
        $this->assertFalse(
            $this->_resource->matches('rdf:test', 'Test C')
        );
    }

    public function testHas()
    {
        $this->assertTrue(
            $this->_resource->has('rdf:test')
        );
    }

    public function testNDoesNotHave()
    {
        $this->assertFalse(
            $this->_resource->has('test:noprop')
        );
    }

    public function testTypes()
    {
        $types = $this->_resource->types();
        $this->assertEquals(1, count($types));
        $this->assertStringEquals('foaf:Person', $types[0]);
    }

    public function testType()
    {
        $this->assertStringEquals('foaf:Person', $this->_resource->type());
    }

    public function testTypeResource()
    {
        $this->assertEquals(
            $this->_type,
            $this->_resource->typeResource()
        );
    }

    public function testIsA()
    {
        $this->assertTrue($this->_resource->is_a('foaf:Person'));
    }

    public function testIsAFullUri()
    {
        $this->assertTrue(
            $this->_resource->is_a('http://xmlns.com/foaf/0.1/Person')
        );
    }

    public function testIsntA()
    {
        $this->assertFalse($this->_resource->is_a('foaf:Rat'));
    }

    public function testPrimaryTopic()
    {
        $doc = new EasyRdf_Resource('http://example.com/foaf.rdf');
        $person = new EasyRdf_Resource('http://example.com/foaf.rdf#me');
        $doc->add('foaf:primaryTopic', $person);
        $this->assertEquals(
            'http://example.com/foaf.rdf#me',
            $doc->primaryTopic()->getUri()
        );
    }

    public function testIsPrimaryTopicOf()
    {
        $doc = new EasyRdf_Resource('http://example.com/foaf.rdf');
        $person = new EasyRdf_Resource('http://example.com/foaf.rdf#me');
        $person->add('foaf:isPrimaryTopicOf', $doc);
        $this->assertEquals(
            'http://example.com/foaf.rdf#me',
            $doc->primaryTopic()->getUri()
        );
    }

    public function testPrefix()
    {
        $foafName = new EasyRdf_Resource('http://xmlns.com/foaf/0.1/name');
        $this->assertStringEquals('foaf', $foafName->prefix());
    }

    public function testUnknownPrefix()
    {
        $unknown = new EasyRdf_Resource('http://example.com/foo');
        $this->assertNull($unknown->prefix());
    }

    public function testShorten()
    {
        $foafName = new EasyRdf_Resource('http://xmlns.com/foaf/0.1/name');
        $this->assertEquals('foaf:name', $foafName->shorten());
    }

    public function testShortenUnknown()
    {
        $unknown = new EasyRdf_Resource('http://example.com/foo');
        $this->assertEquals(null, $unknown->shorten());
    }

    public function testLabelWithRdfsLabel()
    {
        $this->_resource->set('rdfs:label', 'Label Text');
        $this->_resource->set('foaf:name', 'Foaf Name');
        $this->_resource->set('dc:title', 'Dc Title');
        $this->assertStringEquals('Label Text', $this->_resource->label());
    }

    public function testLabelWithFoafName()
    {
        $this->_resource->set('foaf:name', 'Foaf Name');
        $this->_resource->set('dc:title', 'Dc Title');
        $this->assertStringEquals('Foaf Name', $this->_resource->label());
    }

    public function testLabelWithDc11Title()
    {
        $this->_resource->set('dc11:title', 'Dc11 Title');
        $this->assertStringEquals('Dc11 Title', $this->_resource->label());
    }

    public function testLabelNoRdfsLabel()
    {
        $this->assertNull($this->_resource->label());
    }

    public function testLabelWithLang()
    {
        $this->_resource->set('rdfs:label', 'Label Text');
        $this->_resource->set(
            'dc:title',
            new EasyRdf_Literal('Dc Title', 'en')
        );
        $this->assertStringEquals('Dc Title', $this->_resource->label('en'));
    }

    public function testDumpValue()
    {
        $this->assertEquals(
            'http://example.com/#me',
            $this->_resource->dumpValue(false)
        );

        $html = $this->_resource->dumpValue(true);
        $this->assertContains("<a href='http://example.com/#me'", $html);
        $this->assertContains("http://example.com/#me</a>", $html);
    }

    public function testDumpShortenableValue()
    {
        $this->assertEquals(
            'foaf:Person',
            $this->_type->dumpValue(false)
        );

        $html = $this->_type->dumpValue(true);
        $this->assertContains(
            "<a href='http://xmlns.com/foaf/0.1/Person'", $html
        );
        $this->assertContains("foaf:Person</a>", $html);
    }

    public function testDump()
    {
        $text = $this->_resource->dump(false);
        $this->assertContains(
            "http://example.com/#me (EasyRdf_Resource)", $text
        );
        $this->assertContains(
            '-> rdf:type -> foaf:Person', $text
        );
        $this->assertContains(
            '-> rdf:test -> "Test A", "Test B"@en', $text
        );

        $html = $this->_resource->dump(true);
        $this->assertContains("<div id='http://example.com/#me'", $html);
        $this->assertContains(
            "<a href='http://example.com/#me' ".
            "style='text-decoration:none;color:blue'>".
            "http://example.com/#me</a>", $html
        );
        $this->assertContains(
            "<span style='text-decoration:none;color:green'>rdf:type</span>",
            $html
        );
        $this->assertContains(
            "<a href='http://xmlns.com/foaf/0.1/Person' ".
            "style='text-decoration:none;color:red'>foaf:Person</a>",
            $html
        );
        $this->assertContains(
            "<span style='text-decoration:none;color:green'>rdf:test</span>",
            $html
        );
        $this->assertContains(
            "<span style='color:blue'>&quot;Test A&quot;</span>", $html
        );
        $this->assertContains(
            "<span style='color:blue'>&quot;Test B&quot;@en</span>", $html
        );
    }

    public function testDumpWithNoProperties()
    {
        $resource = new EasyRdf_Resource("http://example.com/empty");
        $this->assertEquals('', $resource->dump(false));
        $this->assertEquals('', $resource->dump(true));
    }

    public function testMagicGet()
    {
        $this->assertStringEquals('Test A', $this->_resource->getRdf_test());
    }

    public function testMagicGetNonExistantProperty()
    {
        $this->assertStringEquals('', $this->_resource->getFoo_bar());
    }

    public function testMagicAll()
    {
        $all = $this->_resource->allRdf_test();
        $this->assertEquals(2, count($all));
        $this->assertStringEquals('Test A', $all[0]);
        $this->assertStringEquals('Test B', $all[1]);
    }

    public function testMagicAllNonExistantProperty()
    {
        $this->assertEquals(array(), $this->_resource->allFoo_bar());
    }

    public function testMagicBadMethodCall()
    {
        $this->setExpectedException('BadMethodCallException');
        $this->_resource->fooBar();
    }

    public function testToString()
    {
        $this->assertStringEquals(
            'http://example.com/#me',
            $this->_resource
        );
    }
}
