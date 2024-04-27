<?php
// Function to retrieve all points from the database
function getAllPoints() {
    global $conn;
    $sql = "SELECT * FROM Points WHERE DateDeleted IS NULL";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new point to the database
function addPoint($country, $city, $postalCode, $street, $buildingNumber, $additionalInfo) {
    global $conn;
    $sql = "INSERT INTO Points (PointCountry, PointCity, PointPostalCode, PointStreet, PointBuildingNumber, PointAdditionalInfo, DateCreated) VALUES ('$country', '$city', '$postalCode', '$street', '$buildingNumber', '$additionalInfo', NOW())";
    $conn->query($sql);
}

// Function that returns true or false depending if the point is assigned to driver or not
function isPointAssignedToDriver($pointID){
    global $conn;
    $sql = "SELECT * FROM Assigned_Points WHERE AssignedPointPointID = $pointID AND DateDeleted IS NULL";
    $result = $conn->query($sql);
    if($result->num_rows > 0)
        return true;
    else
        return false;
}

// Function to update an existing point in the database
function updatePoint($pointID, $country, $city, $postalCode, $street, $buildingNumber, $additionalInfo) {
    global $conn;
    $sql = "UPDATE Points SET PointCountry='$country', PointCity='$city', PointPostalCode='$postalCode', PointStreet='$street', PointBuildingNumber='$buildingNumber', PointAdditionalInfo='$additionalInfo', DateEdited=NOW() WHERE PointID=$pointID";
    $conn->query($sql);
}

// Function to delete a point from the database
function deletePoint($pointID) {
    global $conn;
    //$sql = "DELETE FROM Points WHERE PointID=$pointID";
    $sql = "UPDATE Points SET DateDeleted=NOW() WHERE PointID=$pointID";
    $conn->query($sql);
}

// Function to retrieve a point by its ID
function getPointById($pointID) {
    global $conn;
    $sql = "SELECT * FROM Points WHERE PointID = $pointID AND DateDeleted IS NULL";
    return $conn->query($sql);
}
?>