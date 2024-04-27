<?php
session_start();
include 'db.php';
include 'driverdashboard_functions.php';

// Receive the driver from login
$driver = $_SESSION['User'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if(isset($_POST['addRoute'])) {
        addNewRoute();
    }else if(isset($_POST['manageVisited'])) {
        setVisited($_POST['manageAssignedPointPointID']);
    }
}

$assignedPoints = getAssignedPoints();
$driverRoutes = getDriverRoutes();
$vehicles = getVehicles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Driver Dashboard</title>
</head>
<body>
    <div>
        <h1>Welcome, <?php echo "{$driver['EmployeeFirstName']} {$driver['EmployeeLastName']}"; ?>!</h1>
        <p>This is your driver dashboard.</p>

        <!-- Buttons -->
        <div>
            <form method="post" action="" class="standard-form">
                <button type="submit" class="green-button" name="routesManagerButton">Routes Manager</button>
            </form>
        </div>

        <!-- Assigned Points -->
        <h2>Assigned Points</h2>
        <?php if (!empty($assignedPoints)): ?>
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
                </tr>
                <?php foreach ($assignedPoints as $asPoint): ?>
                    <tr>
                        <td><?php echo $asPoint['AssignedPointID']; ?></td>
                        <td><?php echo $asPoint['PointID']; ?></td>
                        <td><?php echo $asPoint['PointCountry']; ?></td>
                        <td><?php echo $asPoint['PointCity']; ?></td>
                        <td><?php echo $asPoint['PointPostalCode']; ?></td>
                        <td><?php echo $asPoint['PointStreet']; ?></td>
                        <td><?php echo $asPoint['PointBuildingNumber']; ?></td>
                        <td><?php echo $asPoint['PointAdditionalInfo']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No assigned points at the moment.</p>
        <?php endif; ?>

        <!-- Routes Manager -->
        <?php if(isset($_POST['routesManagerButton']) || isset($_POST['manageSubmit'])): ?>
            <h2>Routes Manager</h2>

            <!-- Driver Routes -->
            <?php if (!empty($driverRoutes)): ?>
                <table border="1">
                    <tr>
                        <th>Route ID</th>
                        <th>Vehicle</th>
                        <th>Route Status</th>
                        <th>Total Points</th>
                        <th>Date Completed</th>
                        <th>Manage</th>
                    </tr>
                    <?php foreach ($driverRoutes as $route): ?>
                        <tr>
                            <td><?php echo $route['RouteID']; ?></td>
                            <td><?php echo "{$route['VehicleBrand']} {$route['VehicleName']}"; ?></td>
                            <td><?php echo $route['RouteStatus']; ?></td>
                            <td><?php echo $route['RouteTotalPoints']; ?></td>
                            <td><?php echo $route['RouteDateCompleted']; ?></td>
                            <?php if($route['RouteStatus'] == 'In Progress'): ?>
                                <td>
                                <form method="post" action="">
                                    <input type="hidden" name="manageRouteID" value="<?php echo $route['RouteID']; ?>">
                                    <button type="submit" name="manageSubmit">Manage</button>
                                </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table><br>

            <!-- Manage Route -->
            <?php if(isset($_POST['manageSubmit'])): ?>
                <!-- Route Points -->

                <?php $manageRouteID = $_POST['manageRouteID']; ?>

                <?php $routePoints = getRoutePoints($manageRouteID); ?>

                <h3>Route Points</h3>
                <?php if (!empty($routePoints)): ?>
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
                            <th>Visted</th>
                            <th>Manage</th>
                        </tr>
                        <?php foreach ($routePoints as $rp): ?>
                            <tr>
                                <td><?php echo $rp['AssignedPointID']; ?></td>
                                <td><?php echo $rp['PointID']; ?></td>
                                <td><?php echo $rp['PointCountry']; ?></td>
                                <td><?php echo $rp['PointCity']; ?></td>
                                <td><?php echo $rp['PointPostalCode']; ?></td>
                                <td><?php echo $rp['PointStreet']; ?></td>
                                <td><?php echo $rp['PointBuildingNumber']; ?></td>
                                <td><?php echo $rp['PointAdditionalInfo']; ?></td>
                                <td><?php echo ($rp['IsAssignedPointVisited'] == 1) ? "Yes" : "No"; ?></td>
                                <td>
                                <form method="post" action="">
                                    <input type="hidden" name="manageAssignedPointPointID" value="<?php echo $rp['AssignedPointPointID']; ?>">
                                    <button type="submit" name="manageVisited">Visit</button>
                                </td>
                            </form>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
            <?php else: ?>
                <p>No routes assigned to you at the moment.</p>
            <?php endif; ?>

            <!-- Add route -->
            <br><button id="showAddRouteButton" class="green-button">Add Route</button>

            <form id="addRouteForm" method="post" action="" class="addPointFormHidden">
                <label>Vehicle:</label>
                <select name="vehicleSelect" required>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?php echo $vehicle['VehicleID']; ?>">#<?php echo $vehicle['VehicleID'] . '; '
                                                                                    . $vehicle['VehicleBrand'] . '; '
                                                                                    . $vehicle['VehicleName']; ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label>Select Assigned Points:</label>
                <select name="pickedPoints[]" style="min-height: 200px;" multiple required>
                    <?php foreach ($assignedPoints as $asPoint): ?>
                        <option value="<?php echo $asPoint['AssignedPointPointID']; ?>"><?php echo $asPoint['PointCountry'] . '; '
                                                                                    . $asPoint['PointCity'] . '; '
                                                                                    . $asPoint['PointStreet']; ?></option>
                    <?php endforeach; ?>
                </select>

                <br><button type="submit" class="green-button" name="addRoute">Add Route</button>
            </form>

            <script>
                document.getElementById('showAddRouteButton').addEventListener('click', function() {
                    if(document.getElementById('addRouteForm').className == 'addPointFormVisible')
                        document.getElementById('addRouteForm').className = 'addPointFormHidden';
                    else
                        document.getElementById('addRouteForm').className = 'addPointFormVisible';
                });
            </script>

        <?php endif; ?>

        <!-- Logout Button -->
        <br><br><button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>