<?php
session_start();
//var_dump($_POST);

// Include database connection
include 'db.php';
include 'points_manager_functions.php';
include 'assignedpoints_manager_functions.php';

// Get the username for display
$username = $_SESSION['FirstName'] . " " . $_SESSION['LastName'];

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addPoint'])) {
        addPoint($_POST['country'], $_POST['city'], $_POST['postalCode'], $_POST['street'], $_POST['buildingNumber'], $_POST['additionalInfo']);
    } elseif (isset($_POST['updatePoint'])) {
        updatePoint($_POST['pointID'], $_POST['country'], $_POST['city'], $_POST['postalCode'], $_POST['street'], $_POST['buildingNumber'], $_POST['additionalInfo']);
    } elseif (isset($_POST['deletePoint'])) {
        deletePoint($_POST['deletePoint']);
    } elseif (isset($_POST['assignPoints'])) {
        if(isset($_POST['driver'])){
            $selectedDriverID = $_POST['driver'];
            $assignedPoints = getAssignedPoints($selectedDriverID);
        }
    } elseif (isset($_POST['assignPoint'])) {
        if (isset($_POST['point']) && isset($_POST['driver']))
            assignPoint($_POST['point'], $_POST['driver']);
    } elseif(isset($_POST['deleteAssignedPoint'])){
        deleteAssignedPoint($_POST['assignedPointID']);
    } elseif(isset($_POST['updateAssignedPoint'])){
        updateAssignedPoint($_POST['editAssignedPointID'], $_POST['driver']);
    }
}

// Get all points for display
$points = getAllPoints();
$drivers = getAllDrivers();
$availablePoints = getAvailablePoints();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Dispatcher Dashboard</title>
</head>
<body>
    <div>
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <p>This is the dispatcher dashboard.</p>
        
        <!-- Buttons -->
        <div>
            <form method="post" action="" class="standard-form">
                <button type="submit" class="green-button" name="pointsManager">Points Manager</button>
            </form>
            <form method="post" action="" class="standard-form">
                <button type="submit" class="green-button" name="assignPoints">Assign Points</button>
            </form>
        </div>

        <?php if (isset($_POST['pointsManager']) || isset($_POST['addPoint']) || isset($_POST['deletePoint']) || isset($_POST['edit']) || isset($_POST['updatePoint'])): ?>
        <!-- Points Manager Section -->
        <h2>Points Manager</h2>

            <h3>Points List</h3>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Postal Code</th>
                    <th>Street</th>
                    <th>Building Number</th>
                    <th>Additional Info</th>
                    <th>Date Created</th>
                    <th>Date Edited</th>
                    <th>Is Assigned to driver</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php foreach ($points as $point): ?>
                    <tr>
                        <td><?php echo $point['PointID']; ?></td>
                        <td><?php echo $point['PointCountry']; ?></td>
                        <td><?php echo $point['PointCity']; ?></td>
                        <td><?php echo $point['PointPostalCode']; ?></td>
                        <td><?php echo $point['PointStreet']; ?></td>
                        <td><?php echo $point['PointBuildingNumber']; ?></td>
                        <td><?php echo $point['PointAdditionalInfo']; ?></td>
                        <td><?php echo $point['DateCreated']; ?></td>
                        <td><?php echo $point['DateEdited']; ?></td>
                        <td><?php if(isPointAssignedToDriver($point['PointID'])) echo "Yes"; else echo "No"; ?></td>
                        <td>
                        <form method="post" action="">
                            <!-- Type hidden to pass data -->
                            <input type="hidden" name="edit" value="<?php echo $point['PointID']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        </td>
                        <td>
                        <form method="post" action="">
                            <input type="hidden" name="deletePoint" value="<?php echo $point['PointID']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this point?')">Delete</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table><br>
            
            <!-- Edit Point -->
            <?php if (isset($_POST['edit'])):?>
                
                <?php $editobj = getPointById($_POST['edit'])->fetch_assoc(); ?>

                <form method="post" action="" class="addPointFormVisible">
                    <h3>Edit Point</h3>
                    <label for="country">Country:</label>
                    <input type="text" name="country" value="<?php echo $editobj['PointCountry']; ?>" required>

                    <label for="city">City:</label>
                    <input type="text" name="city" value="<?php echo $editobj['PointCity']; ?>" required>

                    <label for="postalCode">Postal Code:</label>
                    <input type="text" name="postalCode" value="<?php echo $editobj['PointPostalCode']; ?>" required>

                    <label for="street">Street:</label>
                    <input type="text" name="street" value="<?php echo $editobj['PointStreet']; ?>" required>

                    <label for="buildingNumber">Building Number:</label>
                    <input type="text" name="buildingNumber" value="<?php echo $editobj['PointBuildingNumber']; ?>" required>

                    <label for="additionalInfo">Additional Info:</label>
                    <input type="text" name="additionalInfo" value="<?php echo $editobj['PointAdditionalInfo']; ?>">

                    <input type="hidden" name="pointID" value="<?php echo $editobj['PointID']; ?>">
                    <button type="submit" class="green-button" name="updatePoint">Update Point</button>
                </form>
                <br>
            <?php endif; ?>

            <!-- Add Point -->
            <button id="addPointButton" class="green-button">Add Point</button>

            <form id="addPointForm" method="post" action="" class="addPointFormHidden">
                <h4>Add New Point</h4>
                <label for="country">Country:</label>
                <input type="text" name="country" required>

                <label for="city">City:</label>
                <input type="text" name="city" required>

                <label for="postalCode">Postal Code:</label>
                <input type="text" name="postalCode" required>

                <label for="street">Street:</label>
                <input type="text" name="street" required>

                <label for="buildingNumber">Building Number:</label>
                <input type="text" name="buildingNumber" required>

                <label for="additionalInfo">Additional Info:</label>
                <input type="text" name="additionalInfo">
                
                <button type="submit" class="green-button" name="addPoint">Add Point</button>
            </form>            

            <script>
                // JavaScript to toggle visibility of the Add Point form
                document.getElementById('addPointButton').addEventListener('click', function() {
                    if(document.getElementById('addPointForm').className == 'addPointFormVisible')
                        document.getElementById('addPointForm').className = 'addPointFormHidden';
                    else
                        document.getElementById('addPointForm').className = 'addPointFormVisible';
                });
            </script>

        <?php endif; ?>

        <!-- Assign Points Section -->
    <?php if (isset($_POST['assignPoints']) || isset($_POST['assignPoint']) || isset($_POST['editAssignedPoint']) || isset($_POST['deleteAssignedPoint']) || isset($_POST['editAssignedPointID'])): ?>
        <h2>Assigned Points Manager</h2>
        <form method="post" action="">
            <label for="driver">Select Driver:</label>
            <select name="driver" id="driver" required>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo $driver['EmployeeID']; ?>"><?php echo $driver['EmployeeFirstName'] . ' ' . $driver['EmployeeLastName'] . '; ';
                            if($driver['IsEmployeeFree'] == 1) echo "FREE"; else echo "BUSY";?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            <button type="submit" class="green-button" name="assignPoints">Show Assigned Points</button>
        </form>

        <?php if (isset($assignedPoints)): ?>
            <!-- Display assigned points in a table -->

            <?php echo "<h3>Assigned Points </h3>";?>
            <table border="1">
                <tr>
                    <th>Assigned Point ID</th>
                    <th>Point ID</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Postal Code</th>
                    <th>Street</th>
                    <th>Building Number</th>
                    <th>Additional Info</th>
                    <th>Assigned Driver</th>
                    <th>Is Bound to Route</th>
                    <th>Visited</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php foreach ($assignedPoints as $point): ?>
                    <tr>
                        <td><?php echo $point['AssignedPointID']; ?></td>
                        <td><?php echo $point['PointID']; ?></td>
                        <td><?php echo $point['PointCountry']; ?></td>
                        <td><?php echo $point['PointCity']; ?></td>
                        <td><?php echo $point['PointPostalCode']; ?></td>
                        <td><?php echo $point['PointStreet']; ?></td>
                        <td><?php echo $point['PointBuildingNumber']; ?></td>
                        <td><?php echo $point['PointAdditionalInfo']; ?></td>
                        <td><?php echo $point['EmployeeFirstName'] . ' ' . $point['EmployeeLastName']; ?></td>
                        <td><?php if($point['IsAssignedPointTaken'] == 1) echo "Yes"; else echo "No"; ?></td>
                        <td><?php if($point['IsAssignedPointVisited'] == 1) echo "Yes"; else echo "No"; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="editAssignedPointID" value="<?php echo $point['AssignedPointID']; ?>">
                                <button type="submit" name="editAssignedPoint">Edit</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="assignedPointID" value="<?php echo $point['AssignedPointID']; ?>">
                                <button type="submit" name="deleteAssignedPoint" onclick="return confirm('Are you sure you want to delete this assigned point?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table><br>
            <?php endif; ?>
                <!-- Edit Assigned Point -->
                <?php if (isset($_POST['editAssignedPointID'])):?>

                    <form id="editAssignedPointForm" method="post" action="" class="addPointFormVisible">
                        <?php $editAssignedPointObj = getAssignedPointById($_POST['editAssignedPointID'])->fetch_assoc() ?>

                        <h4>Edit Assigned Point #<?php echo $editAssignedPointObj['AssignedPointID']; ?></h4>
                        
                        <label for="assignedDriver">Assigned Driver:</label>
                        <input type="text" value="<?php echo $editAssignedPointObj['EmployeeFirstName'] . ' ' . $editAssignedPointObj['EmployeeLastName']; ?>" readonly>

                        <input type="hidden" name="editAssignedPointID" value="<?php echo $editAssignedPointObj['AssignedPointID']; ?>">

                        <label for="driver">Select Driver:</label>
                        <select name="driver" id="driver" required>
                            <?php foreach ($drivers as $driver): ?>
                                <option value="<?php echo $driver['EmployeeID']; ?>"><?php echo $driver['EmployeeFirstName'] . ' ' . $driver['EmployeeLastName']; ?></option>
                            <?php endforeach; ?>
                        </select><br>

                        <button type="submit" class="green-button" name="updateAssignedPoint">Update Assigned Point</button>
                    </form>

                <?php endif; ?>

        <!-- Assign Point -->
        <br><button id="assignPointButton" class="green-button">Assign new Point</button>

        <form id="assignPointForm" method="post" action=""class="addPointFormHidden">

            <label for="driver">Select Driver:</label>
            <select name="driver" id="driver" required>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo $driver['EmployeeID']; ?>"><?php echo $driver['EmployeeFirstName'] . ' ' . $driver['EmployeeLastName'] . '; ';
                            if ($driver['IsEmployeeFree'] == 1) echo "FREE";
                            else echo "BUSY"; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            
            <label for="point">Select Point:</label>
            <select name="point" id="point" required>
                <?php foreach ($availablePoints as $point): ?>
                    <option value="<?php echo $point['PointID']; ?>"><?php echo $point['PointCountry'] . ', ' . $point['PointCity'] . ', ' . $point['PointStreet']; ?></option>
                <?php endforeach; ?>
            </select>

            <br><button type="submit" class="green-button" name="assignPoint">Submit Point Assignment</button>
        </form>

        <script>
                document.getElementById('assignPointButton').addEventListener('click', function() {
                    if(document.getElementById('assignPointForm').className == 'addPointFormVisible')
                        document.getElementById('assignPointForm').className = 'addPointFormHidden';
                    else
                        document.getElementById('assignPointForm').className = 'addPointFormVisible';
                });
        </script>

    <?php endif; ?>

        <!-- Logout Button -->
        <br><br><button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>

    </div>
</body>
</html>
