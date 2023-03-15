<?php
class Repository
{
  public function __construct(string $database, string $host="localhost", string $username="root", string $password="Koala1#")
  {
    $this->pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
  }

  public function Save(string $table, string $idColumn, array $data) : array
  {
    $id = $data[$idColumn];
    if ($id == "")
    {
      unset($data[$idColumn]);
      $result = $this->New($table, $data);
      $result[$idColumn] = $this->pdo->lastInsertId($idColumn);
      return $result;
    }
    else
    {
      return $this->Update($table, $idColumn, $data);
    }
  }

  private function New(string $table, array $data) : array
  {
    $fields = $this->fields($data);
    $values = $this->params($data);
    $statement = $this->pdo->prepare("insert into $table ($fields) values ($values)");
    $statement->execute($this->data($data));
    return $data;
  }

  private function Update(string $table, string $idColumn, array $data) : array
  {
    $idParam = $this->param($idColumn);
    $set = "";
    $first = true;
    $params = [];
    foreach ($data as $field => $value)
    {
      if (!$first)
      {
        $set .= ", ";
      }
      $first = false;
      $p = $this->param($field);
      $set .= "$field = $p";
      $params[$p] = $value;
    }
    $statement = $this->pdo->prepare("update $table set $set where $idColumn = $idParam");
    $statement->execute($params);
    return $data;
  }

  public function Search(string $table, array $criteria) : array
  {
    $params = [];
    $search = "";
    $first = true;

    foreach ($criteria as $field => $value)
    {
      if ($value != "")
      {
        if (!$first)
        {
          $search .= " and ";
        }
        $p = $this->param($field);
        $search .= "$field like $p";
        $params[$p] = "%$value%";
        $first = false;
      }
    }

    $statement = $this->pdo->prepare("select * from $table where $search");
    if ($statement->execute($params))
    {
      $result = $statement->fetch(PDO::FETCH_ASSOC);
    }

    return $result == false ? [] : $result;
  }

  public function Delete(string $table, string $idColumn, string $id) : void
  {
    $p = $this->param($idColumn);
    $statement = $this->pdo->prepare("delete from $table where $idColumn = $p");
    $statement->execute([ $p => $id]);
  }

  private function fields(array $data) : string
  {
    $result = "";
    $first = true;
    foreach ($data as $field => $value)
    {
      $result .= ($first) ? "$field" : ", $field";
      $first = false;
    }
    return $result;
  }

  private function params(array $data) : string
  {
    $result = "";
    $first = true;
    foreach ($data as $field => $value)
    {
      $p = $this->param($field);
      $result .= ($first) ? $p : ", $p";
      $first = false;
    }
    return $result;
  }

  private function data(array $data) : array
  {
    $result = [];
    foreach ($data as $field => $value)
    {
      $result[$this->param($field)] = $value;
    }
    return $result;
  }

  private function param(string $fieldName) : string
  {
    return ":$fieldName";
  }

  private PDO $pdo;
}