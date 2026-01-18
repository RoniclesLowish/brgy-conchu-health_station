<?php
include('dbConnection.php');

session_start();



if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pw = trim($_POST['password']);
    $password = md5($pw);

    
    // Check if the inputs are not empty
    if (!empty($email) && !empty($password)) {
        // Fetch user from the database
        $sql = "SELECT * FROM users WHERE email_add = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify the password
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("Location: users/index.php");
                // Redirect or start session here
            } else {
                $_SESSION['error'] = "Invalid password.";  // Set error message
                header("Location: index.php");  // Redirect back to the login page
                exit();
            }
        } else {
            $_SESSION['error'] = "No account found with this email.";  // Set error message
            header("Location: index.php");  // Redirect back to the login page
            exit();
        }
        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
}

if (isset($_POST['save_medicine_information'])) {

    // Check if the medicine already exists in the medicine_details table
    $checkQuery = "SELECT medicineID FROM medicine_details WHERE medicineName = ?";
    if ($checkStmt = $conn->prepare($checkQuery)) {
        $checkStmt->bind_param('s', $_POST['medicineName']);
        $checkStmt->execute();
        $checkStmt->store_result();

        // If medicineName exists, insert only into stock_details
        if ($checkStmt->num_rows > 0) {

            $checkStmt->bind_result($medicineID); // Bind the result to a variable
            $checkStmt->fetch();

            // Insert into stock_details only
            $insertStockQuery = "INSERT INTO stock_details (
                medicineID,
                quantityInStock,
                minimumStockLevel,
                batchNumber,
                expiryDate,
                manufacturingDate
            ) VALUES (?, ?, ?, ?, ?, ?)";

            if ($insertStockStmt = $conn->prepare($insertStockQuery)) {
                $insertStockStmt->bind_param(
                    'ssssss',
                    $medicineID,
                    $_POST['quantity'],
                    $_POST['minStockLevel'],
                    $_POST['batchNumber'],
                    $_POST['expiryDate'],
                    $_POST['manufacturingDate']
                );

                if ($insertStockStmt->execute()) {
                    $_SESSION['msg'] = "Stock details saved successfully.";
                } else {
                    $_SESSION['error'] = "Failed to save stock details: " . $insertStockStmt->error;
                }

                $insertStockStmt->close();
            } else {
                $_SESSION['error'] = "Failed to prepare stock details insert statement: " . $conn->error;
            }

        } else {
            // Insert into medicine_details and then into stock_details
            $insertMedicineQuery = "INSERT INTO medicine_details (
                medicineName,
                genericName,
                brandName,
                category,
                dosageForm,
                strength,
                description
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            if ($insertMedicineStmt = $conn->prepare($insertMedicineQuery)) {
                $insertMedicineStmt->bind_param(
                    'sssssss',
                    $_POST['medicineName'],
                    $_POST['genericName'],
                    $_POST['brandName'],
                    $_POST['category'],
                    $_POST['dosageForm'],
                    $_POST['strength'],
                    $_POST['description']
                );

                if ($insertMedicineStmt->execute()) {
                    // Get the last inserted medicineID
                    $medicineID = $insertMedicineStmt->insert_id; // This is the last inserted ID (medicineID)

                    // Insert into stock_details using the last inserted medicineID
                    $insertStockQuery = "INSERT INTO stock_details (
                        medicineID,
                        quantityInStock,
                        minimumStockLevel,
                        batchNumber,
                        expiryDate,
                        manufacturingDate
                    ) VALUES (?, ?, ?, ?, ?, ?)";

                    if ($insertStockStmt = $conn->prepare($insertStockQuery)) {
                        $insertStockStmt->bind_param(
                            'siisss',
                            $medicineID,  // Use the last inserted medicineID
                            $_POST['quantity'],
                            $_POST['minStockLevel'],
                            $_POST['batchNumber'],
                            $_POST['expiryDate'],
                            $_POST['manufacturingDate']
                        );

                        if ($insertStockStmt->execute()) {
                            $_SESSION['msg'] = "Stock details saved successfully.";
                        } else {
                            $_SESSION['error'] = "Failed to save stock details: " . $insertStockStmt->error;
                        }

                        $insertStockStmt->close();
                    } else {
                        $_SESSION['error'] = "Failed to prepare stock details insert statement: " . $conn->error;
                    }

                } else {
                    $_SESSION['error'] = "Failed to save medicine details: " . $insertMedicineStmt->error;
                }

                $insertMedicineStmt->close();
            } else {
                $_SESSION['error'] = "Failed to prepare medicine details insert statement: " . $conn->error;
            }
        }
        
    } else {
        $_SESSION['error'] = "Failed to prepare check statement: " . $conn->error;
    }
    $checkStmt->close();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

}


if (isset($_POST['update_medicine_information'])) {
    // Retrieve POST data
    $stockID = $_POST['sID'];  // stockID to uniquely identify the stock record
    $medicineID = $_POST['medicineID'];  // ID of the medicine
    $medicineName = $_POST['medicineName'];
    $genericName = $_POST['genericName'];
    $brandName = $_POST['brandName'];
    $category = $_POST['category'];
    $dosageForm = $_POST['dosageForm'];
    $strength = $_POST['strength'];
    $description = $_POST['description'];
    $quantityInStock = $_POST['quantity'];
    $minStockLevel = $_POST['minStockLevel'];
    $batchNumber = $_POST['batchNumber'];
    $expiryDate = $_POST['expiryDate'];
    $manufacturingDate = $_POST['manufacturingDate'];

    // Check if quantityInStock is updated
    if (isset($quantityInStock) && $quantityInStock !== "") {
        // Update only stock details if quantityInStock is updated
        $updateStockQuery = "UPDATE stock_details SET 
            quantityInStock = ?, 
            minimumStockLevel = ?, 
            batchNumber = ?, 
            expiryDate = ?, 
            manufacturingDate = ? 
        WHERE id = ?";  // Use stockID to update stock details
        $stmt = $conn->prepare($updateStockQuery);
        $stmt->bind_param("iisssi", $quantityInStock, $minStockLevel, $batchNumber, $expiryDate, $manufacturingDate, $stockID);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Stock record updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating stock details: " . $stmt->error;
        }
    }

    // Update medicine details only if other fields are updated (medicine details)
    if (!empty($medicineName) || !empty($genericName) || !empty($brandName) || !empty($category) || !empty($dosageForm) || !empty($strength) || !empty($description)) {
        $updateMedicineQuery = "UPDATE medicine_details SET 
            medicineName = ?, 
            genericName = ?, 
            brandName = ?, 
            category = ?, 
            dosageForm = ?, 
            strength = ?, 
            description = ? 
        WHERE medicineID = ?";

        $stmt = $conn->prepare($updateMedicineQuery);
        $stmt->bind_param("sssssssi", $medicineName, $genericName, $brandName, $category, $dosageForm, $strength, $description, $medicineID);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Medicine record updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating medicine details: " . $stmt->error;
        }
    }

    $stmt->close();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}



if (isset($_GET['delete_med'])) {
    // Get the medicineID from the URL
    $medicineID = $_GET['delete_med'];

    if (is_numeric($medicineID)) {
        $deleteQuery = "DELETE FROM stock_details WHERE id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($deleteQuery)) {
            // Bind the medicineID to the statement
            $stmt->bind_param("i", $medicineID);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Record deleted successfully.";
            } else {
                $_SESSION['error'] = "Error deleting record: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid medicineID.";
    }

    // Redirect to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['change_active_que'])) {

    // Step 1: Set the current active queue to "done"
    $update_active_sql = "UPDATE queue_number 
                          SET status = 'done' 
                          WHERE status = 'active' 
                            AND DATE(created_at) = CURDATE() 
                          LIMIT 1";
    $conn->query($update_active_sql);

    // Step 2: Set the next queue to "active" with priority first
    $update_next_sql = "UPDATE queue_number 
                        SET status = 'active' 
                        WHERE (status = 'waiting' OR status = 'pending') 
                          AND DATE(created_at) = CURDATE()
                        ORDER BY priority DESC, id ASC
                        LIMIT 1";
    $conn->query($update_next_sql);

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}



if (isset($_POST['save_patient_information'])) {

        $firstName = $conn->real_escape_string($_POST['firstName']);
        $middleName = isset($_POST['middleName']) ? $conn->real_escape_string($_POST['middleName']) : '';
        $lastName = $conn->real_escape_string($_POST['lastName']);
        $dateOfBirth = $conn->real_escape_string($_POST['dateOfBirth']);
        $sex = $conn->real_escape_string($_POST['sex']);
        $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
        $address = $conn->real_escape_string($_POST['address']);
        $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
        $is_pwd = $conn->real_escape_string($_POST['is_pwd']);
    
        // Check for duplicate entry
        $checkQuery = "SELECT * FROM patient_information WHERE firstName = '$firstName' AND lastName = '$lastName'";
        $result = $conn->query($checkQuery);
    
        if ($result->num_rows > 0) {
            // If duplicate found
            $_SESSION['error'] = "Patient information already exists.";
        } else {
            // Insert query
            $sql = "INSERT INTO patient_information (firstName, middleName, lastName, dateOfBirth, gender, contactNumber, emailAddress, address, is_pwd) 
                    VALUES ('$firstName', '$middleName', '$lastName', '$dateOfBirth', '$sex', '$contactNumber', '$emailAddress', '$address' ,'$is_pwd')";
    
            // Execute query and check for success
            if ($conn->query($sql) === TRUE) {
                $_SESSION['msg'] = "New patient information added successfully.";
            } else {
                $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    
        // Redirect back to the previous page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
}

if (isset($_GET['delete_patient'])) {
    // Get the medicineID from the URL
    $patientID = $_GET['delete_patient'];

    if (is_numeric($patientID)) {
        $deleteQuery = "DELETE FROM patient_information WHERE patientID = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($deleteQuery)) {
            // Bind the medicineID to the statement
            $stmt->bind_param("i", $patientID);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Record deleted successfully.";
            } else {
                $_SESSION['error'] = "Error deleting record: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid medicineID.";
    }

    // Redirect to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['update_patient_information'])) {
    // var_dump($_POST).die;
    // Get the data from the form and escape special characters to prevent SQL injection
    $pID = $conn->real_escape_string($_POST['pID']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $middleName = isset($_POST['middleName']) ? $conn->real_escape_string($_POST['middleName']) : '';
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $dateOfBirth = $conn->real_escape_string($_POST['dateOfBirth']);
    $sex = $conn->real_escape_string($_POST['sex']);
    $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
    $address = $conn->real_escape_string($_POST['address']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $is_pwd = $conn->real_escape_string($_POST['is_pwd']);

    // Check if the patient exists (using the pID to check uniqueness)
    $checkQuery = "SELECT * FROM patient_information WHERE patientID = '$pID'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // Update the patient's information if the record exists
        $updateQuery = "UPDATE patient_information 
                        SET firstName = '$firstName', middleName = '$middleName', lastName = '$lastName', 
                            dateOfBirth = '$dateOfBirth', gender = '$sex', contactNumber = '$contactNumber', 
                            emailAddress = '$emailAddress', address = '$address', is_pwd = $is_pwd 
                        WHERE patientID = '$pID'";

        // Execute the update query
        if ($conn->query($updateQuery) === TRUE) {
            $_SESSION['msg'] = "Patient information updated successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $updateQuery . "<br>" . $conn->error;
        }
    } else {
        // If the patient does not exist
        $_SESSION['error'] = "Patient not found.";
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


if (isset($_POST['save_medHistory'])) {

    // Sanitize input data to prevent SQL injection
    $patientID = mysqli_real_escape_string($conn, $_POST['patientID']);
    $allergies = mysqli_real_escape_string($conn, $_POST['allergies']);
    $chronicConditions = mysqli_real_escape_string($conn, $_POST['chronicConditions']);
    $medications = mysqli_real_escape_string($conn, $_POST['medications']);
    $familyHistory = mysqli_real_escape_string($conn, $_POST['familyHistory']);

    // SQL Insert query - Ensure $patientID is enclosed in quotes since it's a string
    $sql = "INSERT INTO medical_history (patientID, allergies, chronicDiseases, medications, familyMedicalHistory) 
            VALUES ('$patientID', '$allergies', '$chronicConditions', '$medications', '$familyHistory')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        $_SESSION['msg'] = "Medical History added successfully.";
    } else {
        $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'view_medical_history') {
    $patientID = $_GET['patientID'];

    // SQL query to fetch the medical history for the given patientID
    $sql = "SELECT patientID, allergies, chronicDiseases, medications, familyMedicalHistory FROM medical_history WHERE patientID = '$patientID'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if data was found
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Return the data as JSON
        echo json_encode($row);
    } else {
        echo json_encode(array('error' => 'No medical history found for this patient.'));
    }

    exit();
}

if (isset($_POST['save_emerContact'])) {

    // Sanitize input data to prevent SQL injection
    $patientID = mysqli_real_escape_string($conn, $_POST['patientID']);
    $emergencyContactName = mysqli_real_escape_string($conn, $_POST['emergencyContactName']);
    $emergencyContactRelation = mysqli_real_escape_string($conn, $_POST['emergencyContactRelation']);
    $emergencyContactNumber = mysqli_real_escape_string($conn, $_POST['emergencyContactNumber']);

    // SQL Insert query - Ensure $patientID is enclosed in quotes since it's a string
    $sql = "INSERT INTO emergency_contact (patientID, emergencyContactName, emergencyContactRelation, emergencyContactNumber) 
            VALUES ('$patientID', '$emergencyContactName', '$emergencyContactRelation', '$emergencyContactNumber')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        $_SESSION['msg'] = "Emergency Contact added successfully.";
    } else {
        $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'view_emergency_contact') {
    $patientID = $_GET['patientID'];

    // SQL query to fetch the medical history for the given patientID
    $sql = "SELECT patientID, emergencyContactName, emergencyContactRelation, emergencyContactNumber FROM emergency_contact WHERE patientID = '$patientID'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if data was found
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Return the data as JSON
        echo json_encode($row);
    } else {
        echo json_encode(array('error' => 'No Emergency Contact found for this patient.'));
    }

    exit();
}

if (isset($_POST['update_emerContact'])) {
    // Get the data from the form and escape special characters to prevent SQL injection
    $pID = $conn->real_escape_string($_POST['patientID']);
    $emergencyContactName = $conn->real_escape_string($_POST['emergencyContactName']);
    $emergencyContactRelation = $conn->real_escape_string($_POST['emergencyContactRelation']);
    $emergencyContactNumber = $conn->real_escape_string($_POST['emergencyContactNumber']);
 

    // Check if the patient exists (using the pID to check uniqueness)
    $checkQuery = "SELECT * FROM emergency_contact WHERE patientID = '$pID'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // Update the patient's information if the record exists
        $updateQuery = "UPDATE emergency_contact 
                        SET emergencyContactName = '$emergencyContactName', emergencyContactRelation = '$emergencyContactRelation', emergencyContactNumber = '$emergencyContactNumber'
                        WHERE patientID = '$pID'";

        // Execute the update query
        if ($conn->query($updateQuery) === TRUE) {
            $_SESSION['msg'] = "Emergency Contact updated successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $updateQuery . "<br>" . $conn->error;
        }
    } else {
        // If the patient does not exist
        $_SESSION['error'] = "Patient not found.";
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_GET['delete_emerContact'])) {
    // Get the medicineID from the URL
    $patientID = $_GET['delete_emerContact'];

    if (is_numeric($patientID)) {
        $deleteQuery = "DELETE FROM emergency_contact WHERE patientID = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($deleteQuery)) {
            // Bind the medicineID to the statement
            $stmt->bind_param("i", $patientID);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Record deleted successfully.";
            } else {
                $_SESSION['error'] = "Error deleting record: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid patient ID.";
    }

    // Redirect to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['change_pass'])) {

    $user_id = $conn->real_escape_string($_POST['user_id']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    $new_pass = md5($confirm_password);
        // Update the patient's information if the record exists
        $updateQuery = "UPDATE users 
                        SET password = '$new_pass' WHERE id = '$user_id'";

        // Execute the update query
        if ($conn->query($updateQuery) === TRUE) {
            $_SESSION['msg'] = "Password updated successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $updateQuery . "<br>" . $conn->error;
        }


    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['update_accInformation'])) {
   
    $user_id = $conn->real_escape_string($_POST['user_id']);
    // var_dump($user_id).die;
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $email_add = $conn->real_escape_string($_POST['email_add']);
    $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
    $jobPosition = $conn->real_escape_string($_POST['jobPosition']);
    $AboutMe = $conn->real_escape_string($_POST['AboutMe']);


        // Update the patient's information if the record exists
        $updateQuery = "UPDATE account_information SET firstname = '$firstname', lastname = '$lastname', contactNumber = '$contactNumber', jobPosition = '$jobPosition', AboutMe = '$AboutMe' WHERE unq_id = '$user_id'";

        $updateQuery2 = "UPDATE users SET email_add = '$email_add' WHERE id = '$user_id'";

        // Execute the update query
        if ($conn->query($updateQuery) === TRUE && $conn->query($updateQuery2) === TRUE) {
            $_SESSION['msg'] = "Records updated successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $updateQuery . "<br>" . $conn->error;
        }


    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['save_account_information'])) {

    // var_dump($_POST).die;
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $middleName = isset($_POST['middleName']) ? $conn->real_escape_string($_POST['middleName']) : '';
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $address = $conn->real_escape_string($_POST['address']);
    $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
    $jobPosition = $conn->real_escape_string($_POST['jobPosition']);
    $role = $conn->real_escape_string($_POST['role']);
    $password = md5('password');

    // Check for duplicate entry
    $checkQuery = "SELECT * FROM account_information WHERE firstname = '$firstName' AND lastname = '$lastName'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // If duplicate found
        $_SESSION['error'] = "Account information already exists.";
    } else {
        // Insert query for users table
        $sql = "INSERT INTO users (email_add, role, password) 
        VALUES ('$emailAddress', '$role', '$password')";

        // Execute query and check for success
        if ($conn->query($sql) === TRUE) {
        // Get the last inserted ID from the users table
        $last_id = $conn->insert_id;

        // Insert query for account_information table
        $sql = "INSERT INTO account_information (unq_id, firstname, middlename, lastname, address, contactNumber, jobPosition) 
            VALUES ('$last_id', '$firstName', '$middleName', '$lastName', '$address', '$contactNumber', '$jobPosition')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['msg'] = "Account information added successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
        }
        } else {
            $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
        }

    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['change_active_que_prev'])) {

    $get_active_sql = "SELECT id FROM queue_number WHERE status = 'active' AND DATE(created_at) = CURDATE() LIMIT 1";
    $result = $conn->query($get_active_sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_active_id = $row['id'];

        $update_active_sql = "UPDATE queue_number SET status = 'waiting' WHERE id = $current_active_id AND DATE(created_at) = CURDATE()";
        $conn->query($update_active_sql);

        //  Find the previous queue and set it to "active"
        $update_previous_sql = "UPDATE queue_number 
                                SET status = 'active' 
                                WHERE id = (SELECT MAX(id) FROM queue_number WHERE id > $current_active_id AND status = 'waiting') AND DATE(created_at) = CURDATE() OR id > $current_active_id AND status = 'pending') AND DATE(created_at) = CURDATE()";
        $conn->query($update_previous_sql);
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

}


if (isset($_POST['active_now'])) {
    // var_dump($_POST['stand_by_id']).die;
    // Step 1: Set the current active queue to "done"

    $stand_by_id = $_POST['stand_by_id'];
    $get_active_sql = "SELECT id FROM queue_number WHERE status = 'active' AND DATE(created_at) = CURDATE() LIMIT 1";
    $result = $conn->query($get_active_sql);

    // Step 2: Set the next waiting queue to "active"
    $update_next_sql = "UPDATE queue_number 
                        SET status = 'active' 
                        WHERE id = $stand_by_id
                        ORDER BY id ASC LIMIT 1";
    $conn->query($update_next_sql);


    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_active_id = $row['id'];

        $update_active_sql = "UPDATE queue_number SET status = 'pending' WHERE id = $current_active_id AND DATE(created_at) = CURDATE()";
        $conn->query($update_active_sql);

    }
    

   header("Location: " . $_SERVER['HTTP_REFERER']);
   exit();

}

if (isset($_POST['change_active_que_standby'])) {

    $get_active_sql = "SELECT id FROM queue_number WHERE status = 'active' AND DATE(created_at) = CURDATE() LIMIT 1";
    $result = $conn->query($get_active_sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_active_id = $row['id'];

        $update_active_sql = "UPDATE queue_number SET status = 'pending' WHERE id = $current_active_id AND DATE(created_at) = CURDATE()";
        $conn->query($update_active_sql);

        //  Find the previous queue and set it to "active"
        $update_previous_sql = "UPDATE queue_number 
                            SET status = 'active' 
                            WHERE status = 'waiting' AND id != $current_active_id AND DATE(created_at) = CURDATE()
                            ORDER BY id ASC LIMIT 1";
        $conn->query($update_previous_sql);
    }


    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

}

if (isset($_POST['save_medCategory_information'])) {

    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);


    // Check for duplicate entry
    $checkQuery = "SELECT * FROM medicine_category WHERE category = '$category'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // If duplicate found
        $_SESSION['error'] = "Medical Category already exists.";
    } else {
        // Insert query
        $sql = "INSERT INTO medicine_category (category, description) 
                VALUES ('$category', '$description')";

        // Execute query and check for success
        if ($conn->query($sql) === TRUE) {
            $_SESSION['msg'] = "New category added successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']); // Sanitize the ID

    // Delete from both tables
    $query1 = "DELETE FROM users WHERE id = $userId";
    $query2 = "DELETE FROM account_information WHERE unq_id = $userId";

    $result1 = $conn->query($query1);
    $result2 = $conn->query($query2);

    // Check results
    if ($result1 && $result2) {
        $_SESSION['msg'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting user: " . $conn->error;
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


if (isset($_POST['update_account_information']) && isset($_POST['user_id'])) {

   // var_dump($_POST).die;
    $userId = intval($_POST['user_id']); // Use hidden input from modal

    $firstName = $conn->real_escape_string($_POST['firstName']);
    $middleName = isset($_POST['middleName']) ? $conn->real_escape_string($_POST['middleName']) : '';
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $address = $conn->real_escape_string($_POST['address']);
    $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
    $jobPosition = $conn->real_escape_string($_POST['jobPosition']);
    $role = $conn->real_escape_string($_POST['role']);

    // Optional: Check for duplicates excluding current record
    $checkQuery = "SELECT * FROM account_information 
                   WHERE firstname = '$firstName' AND lastname = '$lastName' AND unq_id != $userId";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Another account with the same name already exists.";
    } else {
        // Update users table
        $sql1 = "UPDATE users SET email_add = '$emailAddress', role = '$role' WHERE id = $userId";
        $result1 = $conn->query($sql1);

        // Update account_information table
        $sql2 = "UPDATE account_information 
                 SET firstname = '$firstName', middlename = '$middleName', lastname = '$lastName', 
                     address = '$address', contactNumber = '$contactNumber', jobPosition = '$jobPosition' 
                 WHERE unq_id = $userId";
        $result2 = $conn->query($sql2);

        if ($result1 && $result2) {
            $_SESSION['msg'] = "Account information updated successfully.";
        } else {
            $_SESSION['error'] = "Update failed: " . $conn->error;
        }
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_GET['delete_patient_medHistory'])) {
    // Get the medicineID from the URL
    $patientID = $_GET['delete_patient_medHistory'];
    if (is_numeric($patientID)) {
        $deleteQuery = "DELETE FROM medical_history WHERE patientID = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($deleteQuery)) {
            // Bind the medicineID to the statement
            $stmt->bind_param("i", $patientID);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Record deleted successfully.";
            } else {
                $_SESSION['error'] = "Error deleting record: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid medicineID.";
    }

    // Redirect to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (isset($_POST['save_medPatient'])) {

    // Sanitize inputs
    $patientID = mysqli_real_escape_string($conn, $_POST['patientID']);
    $patientName = mysqli_real_escape_string($conn, $_POST['emergencyContactName']);
    $medicineID = mysqli_real_escape_string($conn, $_POST['medicine_patient']);
    $qty = (int) $_POST['qty'];

    // Validate quantity
    if ($qty <= 0) {
        echo "<script>alert('Invalid quantity.'); window.history.back();</script>";
        exit;
    }

    // Get current stock
    $stockQuery = "SELECT quantityInStock FROM stock_details WHERE medicineID = '$medicineID'";
    $stockResult = $conn->query($stockQuery);

    if ($stockResult && $stockResult->num_rows > 0) {
        $row = $stockResult->fetch_assoc();
        $currentStock = (int) $row['quantityInStock'];

        if ($qty > $currentStock) {
            echo "<script>alert('Not enough stock available!'); window.history.back();</script>";
            exit;
        }

        // 1️⃣ Insert record into medicine_to_patient
        $insertQuery = "
            INSERT INTO medicine_to_patient (patientID, medicineID, qty)
            VALUES ('$patientID', '$medicineID', '$qty')
        ";

        if ($conn->query($insertQuery)) {
            // 2️⃣ Update the stock
            $newStock = $currentStock - $qty;
            $updateStock = "
                UPDATE stock_details
                SET quantityInStock = '$newStock'
                WHERE medicineID = '$medicineID'
            ";
            $conn->query($updateStock);

            echo "<script>alert('Medicine successfully given to patient!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error saving record.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No stock record found for this medicine.'); window.history.back();</script>";
    }
}

if (isset($_POST['save_referral'])) {

    // checkbox arrays
    $type = isset($_POST['type']) ? implode(", ", $_POST['type']) : '';
    $reason = isset($_POST['reason']) ? implode(", ", $_POST['reason']) : '';

    // form values
    $hosp_name = $_POST['hosp_name'];
    $hosp_add = $_POST['hosp_add'];
    $referred_to = $_POST['referred_to'];
    $date_time = $_POST['date_time'];

    $patient_name = $_POST['patient_name'];
    $patient_age = $_POST['patient_age'];
    $sex = $_POST['sex'];
    $patient_address = $_POST['patient_address'];

    $chief_complaint = $_POST['chief_complaint'];
    $med_history = $_POST['med_history'];

    $surgical = $_POST['surgical'] ?? '';
    $if_yes_surgical = $_POST['if_yes_surgical'];

    $BP = $_POST['BP'];
    $HR = $_POST['HR'];
    $RR = $_POST['RR'];
    $WT = $_POST['WT'];

    $impression = $_POST['impression'];
    $action_taken = $_POST['action_taken'];

    $insurance = $_POST['insurance'] ?? '';
    $coverage_type = $_POST['coverage_type'];

    $reason_others = $_POST['reason_others'];

    // SQL Insert
    $sql = "INSERT INTO referrals (
        hosp_name, hosp_add, type,
        referred_to, date_time,
        patient_name, patient_age, sex, patient_address,
        chief_complaint, med_history,
        surgical, if_yes_surgical,
        BP, HR, RR, WT,
        impression, action_taken,
        insurance, coverage_type,
        reason, reason_others
    ) VALUES (
        '$hosp_name', '$hosp_add', '$type',
        '$referred_to', '$date_time',
        '$patient_name', '$patient_age', '$sex', '$patient_address',
        '$chief_complaint', '$med_history',
        '$surgical', '$if_yes_surgical',
        '$BP', '$HR', '$RR', '$WT',
        '$impression', '$action_taken',
        '$insurance', '$coverage_type',
        '$reason', '$reason_others'
    )";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Referral saved successfully!'); window.location.href='users/referral_form.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}












$conn->close();
?>
