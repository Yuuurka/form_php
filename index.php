<?php
$host='localhost'; //ip-адрес сервера
$db = 'form'; //название базы данных
$username = 'postgres'; //имя пользователя
$password = 'postgres'; //пароль пользователя
$dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password"; //pgsql - postgresql
$conn = new PDO($dsn); //подключение с PDO
if (!$conn){
  echo "Ошибка"; //Если подключение не прошло, то выводим ошибку
}

$first_name = $last_name = $patronymic = $email = $message = ""; //Изначальные значения равны пустой строке, чтобы при входе на сайт в полях были видны плейсхолдеры, а не ошибка Undefined index

Class User { //Класс "пользователь"
  public $first_name;
  public $last_name;
  public $patronymic;
  public $email;
  public $message;

  public function __construct($first_name, $last_name, $patronymic, $email, $message){ //сохраняет переданные данные в соответствующие свойства
    $this->first_name = $first_name;
    $this->last_tname = $last_name;
    $this->patronymic = $patronymic;
    $this->email = $email;
    $this->message = $message;
  }
    
  function clear($data){ //метод убирает пробелы по бокам, лишние слешы, преобразует специальные символы в HTML-сущности
    $data = stripslashes(strip_tags(htmlspecialchars(trim($data))));
    return $data;
  }
}


$pattern_first_name = $pattern_last_name = $pattern_patronymic = "/^[a-zA-Zа-яёА-ЯЁ]+$/u"; //регулярное выражение для имени, фамилии, отчества
$err = array( //массив ошибок, по дефолту в теге h1 пустая строка, чтобы под полями ввода ничего не было
  'first_name' => '<h1 class="text_error"></h1>',
  'last_name' => '<h1 class="text_error"></h1>',
  'patronymic' => '<h1 class="text_error"></h1>',
  'email' => '<h1 class="text_error"></h1>',
  'message' => '<h1 class="text_error"></h1>',
);

// $err = [];
$flag = 0; //флаг, изменяющий свое значение при ошибках
$letter1 = "";
//echo var_dump($err);

if(isset($_POST["submit"])) { //если была отправлена форма
  $user = new User($_POST['first_name'], $_POST['last_name'], $_POST['patronymic'], $_POST['email'], $_POST['message']); //создается объект с полученными данными
  $first_name = $user -> clear($_POST['first_name']); //в переменную записывается результат метода clear класса User с полученным значением
  $last_name = $user -> clear($_POST['last_name']); //в переменную записывается результат метода clear класса User с полученным значением
  $patronymic = $user -> clear($_POST['patronymic']); //в переменную записывается результат метода clear класса User с полученным значением
  $email = $user -> clear($_POST['email']); //в переменную записывается результат метода clear класса User с полученным значением
  $message = $user -> clear($_POST['message']); //в переменную записывается результат метода clear класса User с полученным значением

  if (!preg_match($pattern_first_name, $first_name)){ //проверка имени на соответсвие регулярному выражению
    $err['first_name'] = '<h1 class="text_error">Неверный формат</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (strlen($first_name) > 32){ //проверка имени на длину
    $err['first_name'] = '<h1 class="text_error">Длина превышает допустимое количество символов</h1>';  //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (empty($first_name)){ //проверка имени на пустоту строки
    $err['first_name'] = '<h1 class="text_error">Обязательно поле</h1>';  //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (!preg_match($pattern_last_name, $last_name)){ //проверка фамилии на соответсвие регулярному выражению
    $err['last_name'] = '<h1 class="text_error">Неверный формат</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (strlen($last_name) > 32){ //проверка фамилии на длину
    $err['last_name'] = '<h1 class="text_error">Длина превышает допустимое количество символов</h1>';  //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (empty($last_name)){ //проверка фамилии на пустоту строки
    $err['last_name'] = '<h1 class="text_error">Обязательно поле</h1>';  //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (!preg_match($pattern_patronymic, $patronymic)){ //проверка отчества на соответсвие регулярному выражению
    $err['patronymic'] = '<h1 class="text_error">Неверный формат</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (strlen($patronymic) > 32){ //проверка отчества на длину
    $err['patronymic'] = '<h1 class="text_error">Длина превышает допустимое количество символов</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (empty($patronymic)){ //проверка отчества на пустоту строки
    $err['patronymic'] = '<h1 class="text_error">Обязательно поле</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1;//ошибка - флаг=1
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)){ //проверка корректности email-адреса
    $err['email'] = '<h1 class="text_error">Неверный формат</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (empty($email)){ //проверка email-адреса на пустоту строки
    $err['email'] = '<h1 class="text_error">Обязательно поле</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if (empty($message)){ //проверка сообщения пользователя на пустоту строки
    $err['message'] = '<h1 class="text_error">Обязательно поле</h1>'; //если проверка не пройдена, то значение ошибки в массиве меняется на данную
    $flag = 1; //ошибка - флаг=1
  }
  if ($flag == 0){ //если ошибок нет
    $sql = "INSERT INTO users(first_name, last_name, patronymic, email_address, letter)VALUES('$first_name', '$last_name', '$patronymic', '$email', '$message')"; //sql запрос на добавление данных
    $conn->query($sql); //выполнение запроса
    //Header("Location:". $_SERVER['HTTP_REFERER']."?mes=success");
  }
}


// Для html
// $retn = "SELECT letter FROM users WHERE first_name='$first_name' AND last_name='$last_name' AND patronymic='$patronymic' AND email_address='$email'"; //sql запрос на получение данных (в данном случае получаем только те письма, которые удовлетворяют условию)
// foreach($conn->query($retn) as $row){ //перебор элементов массива
//   $letter1.strval($row['letter'].'<br/>'.'<br/>'); // добавляем строку к изначальное пустой строке и ставим html разделитель
//  }
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

