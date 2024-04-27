<?php
// Function to retrieve all drivers from the database
function getAllDrivers() {
    global $conn;
    $sql = "SELECT * FROM Employees WHERE EmployeeRole = 'Driver' AND DateDeleted IS NULL";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to retrieve all available (not taken) points
function getAvailablePoints() {
    global $conn;

    $sql = "SELECT * FROM Points WHERE DateDeleted IS NULL";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to retrieve assigned points for a specific driver
function getAssignedPoints($driverID) {
    global $conn;
    $sql = "SELECT * FROM Assigned_Points ap
            INNER JOIN Points p ON ap.AssignedPointPointID = p.PointID
            INNER JOIN Employees e ON ap.AssignedPointDriverID = e.EmployeeID
            WHERE ap.AssignedPointDriverID = $driverID AND ap.DateDeleted IS NULL";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to assign a point to a driver
function assignPoint($pointID, $driverID) {
    global $conn;

    $assignSql = "INSERT INTO Assigned_Points (AssignedPointPointID, AssignedPointDriverID, DateCreated) VALUES ($pointID, $driverID, NOW())";
    $conn->query($assignSql);
}

function deleteAssignedPoint($assignedPointID) {
    global $conn;
    $sql = "UPDATE Assigned_Points SET DateDeleted=NOW() WHERE AssignedPointID=$assignedPointID";
    $conn->query($sql);
}

// Function to retrieve an assigned point by its ID
function getAssignedPointById($assignedPointID) {
    global $conn;
    $sql = "SELECT ap.*, e.EmployeeFirstName, e.EmployeeLastName, e.EmployeeRole FROM Assigned_Points ap
        INNER JOIN Employees e ON ap.AssignedPointDriverID = e.EmployeeID
        WHERE ap.AssignedPointID = $assignedPointID AND ap.DateDeleted IS NULL";
    return $conn->query($sql);
}

// Function to update an assigned point in the database
function updateAssignedPoint($assignedPointID, $driverID) {
    global $conn;
    $sql = "UPDATE Assigned_Points SET AssignedPointDriverID=$driverID, DateEdited=NOW() WHERE AssignedPointID=$assignedPointID";
    $conn->query($sql);
}
?>