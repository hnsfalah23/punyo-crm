<?php
// app/core/Database.php

class Database
{
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $dbname = DB_NAME;

  private $dbh;
  private $stmt;
  private $error;

  public function __construct()
  {
    // Set DSN
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';

    // Set options
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    );

    // Create PDO instance
    try {
      $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      throw new Exception('Database Connection Error: ' . $this->error);
    }
  }

  // Prepare statement with query
  public function query($query)
  {
    $this->stmt = $this->dbh->prepare($query);
  }

  // Bind values
  public function bind($param, $value, $type = null)
  {
    if (is_null($type)) {
      switch (true) {
        case is_int($value):
          $type = PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = PDO::PARAM_NULL;
          break;
        default:
          $type = PDO::PARAM_STR;
      }
    }
    $this->stmt->bindValue($param, $value, $type);
  }

  // Execute the prepared statement
  public function execute()
  {
    try {
      return $this->stmt->execute();
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('Database Execute Error: ' . $this->error);
      return false;
    }
  }

  // Get result set as array of objects
  public function resultSet()
  {
    $this->execute();
    return $this->stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get single record as object
  public function single()
  {
    $this->execute();
    return $this->stmt->fetch(PDO::FETCH_OBJ);
  }

  // Get row count
  public function rowCount()
  {
    return $this->stmt->rowCount();
  }

  // Get last insert ID
  public function lastInsertId()
  {
    return $this->dbh->lastInsertId();
  }

  // Transaction Methods
  public function beginTransaction()
  {
    try {
      return $this->dbh->beginTransaction();
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('Transaction Begin Error: ' . $this->error);
      return false;
    }
  }

  public function commit()
  {
    try {
      return $this->dbh->commit();
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('Transaction Commit Error: ' . $this->error);
      return false;
    }
  }

  public function rollback()
  {
    try {
      return $this->dbh->rollBack();
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('Transaction Rollback Error: ' . $this->error);
      return false;
    }
  }

  // Check if we're in a transaction
  public function inTransaction()
  {
    return $this->dbh->inTransaction();
  }

  // Get PDO instance (for advanced operations)
  public function getPDO()
  {
    return $this->dbh;
  }

  // Get last error
  public function getError()
  {
    return $this->error;
  }

  // Execute a raw query (for complex operations)
  public function exec($query)
  {
    try {
      return $this->dbh->exec($query);
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('Database Exec Error: ' . $this->error);
      return false;
    }
  }

  // Prepare and execute a query with parameters (helper method)
  public function run($query, $params = [])
  {
    try {
      $stmt = $this->dbh->prepare($query);
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('Database Run Error: ' . $this->error);
      throw new Exception('Database Error: ' . $this->error);
    }
  }

  // Close connection
  public function close()
  {
    $this->dbh = null;
  }

  // Destructor
  public function __destruct()
  {
    $this->close();
  }
}
