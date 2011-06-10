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

/**
 * Class that represents an RDF resource
 *
 * @package    EasyRdf
 * @copyright  Copyright (c) 2009-2010 Nicholas J Humfrey
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */
class EasyRdf_Resource
{
    /** The URI for this resource */
    private $_uri = null;

    /** Associative array of properties */
    private $_properties = array();

    /** Associative array of inverse properties */
    private $_inverseProperties = array();


    /** Constructor
     *
     * * Please do not call new EasyRdf_Resource() directly *
     *
     * To create a new resource use the get method in a graph:
     * $resource = $graph->resource('http://www.example.com/');
     *
     */
    public function __construct($uri)
    {
        if (!is_string($uri) or $uri == null or $uri == '') {
            throw new InvalidArgumentException(
                "\$uri should be a string and cannot be null or empty"
            );
        }

        $this->_uri = $uri;
    }

    /** Returns the URI for the resource.
     *
     * @return string  URI of this resource.
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /** Set value(s) for a property
     *
     * The new value(s) will replace the existing values for the property.
     * The name of the property should be a string.
     * If you set a property to null or an empty array, then the property
     * will be deleted.
     *
     * @param  string  $property The name of the property (e.g. foaf:name)
     * @param  mixed   $values   The value(s) for the property.
     * @return array             Array of new values for this property.
     */
    public function set($property, $values)
    {
        if (!is_string($property) or $property == null or $property == '') {
            throw new InvalidArgumentException(
                "\$property should be a string and cannot be null or empty"
            );
        }

        // Delete the old values
        $this->delete($property);

        // Add the new values
        $this->add($property, $values);

        return $this->all($property);
    }

    /** Delete a property (or optionally just a specific value)
     *
     * @param  string  $property The name of the property (e.g. foaf:name)
     * @param  object  $value The value to delete (null to delete all values)
     * @return null
     */
    public function delete($property, $value=null)
    {
        if (!is_string($property) or $property == null or $property == '') {
            throw new InvalidArgumentException(
                "\$property should be a string and cannot be null or empty"
            );
        }

        $property = EasyRdf_Namespace::expand($property);
        if (isset($this->_properties[$property])) {
            foreach ($this->_properties[$property] as $k => $v) {
                if (!$value or $v == $value) {
                    unset($this->_properties[$property][$k]);
                    if ($v instanceof EasyRdf_Resource) {
                        $v->deleteInverse($property, $this);
                    }
                }
            }
            if (count($this->_properties[$property]) == 0) {
                unset($this->_properties[$property]);
            }
        }

        return null;
    }

    /** Add values to an existing property
     *
     * The properties can either be a single property name or an
     * associate array of property names and values.
     *
     * The value can either be a single value or an array of values.
     *
     * Examples:
     *   $resource->add('prefix:property', 'value');
     *   $resource->add('prefix:property', array('value1',value2'));
     *   $resource->add(array('prefix:property' => 'value1'));
     *
     * @param  mixed $resource   The resource to add data to
     * @param  mixed $properties The properties or property names
     * @param  mixed $value      The new value for the property
     * @return array             Array of all values associated with property.
     */
    public function add($properties, $values=null)
    {
        if ($properties == null or $properties == '') {
            throw new InvalidArgumentException(
                "\$properties cannot be null or empty"
            );
        }

        // Have multiple properties been given?
        if (is_array($properties)) {
            if (EasyRdf_Utils::is_associative_array($properties)) {
                foreach ($properties as $property => $value) {
                    $this->add($property, $value);
                }
                return;
            } else {
                foreach ($properties as $property) {
                    $this->add($property, $values);
                }
                return;
            }
        } else {
            $property = $properties;
        }

        // No value given?
        if ($values == null) {
             return null;
        }

        // Create the property if it doesn't already exist
        $property = EasyRdf_Namespace::expand($property);
        if (!isset($this->_properties[$property])) {
            $this->_properties[$property] = array();
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        // Convert literal values into objects
        $objects = array();
        foreach ($values as $value) {
            if (is_object($value)) {
                $objects[] = $value;
            } else {
                $objects[] = new EasyRdf_Literal($value);
            }
        }

        // Add the objects, if they don't already exist
        foreach ($objects as $object) {
            if (!$this->matches($property, $object)) {
                array_push($this->_properties[$property], $object);
                if ($object instanceof EasyRdf_Resource) {
                    $object->addInverse($property, $this);
                }
            }
        }

        return $this->_properties[$property];
    }

    /** Get a single value for a property
     *
     * If multiple values are set for a property then the value returned
     * may be arbitrary.
     *
     * If $property is an array, then the first item in the array that matches
     * a property that exists is returned.
     *
     * This method will return null if the property does not exist.
     *
     * @param  string|array $property The name of the property (e.g. foaf:name)
     * @param  string       $lang     The language to filter by (e.g. en)
     * @return mixed                  A value associated with the property
     */
    public function get($property, $lang=null)
    {
        if (is_array($property)) {
            foreach ($property as $p) {
                $value = $this->get($p, $lang);
                if ($value)
                    return $value;
            }
            return null;
        }

        if (!is_string($property) or $property == null or $property == '') {
            throw new InvalidArgumentException(
                "\$property should be a string and cannot be null or empty"
            );
        }

        // Is an inverse property being requested?
        if (substr($property, 0, 1) == '-') {
            $property = substr($property, 1);
            $properties = $this->_inverseProperties;
        } else {
            $properties = $this->_properties;
        }

        $property = EasyRdf_Namespace::expand($property);
        if (isset($properties[$property])) {
            # FIXME: sort values so that we are likely to return the same one?
            if ($lang) {
                foreach ($properties[$property] as $value) {
                    if (is_object($value) && $value->getLang() == $lang)
                        return $value;
                }
            } else if (count($properties[$property]) > 0) {
                return $properties[$property][0];
            }
        }

        return null;
    }

    /** Get all values for a property
     *
     * This method will return an empty array if the property does not exist.
     *
     * @param  string  $property The name of the property (e.g. foaf:name)
     * @param  string  $lang     The language to filter by (e.g. en)
     * @return array             A value associated with the property
     */
    public function all($property, $lang=null)
    {
        if (!is_string($property) or $property == null or $property == '') {
            throw new InvalidArgumentException(
                "\$property should be a string and cannot be null or empty"
            );
        }

        // Is an inverse property being requested?
        if (substr($property, 0, 1) == '-') {
            $property = substr($property, 1);
            $properties = $this->_inverseProperties;
        } else {
            $properties = $this->_properties;
        }

        $property = EasyRdf_Namespace::expand($property);
        if (isset($properties[$property])) {
            if ($lang) {
                $values = array();
                foreach ($properties[$property] as $value) {
                    if (is_object($value) && $value->getLang() == $lang)
                        $values[] = $value;
                }
                return $values;
            } else {
                return $properties[$property];
            }
        } else {
            return array();
        }
    }

    /** Concatenate all values for a property into a string.
     *
     * The default is to join the values together with a space character.
     * This method will return an empty string if the property does not exist.
     *
     * @param  string  $property The name of the property (e.g. foaf:name)
     * @param  string  $glue     The string to glue the values together with.
     * @param  string  $lang     The language to filter by (e.g. en)
     * @return string            Concatenation of all the values.
     */
    public function join($property, $glue=' ', $lang=null)
    {
        if (!is_string($property) or $property == null or $property == '') {
            throw new InvalidArgumentException(
                "\$property should be a string and cannot be null or empty"
            );
        }

        return join($glue, $this->all($property, $lang));
    }

    /** Get a list of the full URIs for the properties of this resource.
     *
     * This method will return an empty array if the resource has no properties.
     *
     * @return array            Array of full URIs
     */
    public function propertyUris()
    {
        return array_keys($this->_properties);
    }

    /** Get a list of all the shortened property names (qnames) for a resource.
     *
     * This method will return an empty array if the resource has no properties.
     *
     * @return array            Array of shortened URIs
     */
    public function properties()
    {
        $properties = array();
        foreach ($this->_properties as $property => $value) {
            $short = EasyRdf_Namespace::shorten($property);
            if ($short)
                $properties[] = $short;
        }
        return $properties;
    }

    /** Check to see if a property exists for this resource.
     *
     * This method will return true if the property exists.
     *
     * @param  string  $property The name of the property (e.g. foaf:gender)
     * @return bool              True if value the property exists.
     */
    public function has($property)
    {
        $property = EasyRdf_Namespace::expand($property);
        if (isset($this->_properties[$property])) {
            return true;
        } else {
            return false;
        }
    }

    /** Check to see if a value exists for a specified property
     *
     * This method will return true if value exists for a property.
     *
     * @param  string  $property The name of the property (e.g. foaf:gender)
     * @param  string  $value    The value to check for (e.g. male)
     * @return bool              True if value exists for property.
     */
    public function matches($property, $value)
    {
        foreach ($this->all($property) as $v) {
            if ($v instanceof EasyRdf_Resource and $v === $value) {
                return true;
            } else if ($v == $value) {
                return true;
            }
        }
        return false;
    }

    /** Check to see if a resource is a blank node.
     *
     * @return bool True if this resource is a blank node.
     */
    public function isBnode()
    {
        if (substr($this->_uri, 0, 2) == '_:') {
            return true;
        } else {
            return false;
        }
    }

    /** Get the identifier for a blank node
     *
     * Returns null if the resource is not a blank node.
     *
     * @return string The identifer for the bnode
     */
    public function getNodeId()
    {
        if ($this->isBnode()) {
            return substr($this->_uri, 2);
        } else {
            return null;
        }
    }

    /** Get a list of types for a resource.
     *
     * The types will each be a shortened URI as a string.
     * This method will return an empty array if the resource has no types.
     *
     * @return array All types assocated with the resource (e.g. foaf:Person)
     */
    public function types()
    {
        $types = array();
        foreach ($this->all('rdf:type') as $uri) {
            $type = EasyRdf_Namespace::shorten($uri);
            array_push($types, $type);
        }
        return $types;
    }

    /** Get a single type for a resource.
     *
     * The type will be a shortened URI as a string.
     * If the resource has multiple types then the type returned
     * may be arbitrary.
     * This method will return null if the resource has no type.
     *
     * @return string A type assocated with the resource (e.g. foaf:Person)
     */
    public function type()
    {
        $uri = $this->get('rdf:type');
        if ($uri) {
            return EasyRdf_Namespace::shorten($uri);
        } else {
            return null;
        }
    }

    /** Get a single type for a resource, as a resource.
     *
     * The type will be returned as an EasyRdf_Resource.
     * If the resource has multiple types then the type returned
     * may be arbitrary.
     * This method will return null if the resource has no type.
     *
     * @return EasyRdf_Resource A type assocated with the resource.
     */
    public function typeResource()
    {
        return $this->get('rdf:type');
    }

    /** Check if a resource is of the specified type
     *
     * @param  string  $type The type to check (e.g. foaf:Person)
     * @return boolean       True if resource is of specified type.
     */
    public function is_a($type)
    {
        $type = EasyRdf_Namespace::expand($type);
        foreach ($this->all('rdf:type') as $t) {
            if ($t->getUri() == $type) {
                return true;
            }
        }
        return false;
    }

    /** Get the primary topic of this resource.
     *
     * Returns null if no primary topic is available.
     *
     * @return EasyRdf_Resource The primary topic of this resource.
     */
    public function primaryTopic()
    {
        return $this->get(
            array('foaf:primaryTopic', '-foaf:isPrimaryTopicOf')
        );
    }

    /** Get a the prefix of the namespace that this resource is part of
     *
     * This method will return null the resource isn't part of any
     * registered namespace.
     *
     * @return string The namespace prefix of the resource (e.g. foaf)
     */
    public function prefix()
    {
        return EasyRdf_Namespace::prefixOfUri($this->_uri);
    }

    /** Get a shortened version of the resources URI.
     *
     * This method will return the full URI if the resource isn't part of any
     * registered namespace.
     *
     * @return string The shortened URI of this resource (e.g. foaf:name)
     */
    public function shorten()
    {
        return EasyRdf_Namespace::shorten($this->_uri);
    }

    /** Get a human readable label for this resource
     *
     * This method will check a number of properties for the resource
     * (in the order: rdfs:label, foaf:name, dc:title) and return an approriate
     * first that is available. If no label is available then it will 
     * return null.
     *
     * @return string A label for the resource.
     */
    public function label($lang=null)
    {
        return $this->get(
            array('rdfs:label', 'foaf:name', 'dc:title', 'dc11:title'), $lang
        );
    }

    /** Return view of the resource and its properties
     *
     * This method is intended to be a debugging aid and will
     * print a resource and its properties to the screen.
     *
     * @param  bool  $html  Set to true to format the dump using HTML
     */
    public function dump($html=true)
    {
        $plist = array();
        foreach ($this->_properties as $prop => $values) {
            $olist = array();
            foreach ($values as $value) {
                $olist []= $value->dumpValue($html);
            }

            $pstr = EasyRdf_Namespace::shorten($prop);
            if ($pstr == null)
                $pstr = $prop;
            if ($html) {
                $plist []= "<span style='font-size:130%'>&rarr;</span> ".
                           "<span style='text-decoration:none;color:green'>".
                           htmlentities($pstr) . "</span> ".
                           "<span style='font-size:130%'>&rarr;</span> ".
                           join(", ", $olist);
            } else {
                $plist []= "  -> $pstr -> " . join(", ", $olist);
            }
        }

        if (count($plist)) {
            if ($html) {
                return "<div id='".htmlentities($this->_uri)."' " .
                       "style='font-family:arial; padding:0.5em; ".
                       "background-color:lightgrey;border:dashed 1px grey;'>\n".
                       "<div>".$this->dumpValue(true, 'blue')." ".
                       "<span style='font-size: 0.8em'>(".
                       get_class($this).")</span></div>\n".
                       "<div style='padding-left: 3em'>\n".
                       "<div>".join("</div>\n<div>", $plist)."</div>".
                       "</div></div>\n";
            } else {
                return $this->_uri." (".get_class($this).")\n" .
                       join("\n", $plist) . "\n\n";
            }
        } else {
            return '';
        }
    }

    /** Return pretty-print view of just this resource
     *
     * @param  bool  $html  Set to true to format the dump using HTML
     */
    public function dumpValue($html=true, $color='red')
    {
        $short = $this->shorten();
        if ($html) {
            $escaped = htmlentities($this->_uri);
            if ($this->isBnode()) {
                $href = '#' . $escaped;
            } else {
                $href = $escaped;
            }
            if ($short) {
                return "<a href='$href' style='text-decoration:none;color:$color'>$short</a>";
            } else {
                return "<a href='$href' style='text-decoration:none;color:$color'>$escaped</a>";
            }
        } else {
            if ($short) {
                return $short;
            } else {
                return $this->_uri;
            }
        }
    }

    /** This function is for internal use only.
     *
     * Adds an inverse property to a resource.
     *
     * @ignore
     */
    public function addInverse($property, $value)
    {
        if (!isset($this->_inverseProperties[$property])) {
            $this->_inverseProperties[$property] = array();
        }

        // Is the object already in the array?
        foreach ($this->_inverseProperties[$property] as $v) {
            if ($v === $value)
                return;
        }

        array_push($this->_inverseProperties[$property], $value);
    }

    /** This function is for internal use only.
     *
     * Deletes an inverse property from a resource.
     *
     * @ignore
     */
    public function deleteInverse($property, $value)
    {
        if (isset($this->_inverseProperties[$property])) {
            foreach ($this->_inverseProperties[$property] as $k => $v) {
                if ($v === $value) {
                    unset($this->_inverseProperties[$property][$k]);
                }
            }
        }
    }


    /** Magic method to give access to properties using method calls
     *
     * This method is allows you to access the properties using method calls.
     *
     * The format is:
     *  1) the lowercse method name (get or all)
     *  2) the namespace prefix
     *  3) an underscore
     *  4) the property name (case preserved)
     *
     * For example:
     *   $resource->getFoaf_name()
     *   $resource->allFoaf_knows()
     *
     * @return mixed The value(s) of the properties requested.
     */
    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);
        $property = preg_replace(
            '/_/', ':', strtolower(substr($name, 3, 1)) . substr($name, 4), 1
        );

        switch ($method) {
          case 'get':
              return $this->get($property);
              break;

          case 'all':
              return $this->all($property);
              break;

          default:
              throw new BadMethodCallException(
                  'Tried to call unknown method '.get_class($this).'::'.$name
              );
              break;
        }
    }

    /** Magic method to return URI of resource when casted to string
     *
     * @return string The URI of the resource
     */
    public function __toString()
    {
        return $this->_uri;
    }
}

