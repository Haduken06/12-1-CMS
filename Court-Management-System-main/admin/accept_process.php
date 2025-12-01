<?php
include "../db_connect.php";

// Check if reservation ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    
    // Start transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // First, get the reservation data
        $select_query = "SELECT * FROM reservations WHERE reservation_id = ?";
        $stmt = $conn->prepare($select_query);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $reservation_data = $result->fetch_assoc();
            
            // Insert into accepted_table
            $insert_query = "INSERT INTO accepted_bookings (reservation_id, fullname, email, phonenumber, court_type, date, time_slot, created_at, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'accepted')";
            $stmt_insert = $conn->prepare($insert_query);
            $stmt_insert->bind_param("isssssss", 
                $reservation_data['reservation_id'],
                $reservation_data['fullname'],
                $reservation_data['email'],
                $reservation_data['phonenumber'],
                $reservation_data['court_type'],
                $reservation_data['date'],
                $reservation_data['time_slot'],
                $reservation_data['created_at']
            );
            $stmt_insert->execute();
            
            // Delete from reservations table
            $delete_query = "DELETE FROM reservations WHERE reservation_id = ?";
            $stmt_delete = $conn->prepare($delete_query);
            $stmt_delete->bind_param("i", $reservation_id);
            $stmt_delete->execute();
            
            // Commit transaction
            $conn->commit();
            
            // Redirect back to admin page with success message
            header("Location: reservations.php?message=Reservation accepted successfully&type=success");
            exit();
            
        } else {
            // Reservation not found
            header("Location: reservations.php?message=Reservation not found&type=error");
            exit();
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Redirect back with error message
        header("Location: reservations.php?message=Error accepting reservation: " . $e->getMessage() . "&type=error");
        exit();
    }
    
} else {
    // No ID provided
    header("Location: reservations.php?message=No reservation ID provided&type=error");
    exit();
}
?>