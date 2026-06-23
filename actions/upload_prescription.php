<?php
/**
 * Handles the security and moving of prescription files.
 * @param array $file The $_FILES['input_name'] array.
 * @return string The filename on success, or an empty string on failure.
 */
function uploadPrescription($file) {
    // Check if a file was actually uploaded without errors
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        
        $upload_dir = "../assets/uploads/prescriptions/";
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        
        // 1. Validate File Extension (Security)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            // 2. Create a unique filename to prevent overwriting
            $unique_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
            $target_path = $upload_dir . $unique_name;

            // 3. Move the file to the target directory
            if (move_uploaded_file($file_tmp, $target_path)) {
                return $unique_name;
            }
        }
    }
    return ""; // Return empty if no file or failed validation
}
?>