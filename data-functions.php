<?php

// Global variables
$pfLmB = array();
$BHHE4 = 8;
$jEump = 1;
$Gdcna = 2023;

/**
 * Generate an INNER JOIN clause for SQL queries.
 */


/**
 * Search data in a table using a regular expression.
 */
function searchDataInTable($table, $searchTerm, $columns, $selectColumns = "*", $singleResult = false, $callback = null, $offset = null, $limit = null, $groupBy = null, $orderBy = null) {
    global $con;
    validateProject();

    $results = array();
    $selectColumnsArray = explode(",", $selectColumns);
    $searchTerms = explode(" ", $searchTerm);
    $columnList = explode(",", $columns);

    // Build the query
    $query = "SELECT $selectColumns, SUM(({$columnList[0]} REGEXP '[[:<:]]" . implode("|", $searchTerms) . "[[:>:]]')) AS match_count FROM $table ";

    // Build the WHERE clause
    $whereClause = '';
    foreach ($columnList as $column) {
        $column = trim($column);
        if ($whereClause == '') {
            $whereClause = "($column REGEXP '[[:<:]]" . implode("|", $searchTerms) . "[[:>:]]')";
        } else {
            $whereClause .= " OR ($column REGEXP '[[:<:]]" . implode("|", $searchTerms) . "[[:>:]]')";
        }
    }

    $query .= " WHERE $whereClause GROUP BY $selectColumns ORDER BY match_count DESC";

    // Add LIMIT and OFFSET if provided
    if ($limit != null) {
        $query .= " LIMIT $limit";
    }
    if ($offset != null) {
        $query .= " OFFSET $offset";
    }

    // Execute the query
    $result = $con->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($callback != null) {
                $row = $callback($row);
            }

            if ($selectColumns != "*" && sizeof($selectColumnsArray) == 1) {
                $results[] = $row[trim($selectColumns)];
            } else {
                $results[] = $row;
            }
        }
    } else {
        logError("Unable to search in table. Query: $query. Error: " . $con->error);
    }

    // Return single result or all results
    return $singleResult ? (empty($results) ? null : $results[0]) : $results;
}

/**
 * Get data from a table.
 */
function getDataFromTable($table, $selectColumns = "*", $whereClause = "1", $singleResult = false, $callback = null, $orderBy = null, $orderDirection = null, $offset = null, $limit = null, $groupBy = null, $having = null) {
    global $con;
    // validateProject();

    $results = array();
    $selectColumnsArray = explode(",", $selectColumns);

    // Build the query
    $query = "SELECT $selectColumns FROM $table";

    if ($whereClause != null) {
        $query .= " WHERE $whereClause";
    }

    if ($groupBy != null && $having != null) {
        $query .= " GROUP BY $groupBy HAVING $having";
    }

    if ($orderBy != null && $orderDirection != null) {
        $query .= " ORDER BY $orderBy $orderDirection";
    }

    if ($limit != null) {
        $query .= " LIMIT $limit";
    }

    if ($offset != null) {
        $query .= " OFFSET $offset";
    }

    // Execute the query
    $result = $con->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($callback != null) {
                $row = $callback($row);
            }

            if ($selectColumns != "*" && sizeof($selectColumnsArray) == 1) {
                $results[] = $row[trim($selectColumns)];
            } else {
                $results[] = $row;
            }
        }
    } else {
        logError("Unable to get data from table. Query: $query. Error: " . $con->error);
        return array("status" => 0, "msg" => "Unable to get data");
    }

    // Return single result or all results
    return $singleResult ? (empty($results) ? null : $results[0]) : $results;
}

/**
 * Delete data from a table.
 */
function deleteDataFromTable($table, $whereClause) {
    global $con;
    // validateProject();

    $query = "DELETE FROM $table WHERE $whereClause";

    if ($con->query($query)) {
        return array("status" => 1, "msg" => "Deleted");
    } else {
        logError("Unable to delete data from table. Query: $query. Error: " . $con->error);
        return array("status" => 0, "msg" => "Failed");
    }
}

/**
 * Insert data into a table.
 */
function insertDataIntoTable($table, $data, $columns = null) {
    global $con;
    // validateProject();

    $columnList = '';
    $valueList = '';

    // Prepare column and value lists
    if ($columns != null) {
        $columnsArray = explode(",", $columns);
        foreach ($columnsArray as $column) {
            $column = trim($column);
            $columnList .= ($columnList == '') ? $column : ", $column";
            $valueList .= ($valueList == '') ? "'{$data[$column]}'" : ", '{$data[$column]}'";
        }
    } else {
        foreach ($data as $key => $value) {
            $columnList .= ($columnList == '') ? $key : ", $key";
            $valueList .= ($valueList == '') ? "'$value'" : ", '$value'";
        }
    }

    // Build and execute the query
    $query = "INSERT INTO $table ($columnList) VALUES ($valueList)";

    if ($con->query($query)) {
        return array("status" => 1, "msg" => "Success", "id" => $con->insert_id);
    } else {
        logError("Unable to insert data into table. Query: $query. Error: " . $con->error);
        return array("status" => 0, "msg" => "Failed");
    }
}

/**
 * Update data in a table.
 */
function updateDataIntoTable($table, $data, $whereClause) {
    global $con;
    // validateProject();

    $updateClause = '';

    // Prepare the SET clause
    foreach ($data as $key => $value) {
        
         if (strpos($value, ' + ') !== false || strpos($value, ' - ') !== false) {
            // Don't add quotes for arithmetic expressions
            $updateClause .= ($updateClause == '') ? "$key = $value" : ", $key = $value";
        } else {
            // Add quotes for normal values
            $updateClause .= ($updateClause == '') ? "$key = '$value'" : ", $key = '$value'";
        }
        
        // $updateClause .= ($updateClause == '') ? "$key = '$value'" : ", $key = '$value'";
    }

    // Build and execute the query
    $query = "UPDATE $table SET $updateClause WHERE $whereClause";

    if ($con->query($query)) {
        return array("status" => 1, "msg" => "Updated");
    } else {
        logError("Unable to update data in table. Query: $query. Error: " . $con->error);
        return array("status" => 0, "msg" => "Failed");
    }
}
function generateInnerJoin($joinTable, $joinColumn, $condition = null) {
    global $con;
    $join = " INNER JOIN $joinTable ON $joinTable.$joinColumn = tournaments.$joinColumn";
    if ($condition) {
        $join .= " AND $condition";
    }
    
    return $join;
}
/**
 * Log errors to a file.
 */
function logError($message) {
    $logMessage = date("Y-m-d H:i:s") . " | Error: $message\n";
    file_put_contents("logs/error-logs.txt", $logMessage, FILE_APPEND);
}

?>