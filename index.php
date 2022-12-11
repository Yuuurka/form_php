<?php
$host='localhost';
$db = 'form';
$username = 'postgres';
$password = 'postgres';
$dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";
$conn = new PDO($dsn);
if (!$conn){
  echo "Ошибка";
}

$first_name = $last_name = $patronymic = $email = $message = "";

Class User {
  public $first_name;
  public $last_name;
  public $patronymic;
  public $email;
  public $message;

  public function __construct($first_name, $last_name, $patronymic, $email, $message){
    $this->first_name = $first_name;
    $this->last_tname = $last_name;
    $this->patronymic = $patronymic;
    $this->email = $email;
    $this->message = $message;
  }
    
  function clear($data){
    $data = pg_escape_string(stripslashes(strip_tags(htmlspecialchars(trim($data)))));
    return $data;
  }
}


$pattern_first_name = $pattern_last_name = $pattern_patronymic = "/^[a-zA-Zа-яёА-ЯЁ]+$/u";
$err = array(
  'first_name' => '<h1 class="text_error"></h1>',
  'last_name' => '<h1 class="text_error"></h1>',
  'patronymic' => '<h1 class="text_error"></h1>',
  'email' => '<h1 class="text_error"></h1>',
  'message' => '<h1 class="text_error"></h1>',
);

// $err = [];
$flag = 0;
$letter1 = "";
//echo var_dump($err);

if(isset($_POST["submit"])) {
  $user = new User($_POST['first_name'], $_POST['last_name'], $_POST['patronymic'], $_POST['email'], $_POST['message']);
  $first_name = $user -> clear($_POST['first_name']);
  $last_name = $user -> clear($_POST['last_name']);
  $patronymic = $user -> clear($_POST['patronymic']);
  $email = $user -> clear($_POST['email']);
  $message = $user -> clear($_POST['message']);

  if (!preg_match($pattern_first_name, $first_name)){
    $err['first_name'] = '<h1 class="text_error">Неверный формат</h1>';
    $flag = 1;
  }
  if (strlen($first_name) > 32){
    $err['first_name'] = '<h1 class="text_error">Длина превышает допустимое количество символов</h1>';
    $flag = 1;
  }
  if (empty($first_name)){
    $err['first_name'] = '<h1 class="text_error">Обязательно поле</h1>';
    $flag = 1;
  }
  if (!preg_match($pattern_last_name, $last_name)){
    $err['last_name'] = '<h1 class="text_error">Неверный формат</h1>';
    $flag = 1;
  }
  if (strlen($last_name) > 32){
    $err['last_name'] = '<h1 class="text_error">Длина превышает допустимое количество символов</h1>';
    $flag = 1;
  }
  if (empty($last_name)){
    $err['last_name'] = '<h1 class="text_error">Обязательно поле</h1>';
    $flag = 1;
  }
  if (!preg_match($pattern_patronymic, $patronymic)){
    $err['patronymic'] = '<h1 class="text_error">Неверный формат</h1>';
    $flag = 1;
  }
  if (strlen($patronymic) > 32){
    $err['patronymic'] = '<h1 class="text_error">Длина превышает допустимое количество символов</h1>';
    $flag = 1;
  }
  if (empty($patronymic)){
    $err['patronymic'] = '<h1 class="text_error">Обязательно поле</h1>';
    $flag = 1;
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $err['email'] = '<h1 class="text_error">Неверный формат</h1>';
    $flag = 1;
  }
  if (empty($email)){
    $err['email'] = '<h1 class="text_error">Обязательно поле</h1>';
    $flag = 1;
  }
  if (empty($message)){
    $err['message'] = '<h1 class="text_error">Обязательно поле</h1>';
    $flag = 1;
  }
  if ($flag == 0){
    $sql = "INSERT INTO users(first_name, last_name, patronymic, email_address, letter)VALUES('$first_name', '$last_name', '$patronymic', '$email', '$message')";
    $conn->query($sql);
    $retn = "SELECT letter FROM users WHERE first_name='$first_name' AND last_name='$last_name' AND patronymic='$patronymic' AND email_address='$email'";
    foreach($conn->query($retn) as $row){
      $letter1.strval($row['letter'].'<br/>');
     }
    //Header("Location:". $_SERVER['HTTP_REFERER']."?mes=success");
  }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" media="all"/>
    <title>Form</title>
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="header__wrapped">
        <div class="header__block">
          <div class="logo">
            <img class="logo__image" src="img/logo.png" alt="logo" />
          </div>
        </div>
      </div>
    </div>
  </header>
  <section class="content">
          <div class="main">
              <div class="content__block">
                <form class="class_form" name="insert" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" >
                  <legend>Форма для заполнения</legend>
                    <p>
                      <label for="first_name"></label><input type="text" name="first_name" value="<?php echo $first_name; ?>" placeholder="Имя">*
                    </p>
                    <?php echo $err['first_name'];?>
                    <p>
                      <label for="last_name"></label><input type="text" name="last_name" value="<?php echo $last_name; ?>" placeholder="Фамилия">*
                    </p>
                    <?php echo $err['last_name'];?>
                    <p>
                      <label for="patronymic"> </label><input type="text" name="patronymic" value="<?php echo $patronymic; ?>" placeholder="Отчество">*
                    </p>
                    <?php echo $err['patronymic'];?>
                    <p>
                      <label for="email"></label><input type="email" name="email" value="<?php echo $email; ?>" placeholder="Email-адрес">*
                    </p>
                    <?php echo $err['email'];?>
                    <textarea rows="10" cols="45" name="message" placeholder="Ваше сообщение..."><?php echo $message; ?></textarea>
                    <?php echo $err['message'];?>
                    <p><input type="submit" name="submit" class="button" value="Отправить"></p>
                </form>
              </div>
              <h2>Здравстуйте, Ваши прошлые обращения:</h2>
              <h2><?php if(isset($_POST["submit"])){    $retn = "SELECT letter FROM users WHERE first_name='$first_name' AND last_name='$last_name' AND patronymic='$patronymic' AND email_address='$email'";
    foreach($conn->query($retn) as $row){
      print $row['letter'].'<br/>'.'<br/>';
     }} ?></h2>
          </div>
        </section>
</body>
</html>

