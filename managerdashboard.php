<?php
session_start();
include 'db.php';
include 'managerdashboard_functions.php';

// Receive the manager from login
$manager = $_SESSION['User'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addVehicle'])) {

        addVehicle($_POST['vehicleBrand'], $_POST['vehicleName'], $_POST['manufactureDate'], $_POST['serviceDate']);
    } elseif (isset($_POST['addEmployee'])) {
        addEmployee($_POST['employeeFirstName'], $_POST['employeeLastName'], $_POST['employeeRole'], $_POST['employeeSalary'], $_POST['employeeBirthday'], $_POST['employeeContactNumber'], $_POST['employeeUsername'], $_POST['employeePassword']);
    } elseif (isset($_POST['editVehicle'])) {
        editVehicle($_POST['editVehicleValue'], $_POST['vehicleBrand'], $_POST['vehicleName'], $_POST['manufactureDate'], $_POST['serviceDate']);
    } elseif (isset($_POST['deleteVehicleButton'])) {
        deleteVehicle($_POST['deleteVehicleValue']);
    } elseif (isset($_POST['editEmployee'])) {
        editEmployee($_POST['editEmployeeValue'], $_POST['employeeFirstName'], $_POST['employeeLastName'], $_POST['employeeRole'], $_POST['employeeSalary'], $_POST['employeeBirthday'], $_POST['employeeContactNumber'], $_POST['employeeUsername'], $_POST['employeePassword']);
    } elseif (isset($_POST['deleteEmployeeButton'])) {
        deleteEmployee($_POST['deleteEmployeeValue']);
    }
}

$vehicles = getVehicles();
$employees = getEmployees();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Manager Dashboard</title>
</head>
<body>
    <div>
        <h1>Welcome, <?php echo $manager['EmployeeFirstName'] . ' ' . $manager['EmployeeLastName']; ?>!</h1>
        <p>This is your manager dashboard.</p>

        <!-- Buttons -->
        <div>
            <form method="post" action="" class="standard-form">
                <button type="submit" class="green-button" name="employeesManagerButton">Employees Manager</button>
            </form>
        </div>

        <div>
            <form method="post" action="" class="standard-form">
                <button type="submit" class="green-button" name="vehiclesManagerButton">Vehicles Manager</button>
            </form>
        </div>

        <!-- Vehicles -->
        <?php if(isset($_POST['vehiclesManagerButton']) || isset($_POST['editVehicleButton'])): ?>
            <h2>Vehicles</h2>
            <table border="1">
                <tr>
                    <th>Vehicle ID</th>
                    <th>Brand</th>
                    <th>Name</th>
                    <th>Manufacture Date</th>
                    <th>Service Date</th>
                    <th>Date Created</th>
                    <th>Date Edited</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?php echo $vehicle['VehicleID']; ?></td>
                        <td><?php echo $vehicle['VehicleBrand']; ?></td>
                        <td><?php echo $vehicle['VehicleName']; ?></td>
                        <td><?php echo $vehicle['VehicleManufactureDate']; ?></td>
                        <td><?php echo $vehicle['VehicleServiceDate']; ?></td>
                        <td><?php echo $vehicle['DateCreated']; ?></td>
                        <td><?php echo $vehicle['DateEdited']; ?></td>
                        <td>
                        <form method="post" action="">
                            <!-- Type hidden to pass data -->
                            <input type="hidden" name="editVehicleValue" value="<?php echo $vehicle['VehicleID']; ?>">
                            <button type="submit" name="editVehicleButton">Edit</button>
                        </form>
                        </td>
                        <td>
                        <form method="post" action="">
                            <input type="hidden" name="deleteVehicleValue" value="<?php echo $vehicle['VehicleID']; ?>">
                            <button type="submit" name="deleteVehicleButton" onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php if(isset($_POST['editVehicleButton'])): ?>
                <?php $result = getVehicle($_POST['editVehicleValue']); ?>
                <h3>Edit Vehicle</h3>

                <!-- Edit Vehicle Form -->
                <form id="editVehicleForm" method="post" action="" class="addPointFormVisible">
                    <label>Brand:</label>
                    <input type="text" name="vehicleBrand" value="<?php echo $result['VehicleBrand']; ?>" required><br>
                    <label>Name:</label>
                    <input type="text" name="vehicleName" value="<?php echo $result['VehicleName']; ?>" required><br>
                    <label>Manufacture Date:</label>
                    <input type="date" name="manufactureDate" value="<?php echo $result['VehicleManufactureDate']; ?>" required><br>
                    <label>Service Date:</label>
                    <input type="date" name="serviceDate" value="<?php echo $result['VehicleServiceDate']; ?>" required><br>

                    <input type="hidden" name="editVehicleValue" value="<?php echo $_POST['editVehicleValue']; ?>">

                    <button type="submit" class="green-button" name="editVehicle">Edit Vehicle</button>
                </form>
            <?php endif; ?>
        
            <br><button id="showAddVehicleButton" class="green-button">Add Vehicle</button>
            <!-- Add Vehicle Form -->
            <form id="addVehicleForm" method="post" action=""  class="addPointFormHidden">
                <label>Brand:</label>
                <input type="text" name="vehicleBrand" required><br>
                <label>Name:</label>
                <input type="text" name="vehicleName" required><br>
                <label>Manufacture Date:</label>
                <input type="date" name="manufactureDate" required><br>
                <label>Service Date:</label>
                <input type="date" name="serviceDate" required><br>
                <button type="submit" class="green-button" name="addVehicle">Add Vehicle</button>
            </form>

            <script>
                document.getElementById('showAddVehicleButton').addEventListener('click', function() {
                    if(document.getElementById('addVehicleForm').className == 'addPointFormVisible')
                        document.getElementById('addVehicleForm').className = 'addPointFormHidden';
                    else
                        document.getElementById('addVehicleForm').className = 'addPointFormVisible';
                });
            </script>
        <?php endif; ?>

        <!-- Employees -->
        <?php if(isset($_POST['employeesManagerButton']) || isset($_POST['editEmployeeButton'])): ?>
            <h2>Employees</h2>
            <table border="1">
                <tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Salary</th>
                    <th>Birthday</th>
                    <th>Contact Number</th>
                    <th>Date Created</th>
                    <th>Date Edited</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo $employee['EmployeeID']; ?></td>
                        <td><?php echo $employee['EmployeeFirstName']; ?></td>
                        <td><?php echo $employee['EmployeeLastName']; ?></td>
                        <td><?php echo $employee['EmployeeRole']; ?></td>
                        <td><?php echo $employee['EmployeeSalary']; ?></td>
                        <td><?php echo $employee['EmployeeBirthday']; ?></td>
                        <td><?php echo $employee['EmployeeContactNumber']; ?></td>
                        <td><?php echo $employee['DateCreated']; ?></td>
                        <td><?php echo $employee['DateEdited']; ?></td>
                        <td>
                        <form method="post" action="">
                            <!-- Type hidden to pass data -->
                            <input type="hidden" name="editEmployeeValue" value="<?php echo $employee['EmployeeID']; ?>">
                            <button type="submit" name="editEmployeeButton">Edit</button>
                        </form>
                        </td>
                        <td>
                        <form method="post" action="">
                            <input type="hidden" name="deleteEmployeeValue" value="<?php echo $employee['EmployeeID']; ?>">
                            <button type="submit" name="deleteEmployeeButton" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php if(isset($_POST['editEmployeeButton'])): ?>
                <?php $result = getEmployee($_POST['editEmployeeValue']); ?>
                <h3>Edit Employee</h3>

                <!-- Edit Employee Form -->
                <form id="editEmployeeForm" method="post" action="" class="addPointFormVisible">
                    <label>First Name:</label>
                    <input type="text" name="employeeFirstName" value="<?php echo $result['EmployeeFirstName']; ?>" required><br>
                    <label>Last Name:</label>
                    <input type="text" name="employeeLastName" value="<?php echo $result['EmployeeLastName']; ?>" required><br>
                    <label>Role:</label>
                    <select name="employeeRole" required>
                        <option value="Driver">Driver</option>
                        <option value="Dispatcher">Dispatcher</option>
                        <option value="Manager">Manager</option>
                    </select><br>
                    <label>Salary:</label>
                    <input type="number" name="employeeSalary" value="<?php echo $result['EmployeeSalary']; ?>" required><br>
                    <label>Birthday:</label>
                    <input type="date" name="employeeBirthday" value="<?php echo $result['EmployeeBirthday']; ?>" required><br>
                    <label>Contact Number:</label>
                    <input type="text" name="employeeContactNumber" value="<?php echo $result['EmployeeContactNumber']; ?>" required><br>
                    <label>Login:</label>
                    <input type="text" name="employeeUsername" value="<?php echo $result['EmployeeLogin']; ?>" required><br>
                    <label>Password:</label>
                    <input type="text" name="employeePassword" value="<?php echo $result['EmployeePassword']; ?>" required><br>

                    <input type="hidden" name="editEmployeeValue" value="<?php echo $result['EmployeeID']; ?>">

                    <button type="submit" class="green-button" name="editEmployee">Edit Employee</button>
                </form>
            <?php endif; ?>

            <br><button id="showAddEmployeeButton" class="green-button">Add Employee</button>
            <!-- Add Employee Form -->
            <form id="addEmployeeForm" method="post" action="" class="addPointFormHidden">
                <label>First Name:</label>
                <input type="text" name="employeeFirstName" required><br>
                <label>Last Name:</label>
                <input type="text" name="employeeLastName" required><br>
                <label>Role:</label>
                <select name="employeeRole" required>
                    <option value="Driver">Driver</option>
                    <option value="Dispatcher">Dispatcher</option>
                    <option value="Manager">Manager</option>
                </select><br>
                <label>Salary:</label>
                <input type="number" name="employeeSalary" required><br>
                <label>Birthday:</label>
                <input type="date" name="employeeBirthday" required><br>
                <label>Contact Number:</label>
                <input type="text" name="employeeContactNumber" required><br>
                <label>Login:</label>
                <input type="text" name="employeeUsername" required><br>
                <label>Password:</label>
                <input type="password" name="employeePassword" required><br>

                <button type="submit" class="green-button" name="addEmployee">Add Employee</button>
            </form>

            <script>
                document.getElementById('showAddEmployeeButton').addEventListener('click', function() {
                    if(document.getElementById('addEmployeeForm').className == 'addPointFormVisible')
                        document.getElementById('addEmployeeForm').className = 'addPointFormHidden';
                    else
                        document.getElementById('addEmployeeForm').className = 'addPointFormVisible';
                });
            </script>
        <?php endif; ?>

        <!-- Logout Button -->
        <br><br><button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>
