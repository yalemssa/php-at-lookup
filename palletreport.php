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
require 'at-lookup.php';
require 'at-config.php';

$at = new ATLookup(ATDB_HOST, ATDB_USER, ATDB_PASS, ATDB_DATABASE);
$pallets = array('2-1', '4-1', '4-2', '4-3', '4-4');

?>
<html>
  <head>
    <title>Current Pallet Report from AT</title>
  </head>
  <body>
    <p>Boxes currently shown by the AT to be on Pallets in the Skid Room (B54).</p>
    <p>To select a column, hold the Control [Ctrl] key and click-and-drag to select text.</p>
    <table border="0" cellspacing="10" cellpadding="10" valign="top">
      <tr><?php foreach ($pallets as $p) echo "<th>Pallet $p</th>"; ?></tr>
<?php
foreach ($pallets as $p) { 
  echo '<td valign="top">';
  echo "<table><tr><th>Barcode</th><th>Box #</th><th>Coll #</th></tr>\n";
  $palletdata = $at->by_coordinate1('pallet', $p);
  while ($row = $palletdata->fetch_assoc()) {
    echo '<tr>';
    echo '<td>'. $row['barcode'] .'</td>';
    echo '<td>';
    echo empty($row['container1AlphaNumIndicator']) ? $row['container1NumericIndicator'] : $row['container1AlphaNumIndicator'];
    echo '</td>';
    $resource = $at->get_resource_from_component($row['resourceComponentId']);
    echo '<td>'. $resource['resourceIdentifier1'] .'&nbsp;'. $resource['resourceIdentifier2'] .'</td>';
    echo "</tr>\n";
  }
  echo '</table></td>';
}
?>
</tr></table>
</body>
</html>