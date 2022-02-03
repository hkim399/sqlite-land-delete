<?php
    echo "<h1> hello world!!! github and azure </h1>";
    # step 1
    # creates a new databsae named school.db
    $db = new SQLite3('school.db');

    $version = $db->querySingle('SELECT SQLITE_VERSION()');
    
    echo "<br />version: " . $version . "<br />";

    # step 2
#===============================================
# Create table
#===============================================
echo "<hr /><h3>Create Table</h3>";

# syntax are similar to mysql
// this was the for testing adding another table
//$SQL_create_table = "CREATE TABLE IF NOT EXISTS Students0 (
// this is for the after break
$SQL_create_table = "CREATE TABLE IF NOT EXISTS Students (
    StudentId VARCHAR(10) NOT NULL,
    FirstName VARCHAR(80),
    LastName VARCHAR(80),
    School VARCHAR(50),
    PRIMARY KEY (StudentId)
);";

echo "<p>$SQL_create_table</p>";

$db->exec($SQL_create_table);

#step 3
#===============================================
# Insert sample data
#===============================================
// echo "<hr /><h3>Insert sample data</h3>";
// $SQL_insert_data = "INSERT INTO Students (StudentId, FirstName, LastName, School)
// VALUES
// ('A00111111', 'Tom', 'Max', 'Science'),
// ('A00222222', 'Ann', 'Fay', 'Mining'),
// ('A00333333', 'Joe', 'Sun', 'Nursing'),
// ('A00444444', 'Sue', 'Fox', 'Computing'),
// ('A00555555', 'Ben', 'Ray', 'Mining')
// ";

// echo "<p>$SQL_insert_data</p>";

// $db->exec($SQL_insert_data);

#step 4
#===============================================
# Query data using column names
#===============================================
echo "<hr /><h3>Query data</h3>";

// get every row and 
$res = $db->query('SELECT * FROM Students');
// display the data
while ($row = $res->fetchArray()) {
    echo "{$row['StudentId']} {$row['FirstName']} {$row['LastName']}  {$row['School']}<br />";
}
#step 5
#===============================================
# Parameterized statement with Question marks
#===============================================
// parateterized query, using ?. the placeholder with ?
// db have prepare statement
echo "<hr /><h3>Parameterized statement with Question marks</h3>";
$stm = $db->prepare('SELECT * FROM Students WHERE StudentId = ?');
$stm->bindValue(1, "A00333333", SQLITE3_TEXT); // bind is 1 index based??? not sure

// resrutn result set (res)
$res = $stm->execute();

// this row is 0 index based
$row = $res->fetchArray(SQLITE3_NUM);
echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";

#===============================================
# Parameterized statements with named placeholders
#===============================================
// this uses id
echo "<hr /><h3>Parameterized statements with named placeholders</h3>";

$stm = $db->prepare('SELECT * FROM Students WHERE StudentId = :id');
$stm->bindValue(':id', "A00555555", SQLITE3_TEXT);

$res = $stm->execute();

$row = $res->fetchArray(SQLITE3_NUM);
echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";

#===============================================
# bind_param
#===============================================
// this looking for multiple columns
echo "<hr /><h3>bind_param</h3>";
$sql = "";
$sql .= 'SELECT * FROM Students';
$sql .= ' WHERE FirstName = ? AND LastName = ?';

echo "<p>$sql</p>";

$stm = $db->prepare( $sql );

// you need to pass it by reference
$firstName = 'Sue';
$lastName = 'Fox';

$stm->bindParam(1, $firstName); // $firstname was not declared before
$stm->bindParam(2, $lastName);
// you can't pass by value but by reference if you try the bottom one, there will be an error
// $stm->bindParam(1, 'Sue'); // $firstname was not declared before
// $stm->bindParam(2, 'Fox');

$res = $stm->execute();

$row = $res->fetchArray(SQLITE3_NUM);
echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";

// meta data. data about the data
#===============================================
# Meta data - number of columns
#===============================================
// see how many columns in the data
// ther are 2 because of first and last??
echo "<hr /><h3>Meta data - number of columns</h3>";
$res = $db->query("SELECT FirstName, LastName FROM Students");
$cols = $res->numColumns();

echo "<p>There are {$cols} columns in the result set.</p>";

#===============================================
# Meta data - column names
#===============================================
// returns the result set
echo "<hr /><h3>Meta data - column names</h3>";
$res = $db->query("PRAGMA table_info(Students)");

// fetch array, you need to tell what type it is. in this case, number
// when printed we will get 0 or 1. 1 means it cannot be null and 0 means it can be null
while ($row = $res->fetchArray(SQLITE3_NUM)) {
    echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";
}

#===============================================
# Meta data - another way to find column names
#===============================================
echo "<hr /><h3>Meta data - another way to find column names</h3>";

// this time you execute the query, and if you know the number of the query, you can display like *here
$res = $db->query("SELECT * FROM Students");
// *here
$col0 = $res->columnName(0);
$col1 = $res->columnName(1);
$col2 = $res->columnName(2);
$col3 = $res->columnName(3);

// display column names
$header = sprintf("%-10s %s %s %s\n", $col0, $col1, $col2, $col3);
echo "<p>$header</p>";

// display the data
while ($row = $res->fetchArray()) {
    $line = sprintf("<p>%-10s %s %s %s</p>", $row[0], $row[1], $row[2], $row[3]);
    echo $line;
}


// step after break
#===============================================
# Meta Data -List tables in the database
#===============================================
// in sql, can do it by == show tables;
// in sqlite, you have to do it this way
echo "<hr /><h3>Meta Data -List tables in the database</h3>";

// this way for sqlite. sqlite_master is the hidden table. 
// give me all the name of from the hidden table, named table?
$res = $db->query("SELECT name FROM sqlite_master WHERE type='table'");

$cols = $res->numColumns();

echo "<p>There are {$cols} columns in the result set.</p>";

while ($row = $res->fetchArray(SQLITE3_NUM)) {
    echo "<p>{$row[0]}</p>";
}

#===============================================
# Rows that were modified, inserted, or deleted
#===============================================
// see how many rows were effected by the query?
echo "<hr /><h3>Rows that were modified, inserted, or deleted</h3>";
$SQL_insert_data = "INSERT INTO Students (StudentId, FirstName, LastName, School)
VALUES
('A00666666', 'Tim', 'Day', 'Science'),
('A00777777', 'Zoe', 'Fry', 'Mining'),
('A00888888', 'Jim', 'Roy', 'Nursing'),
('A00888899', 'Joe', 'Lee', 'Nursing'),
('A00999999', 'Fay', 'Lot', 'Computing')
";
// added on the top Joe Lee

// property called changes. see how many were effeced.
$db->exec($SQL_insert_data);
$changes = $db->changes();
echo "<p>The INSERT statement added $changes rows</p>";

# close database            IMPORTANT, ALWAYS CLOSE
$db->close();
?>