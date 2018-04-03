<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

class database {

    var $_sql = '';

    /** @var Internal variable to hold the connector resource */
    var $_resource = '';

    /** @var Internal variable to hold the query result */
    var $_result = '';

    /** @var Internal variable to hold the query result */
    var $_insert_id = '';

    /**
     * Database object constructor
     * @param string Database host
     * @param string Database user name
     * @param string Database user password
     * @param string Database name
     * @param string Common prefix for all tables
     * @param boolean If true and there is an error, go offline
     */
    function database() {
        global $glob;
        $host = $glob['dbhost'];
        $user = $glob['dbusername'];
        $pass = $glob['dbpassword'];
        $db = $glob['dbdatabase'];
        if ($this->_resource = @mysql_connect($host, $user, $pass)) {
            mysql_select_db($db) or die('Cant select database' . mysql_error());
        } else {
            echo "Could not connect to the database!";
            exit;
        }
    }

    /**
     * Execute the query
     * @return mixed A database resource if successful, FALSE if not.
     */
    function query($sql) {
        $_sql = $sql;
        return $_result = mysql_query($_sql);
    }

    function insert($table, $dbfields) {
        $field = array();
        $value = array();
        foreach ($dbfields as $k => $v) {
            $v = addslashes(stripslashes($v));
            $field[] = $k;
            $value[] = $v;
        }
        $f = implode('`,`', $field);
        $val = implode("','", $value);
        $insertsql = "INSERT INTO `$table` (`$f`) VALUES ('$val')";
        $this->_sql = $insertsql;
        $result = mysql_query($insertsql);
        $this->_insert_id = mysql_insert_id();
        return $this->_insert_id;
    }

    function update($table, $dbfields, $where) {
        $updatesql = "UPDATE $table SET ";
        $i = 0;
        foreach ($dbfields as $k => $v) {
            $v = addslashes(stripslashes($v));
            if ($i == 0) {
                if ($v != NULL)
                    $updatesql .= " $k = '$v' ";
                else
                    $updatesql .= " $k = NULL ";
            }
            else {
                if ($v != NULL)
                    $updatesql .= ", $k = '$v' ";
                else
                    $updatesql .= ", $k = NULL ";
            }
            $i++;
        }
        $updatesql .= " WHERE $where";
        $this->_sql = $updatesql;
        $result = mysql_query($updatesql);
        return true;
    }

    function select($vars = "*", $table, $where = "", $order_by = "", $group_by = "", $result_type = MYSQL_ASSOC) {
        if ($vars != "*") {
            if (is_array($vars)) {
                $vars = implode(",", $vars);
            }
        }
        $select_sql = "SELECT " . $vars . " FROM " . $table . " WHERE 1 " . $where . " " . $order_by . " " . $group_by;
        $this->_sql = $select_sql;
        $resource = $this->exec_query($select_sql);
        $result = array();
        while ($row = mysql_fetch_array($resource, $result_type)) {
            $result[] = $row;
        }
        return $result;
    }

    function exec_query($sql) {
        return @mysql_query($sql);
    }

    function delete($table, $where) {
        $deletesql = "DELETE FROM $table WHERE $where ";
        $this->_sql = $deletesql;
        $result = mysql_query($deletesql);
        return true;
    }

    function getInsertId() {
        echo $this->_insert_id;
    }

    function numRows($sql) {
        $_sql = $sql;
        $_result = mysql_query($_sql);
        $results = mysql_num_rows($_result);
        mysql_free_result($_result);
        return $results;
    }

    function dbClose() {
        mysql_close($this->_resource);
    }

    function fetchArray($rs) {
        return @mysql_fetch_array($rs);
    }

}

?>