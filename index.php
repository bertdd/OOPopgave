<?php

require_once 'repository.php';

$database = "probeer";
$table = "person";
$idColumn = "id";
$repository = new Repository($database);
$record = [];

try
{
  if (isset($_POST["button"]))
  {
    $pressed = $_POST["button"];
    unset($_POST["button"]);
    switch ($pressed)
    {
      case "Save":
        $record = $repository->Save($table, $idColumn, $_POST);
        break;

      case "Clear":
        $record = [];
        break;

      case "Search":
        unset($_POST[$idColumn]);
        $record = $repository->Search($table, $_POST);
        break;

      case "Delete":
        $repository->Delete($table, $idColumn, $_POST[$idColumn]);
        $record = [];
        break;
    }
  }
}
catch (Exception $exception)
{
  echo $exception->getmessage();
}

function Row(array $record, string $label, string $field, bool $readonly = false)
{
  $ro = $readonly ? "readonly" : "";
  print "<tr><td><label>$label</label></td><td><input type='text' name='$field' $ro value='$record[$field]'/></td></tr>";
}
?>

<html>
<head>
  <title>Object oriented form</title>
</head>
<body>
  <form action="/" method="post">
    <table>
      <?php Row($record, "Id", $idColumn, true); ?>
      <?php Row($record, "Naam", "name"); ?>
      <?php Row($record, "Adres", "address"); ?>
      <?php Row($record, "Postcode", "postalcode"); ?>
      <?php Row($record, "Plaats", "place"); ?>
      <?php Row($record, "Geboortedatum", "birthdate"); ?>
      <?php Row($record, "Telefoon", "phone"); ?>
      <?php Row($record, "Email", "email"); ?>
    </table>
    <br/>
    <div>
      <input type="submit" value="Save" name="button" />
      <input type="submit" value="Clear" name="button" />
      <input type="submit" value="Search" name="button" />
      <input type="submit" value="Delete" name="button" />
    </div>
  </form>
</body>
</html>

