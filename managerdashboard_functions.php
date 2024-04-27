<?php
function getVehicles() {
    global $conn;

    $sql = "SELECT * FROM Vehicles WHERE DateDeleted IS NULL";
    $result = $conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getEmployees() {
    global $conn;

    $sql = "SELECT * FROM Employees WHERE DateDeleted IS NULL";
    $result = $conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
}

function addVehicle($brand, $name, $manufactureDate, $serviceDate) {
    global $conn;

    $sql = "INSERT INTO Vehicles (VehicleBrand, VehicleName, VehicleManufactureDate, VehicleServiceDate, IsVehicleFree, DateCreated)
            VALUES ('$brand', '$name', '$manufactureDate', '$serviceDate', 1, NOW())";

    $conn->query($sql);
}

function addEmployee($firstName, $lastName, $role, $salary, $birthday, $contactNumber, $username, $password) {
    global $conn;

    //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Employees (EmployeeFirstName, EmployeeLastName, EmployeeRole, EmployeeSalary, EmployeeBirthday, EmployeeContactNumber, EmployeeLogin, EmployeePassword, IsEmployeeFree, DateCreated)
            VALUES ('$firstName', '$lastName', '$role', '$salary', '$birthday', '$contactNumber', '$username', '$password', 1, NOW())";

    $conn->query($sql);
}

function getVehicle($id) {
    global $conn;

    $sql = "SELECT * FROM Vehicles WHERE VehicleID='$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getEmployee($id) {
    global $conn;

    $sql = "SELECT * FROM Employees WHERE EmployeeID='$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function editVehicle($id, $brand, $name, $manufactureDate, $serviceDate) {
    global $conn;

    $sql = "UPDATE Vehicles SET VehicleBrand='$brand', VehicleName='$name', VehicleManufactureDate='$manufactureDate', VehicleServiceDate='$serviceDate', DateEdited=NOW() WHERE VehicleID='$id'";

    $conn->query($sql);
}

function deleteVehicle($id) {
    global $conn;

    $sql = "UPDATE Vehicles SET DateDeleted=NOW() WHERE VehicleID='$id'";

    $conn->query($sql);
}

function editEmployee($id, $firstName, $lastName, $role, $salary, $birthday, $contactNumber, $username, $password) {
    global $conn;

    $sql = "UPDATE Employees SET EmployeeFirstName='$firstName', EmployeeLastName='$lastName', EmployeeRole='$role', EmployeeSalary='$salary', EmployeeBirthday='$birthday', EmployeeContactNumber='$contactNumber', EmployeeLogin='$username', EmployeePassword='$password', DateEdited=NOW() WHERE EmployeeID='$id'";

    $conn->query($sql);
}

function deleteEmployee($id) {
    global $conn;

    $sql = "UPDATE Employees SET DateDeleted=NOW() WHERE EmployeeID='$id'";

    $conn->query($sql);
}
?>