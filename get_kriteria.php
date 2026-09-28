<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aspekId = $_POST['aspek_id'];

    $query = "SELECT * FROM kriteria WHERE id_aspek = $aspekId";
    $result = $conn->query($query);

    $kriteriaList = array();
    while ($row = $result->fetch_assoc()) {
        $kriteriaList[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($kriteriaList);
}
?>
