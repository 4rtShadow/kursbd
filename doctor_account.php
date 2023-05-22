<!DOCTYPE html>
<html>
<head>
  <title>Личный кабинет доктора</title>
  <link rel="stylesheet" type="text/css" href="styledocaccount.css">
</head>
<body>
    <?php
     // Параметры подключения к базе данных
    $host = "localhost";
    $user = "root";
    $password = "99830056Abcd";
    $db_name = "hospital";
          
    // Устанавливаем соединение с базой данных
    $conn = mysqli_connect($host, $user, $password, $db_name);

    // Проверяем, был ли пользователь авторизован
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: doctor_login.php");
        exit;
    }

    // Получаем информацию о пользователе
    $name = $_SESSION['username'];
    $sql = "SELECT * FROM doctors WHERE full_name = '$name'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    // Получаем связанные с пользователем медицинские записи
    $doctor_id = $row['id'];
    $date = date('Y-m-d');
    $sql = "SELECT appointments.*, patients.full_name AS patient_name, patients.phone_number, patients.medical_policy FROM appointments JOIN patients ON appointments.patient_id = patients.id WHERE appointments.doctor_id = '$doctor_id' AND appointments.date_and_time >= '$date' ORDER BY appointments.date_and_time ASC";
    $result = mysqli_query($conn, $sql);
    ?>

    <div class="header">
    <img src="images/polytech_logo_main_RGB.png" alt="logo">
    <h1>Личный кабинет</h1>
    <div class="header-links">
        <a class="header-link" href="logoutdoc.php">На главную</a>
    </div>
    </div>

    <div class="user-info">
    <h2>Личная информация</h2>
    <p><b>ФИО:</b> <?php echo $row['full_name']; ?></p>
    <p><b>Специализация:</b> <?php echo $row['field_of_specialization']; ?></p>
    <p><b>Номер кабинета:</b> <?php echo $row['cabinet_number']; ?></p>
    </div>

    <div class="appointments">
    <h2>Медицинские записи</h2>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="appointment">
            <p><b>Дата и время:</b> <?php echo $row['date_and_time']; ?></p>
            <p><b>Пациент:</b> <?php echo $row['patient_name']; ?></p>
            <p><b>Номер телефона пациента:</b> <?php echo $row['phone_number']; ?></p>
            <p><b>Медицинская полис пациента:</b> <?php echo $row['medical_policy']; ?></p>
            <p><b>Лечение:</b> <?php echo $row['treatment']; ?></p>
            <p><b>Диагноз:</b> <?php echo $row['diagnosis']; ?></p>
            <form method="post">
              <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
              <label for="treatment">Лечение:</label>
              <input type="text" id="treatment" name="treatment" value="<?php echo $row['treatment']; ?>" size="50">
              <label for="diagnosis">Диагноз:</label>
              <input type="text" id="diagnosis" name="diagnosis" value="<?php echo $row['diagnosis']; ?>" size="50">
              <input type="submit" name="update_appointment" value="Обновить запись">
            </form>
            <form method="post">
              <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
              <input type="submit" name="delete_appointment" value="Удалить запись">
            </form>
        </div>
        <?php } ?>
    <?php } else { ?>
        <p>Нет записей на прием</p>
    <?php } ?>
    </div>

    <script>
    document.querySelector('#datetime').addEventListener('change', function() {
        var datetime = this.value;
        // Отправляем запрос на сервер с выбранной датой и временем
        // и отображаем информацию о записи
    });
    </script>

    <?php
    if (isset($_POST['update_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $treatment = $_POST['treatment'];
        $diagnosis = $_POST['diagnosis'];
        $sql = "UPDATE appointments SET treatment = '$treatment', diagnosis = '$diagnosis' WHERE id = '$appointment_id'";
        mysqli_query($conn, $sql);
        header("Location: doctor_account.php");
        exit;
    }

    if (isset($_POST['delete_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $sql = "DELETE FROM appointments WHERE id = '$appointment_id'";
        mysqli_query($conn, $sql);
        header("Location: doctor_account.php");
        exit;
    }
    ?>

    <?php mysqli_close($conn); ?>
</body>
</html>