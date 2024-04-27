<?php
session_start();

include 'db.php';

    //functions that checks if points/employees/vehicles table are empty, and if yes it
    //populates them with data from the txt files
    function populateEmployeeTableIfEmpty($file) {
        global $conn;
        $sql = "SELECT * FROM Employees";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $file = fopen($file, "r");
            while (!feof($file)) {
                $line = fgets($file);
                $line = explode(";", $line);
                $sql = "INSERT INTO Employees (EmployeeFirstName, EmployeeLastName, EmployeeBirthday, EmployeeSalary,
                EmployeeContactNumber, EmployeeLogin, EmployeePassword, EmployeeRole,
                IsEmployeeFree, DateCreated, DateEdited, DateDeleted)
                VALUES ('$line[0]', '$line[1]', '$line[2]', '$line[3]', '$line[4]', '$line[5]',
                '$line[6]', '$line[7]', '$line[8]', $line[9], NULL, NULL)";

                $conn->query($sql);
            }
            fclose($file);
        }
    }

    function populatePointsTableIfEmpty($file) {
        global $conn;
        $sql = "SELECT * FROM Points";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $file = fopen($file, "r");
            while (!feof($file)) {
                $line = fgets($file);
                $line = explode(";", $line);
                $sql = "INSERT INTO Points (PointCountry, PointCity, PointPostalCode, PointStreet, PointBuildingNumber, PointAdditionalInfo, DateCreated, DateEdited, DateDeleted)
                        VALUES ('$line[0]', '$line[1]', '$line[2]', '$line[3]', '$line[4]', '$line[5]', '$line[6]', NULL, NULL)";
                $conn->query($sql);
            }
            fclose($file);
        }
    }

    function populateVehiclesTableIfEmpty($file) {
        global $conn;
        $sql = "SELECT * FROM Vehicles";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $file = fopen($file, "r");
            while (!feof($file)) {
                $line = fgets($file);
                $line = explode(";", $line);
                $sql = "INSERT INTO Vehicles (VehicleBrand, VehicleName, VehicleManufactureDate, VehicleServiceDate, IsVehicleFree, DateCreated, DateEdited, DateDeleted)
                VALUES ('$line[0]', '$line[1]', '$line[2]', '$line[3]', '$line[4]', '$line[5]', NULL, NULL)";
                $conn->query($sql);
            }
            fclose($file);
        }
    }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    populateEmployeeTableIfEmpty("employees.txt");
    populatePointsTableIfEmpty("points.txt");
    populateVehiclesTableIfEmpty("vehicles.txt");

    $login = $_POST["login"];
    $password = $_POST["password"];

    // verify password hash
    // $sql = "SELECT EmployeePassword FROM employees WHERE EmployeeLogin='$login'";
    // $result = $conn->query($sql);
    // $result = $result->fetch_assoc();
    // $hash = $result['EmployeePassword'];

    // if (!password_verify($password, $hash)) {
    //     echo "Invalid username or password.";
    //     return;
    // }
    
    $sql = "SELECT * FROM employees WHERE EmployeeLogin='$login'
                                     AND EmployeePassword='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $result = $result->fetch_assoc();
        $_SESSION['FirstName'] = $result['EmployeeFirstName'];
        $_SESSION['LastName'] = $result['EmployeeLastName'];
        $_SESSION['User'] = $result;

        if($result['EmployeeRole'] == 'Dispatcher')
            header("location: dispatcherdashboard.php");
        else if($result['EmployeeRole'] == 'Driver')
            header("location: driverdashboard.php");
        else if($result['EmployeeRole'] == 'Manager')
            header("location: managerdashboard.php");
    } else {
        echo "Invalid username or password.";
    }
}

$conn->close();
?>