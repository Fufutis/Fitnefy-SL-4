<?php
// Initialize variables
$firstNumber = '';
$secondNumber = '';
$result = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $firstNumber = isset($_POST['firstNumber']) ? $_POST['firstNumber'] : '';
    $secondNumber = isset($_POST['secondNumber']) ? $_POST['secondNumber'] : '';
    $operation = isset($_POST['operation']) ? $_POST['operation'] : '';

    // Convert input values to floats
    $num1 = floatval($firstNumber);
    $num2 = floatval($secondNumber);

    // Perform the selected operation
    switch ($operation) {
        case "Addition":
            $result = $num1 + $num2;
            break;
        case "Subtract":
            $result = $num1 - $num2;
            break;
        case "Multiply":
            $result = $num1 * $num2;
            break;
        case "Divide":
            if ($num2 != 0) {
                $result = $num1 / $num2;
            } else {
                $result = "Cannot divide by zero";
            }
            break;
        case "Mod":
            if ($num2 != 0) {
                $result = $num1 % $num2;
            } else {
                $result = "Cannot mod by zero";
            }
            break;
        case "Clear":
            $firstNumber = '';
            $secondNumber = '';
            $result = '';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Calculator</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">PHP Calculator</h1>
        <form method="post" action="" class="row g-4">
            <!-- Input Fields -->
            <div class="col-md-6">
                <label for="firstNumber" class="form-label">1st Number:</label>
                <input type="text" id="firstNumber" name="firstNumber" 
                       value="<?php echo htmlspecialchars($firstNumber); ?>" 
                       class="form-control bg-secondary text-light border-light">
            </div>
            <div class="col-md-6">
                <label for="secondNumber" class="form-label">2nd Number:</label>
                <input type="text" id="secondNumber" name="secondNumber" 
                       value="<?php echo htmlspecialchars($secondNumber); ?>" 
                       class="form-control bg-secondary text-light border-light">
            </div>
            <!-- Operation Buttons -->
            <div class="col-12 text-center">
                <button type="submit" name="operation" value="Clear" class="btn btn-outline-light me-2">Clear</button>
                <button type="submit" name="operation" value="Addition" class="btn btn-outline-primary me-2">Addition</button>
                <button type="submit" name="operation" value="Subtract" class="btn btn-outline-secondary me-2">Subtract</button>
                <button type="submit" name="operation" value="Multiply" class="btn btn-outline-success me-2">Multiply</button>
                <button type="submit" name="operation" value="Divide" class="btn btn-outline-warning me-2">Divide</button>
                <button type="submit" name="operation" value="Mod" class="btn btn-outline-danger">Mod</button>
            </div>
        </form>
        <!-- Result Display -->
        <div class="mt-4 text-center">
        <h4>Result:</h4>
            <div class="border border-light rounded bg-secondary p-3">
                
                <p class="display-6 text-light">
                    <?php echo htmlspecialchars($result); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
