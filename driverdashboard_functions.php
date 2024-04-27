<?php
function getAssignedPoints() {
    global $driver;
    $driverID = $driver['EmployeeID'];
    global $conn;

    $sql = "SELECT * FROM Assigned_Points ap
            INNER JOIN Points p ON ap.AssignedPointPointID = p.PointID
            WHERE ap.AssignedPointDriverID = $driverID
            AND ap.IsAssignedPointTaken = 0
            AND ap.DateDeleted IS NULL";

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getVehicles() {
    global $conn;

    $sql = "SELECT * FROM Vehicles
            WHERE DateDeleted IS NULL AND IsVehicleFree = 1";

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getDriverRoutes() {
    global $driver;
    $driverID = $driver['EmployeeID'];
    global $conn;

    $sql = "SELECT * FROM Routes r
            INNER JOIN Employees e ON r.RouteDriverID = e.EmployeeID
            INNER JOIN Vehicles v ON r.RouteVehicleID = v.VehicleID
            WHERE RouteDriverID = $driverID";

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRoutePoints($routeID) {
    global $conn;

    $sql = "SELECT * FROM Assigned_Points ap
    INNER JOIN Points p ON ap.AssignedPointPointID = p.PointID
    WHERE ap.AssignedPointRouteID = $routeID
    AND ap.DateDeleted IS NULL";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addNewRoute() {
    global $driver;
    global $conn;

    $driverID = $driver['EmployeeID'];
    $vehicleID = $_POST['vehicleSelect'];
    $totalPoints = count($_POST['pickedPoints']);

    $sql = "INSERT INTO Routes (RouteDriverID, RouteVehicleID, RouteStatus, RouteTotalPoints, DateCreated)
            VALUES ($driverID, $vehicleID, 'In Progress', $totalPoints, NOW())";
    $conn->query($sql);

    $sql = "UPDATE Vehicles SET IsVehicleFree = 0 WHERE VehicleID = $vehicleID";
    $conn->query($sql);

    $sql = "UPDATE Employees SET IsEmployeeFree = 0 WHERE EmployeeID = $driverID AND EmployeeRole = 'Driver' AND DateDeleted IS NULL";
    $conn->query($sql);

    foreach($_POST['pickedPoints'] as $pointID) {
        $sql = "UPDATE Assigned_Points SET IsAssignedPointTaken = 1 WHERE AssignedPointPointID = $pointID AND DateDeleted IS NULL";
        $conn->query($sql);

        $sql = "UPDATE Assigned_Points SET AssignedPointRouteID = (SELECT MAX(RouteID) FROM Routes) WHERE AssignedPointPointID = $pointID AND DateDeleted IS NULL AND IsAssignedPointTaken = 1";
        $conn->query($sql);
    }

}

function setVisited($assignedPointPointID) {
    global $conn;

    $sql = "UPDATE Assigned_Points SET IsAssignedPointVisited = 1 WHERE AssignedPointPointID = $assignedPointPointID AND DateDeleted IS NULL";
    $conn->query($sql);

    checkIfRouteDone($assignedPointPointID);
}

function checkIfRouteDone($assignedPointPointID) {
    global $conn;

    // Get route ID
    $sql = "SELECT AssignedPointRouteID FROM Assigned_Points WHERE AssignedPointPointID = $assignedPointPointID AND DateDeleted IS NULL";
    $result = $conn->query($sql);
    $routeID = $result->fetch_assoc()['AssignedPointRouteID'];

    // Get total points
    $sql = "SELECT RouteTotalPoints FROM Routes WHERE RouteID = $routeID";
    $result = $conn->query($sql);
    $totalPoints = $result->fetch_assoc()['RouteTotalPoints'];

    // Get visited points
    $sql = "SELECT COUNT(*) FROM Assigned_Points WHERE AssignedPointRouteID = $routeID AND IsAssignedPointVisited = 1";
    $result = $conn->query($sql);
    $visitedPoints = $result->fetch_assoc()['COUNT(*)'];

    // Compare
    if($totalPoints == $visitedPoints) {
        $sql = "UPDATE Routes SET RouteStatus = 'Completed', RouteDateCompleted = NOW() WHERE RouteID = $routeID";
        $conn->query($sql);

        $sql = "UPDATE Vehicles SET IsVehicleFree = 1 WHERE VehicleID = (SELECT RouteVehicleID FROM Routes WHERE RouteID = $routeID)";
        $conn->query($sql);

        $sql = "UPDATE Employees SET IsEmployeeFree = 1 WHERE EmployeeID = (SELECT RouteDriverID FROM Routes WHERE RouteID = $routeID)";
        $conn->query($sql);

        // $sql = "UPDATE Assigned_Points SET IsAssignedPointTaken = 0 WHERE AssignedPointRouteID = $routeID";
        // $conn->query($sql);

        // $sql = "UPDATE Assigned_Points SET AssignedPointRouteID = NULL WHERE AssignedPointRouteID = $routeID";
        // $conn->query($sql);

        // $sql = "UPDATE Assigned_Points SET IsAssignedPointVisited = 0 WHERE AssignedPointRouteID = $routeID";
        // $conn->query($sql);
        
        //delete assigned point
        $sql = "UPDATE Assigned_Points SET DateDeleted = NOW() WHERE AssignedPointRouteID = $routeID";
        $conn->query($sql);
    }
}
?>