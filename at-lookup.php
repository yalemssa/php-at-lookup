<?php
/**
 * Archivists' Toolkit(TM) PHP Lookup Tools
 * Copyright (c) 2010 Yale University
 * All rights reserved.
 * 
 * This software is free. You can redistribute it and / or modify it under the
 * terms of the Educational Community License (ECL)  version 1.0
 * (http://www.opensource.org/licenses/ecl1.php)
 *
 * This software is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the ECL license for more details
 * about permissions and limitations.
 *
 */ 
class ATLookup 
{
  private $_db;
  
  /**
   * Constructor method for ATLookup class.
   *
   * @param string $dbhost
   *   hostname of the database server
   * @param string $dbuser
   *   username for database connection
   * @param string $dbpass
   *   password for database connection
   * @param string $dbname
   *   name of the database to connect to
   */
  function __construct($dbhost, $dbuser, $dbpass, $dbname)
  {
    $this->_db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  }
  
  /**
   * Destructor method for ATLookup class.
   */
  function __destruct()
  {
    $this->_db->close();
  }
  
  /**
   * Sanitize a given string using the MySQL real_escape_string function
   * 
   * @param string $value
   *   string to sanitize
   * @return string
   *   sanitized version of string
   */
  private function sanitize($value)
  {
    return $this->_db->real_escape_string($value);
  }
  
  /**
   * Look up AT instances by their first coordinate.
   *
   * @param string $label
   *   location label
   * @param string $indicator
   *   location indicator
   * @return mysqli_result
   *   a mysqli_result set representing matching instances
   */
  function by_coordinate1($label=NULL, $indicator=NULL)
  {
    $label = $this->sanitize($label);
    $indicator = $this->sanitize($indicator);
    $q = "SELECT DISTINCT (adi.barcode), adi.container1Type,
      adi.container1NumericIndicator, adi.container1AlphaNumIndicator,
      adi.userDefinedString1, loc.coordinate1Label,
      loc.coordinate1AlphaNumIndicator, adi.resourceComponentId
      FROM ArchDescriptionInstances adi
      JOIN LocationsTable AS loc ON adi.locationId = loc.locationId
      WHERE loc.coordinate1Label LIKE '$label'
        AND loc.coordinate1AlphaNumIndicator = '$indicator'
      GROUP BY adi.barcode
      ORDER BY adi.container1NumericIndicator;";
    
    return $this->_db->query($q); 
  }
  
  /**
   * Identify a component's ultimate parent resource record identifier.
   *
   * @param string $componentId
   *   the identifier of a given resource component record
   * @return array
   *   associative array of parent resource
   */
  function get_resource_from_component($componentId)
  {
    $componentId = $this->sanitize($componentId);
    $par_q = "SELECT rsc.resourceComponentId, rsc.parentResourceComponentId, 
                rsc.resourceId FROM ResourcesComponents rsc
                WHERE rsc.resourceComponentId = '$componentId';";
    $result = $this->_db->query($par_q)->fetch_assoc();
    $parentid = $result['parentResourceComponentId'];
    if (is_null($parentid))
    {
      return $this->get_resource($result['resourceId']);  
    } else {
      return $this->get_resource_from_component($parentid);
    }
  }
  
  /**
   * Retrieve a row representing a resource based on its ID
   *
   * @param string $resourceId
   *   the identifier of the resource record
   * @return array
   *   associative array representing resource record 
   */
  function get_resource($resourceId)
  {
    $resourceId = $this->sanitize($resourceId);
    $q = "SELECT * FROM Resources rsc WHERE rsc.resourceId = '$resourceId';";
    return $this->_db->query($q)->fetch_assoc();
  }
}

?>