<?php
session_start();

// Initialize the game state
if (!isset($_SESSION['grid'])) {
    $_SESSION['grid'] = array_fill(0, 9, ''); // 9 empty cells
    $_SESSION['turn'] = 'X'; // X starts first
}

// Handle button clicks
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    for ($i = 0; $i < 9; $i++) {
        if (isset($_POST["btn-$i"]) && $_SESSION['grid'][$i] == '') {
            // Place the current player's mark
            $_SESSION['grid'][$i] = $_SESSION['turn'];
            // Alternate turns
            $_SESSION['turn'] = ($_SESSION['turn'] == 'X') ? 'O' : 'X';
            break;
        }
    }
}

// Check for a winner
$winner = whoIsWinner();
if ($winner) {
    $message = "The winner is $winner!";
    // Disable further play
    for ($i = 0; $i < 9; $i++) {
        if ($_SESSION['grid'][$i] == '') {
            $_SESSION['grid'][$i] = '-'; // Unchosen cells become blank
        }
    }
} elseif (!in_array('', $_SESSION['grid'])) {
    // If the grid is full and no winner, it's a draw
    $message = "It's a draw!";
}

function whoIsWinner()
{
    $winner = checkWhoHasTheSeries(['1-1', '2-1', '3-1']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['1-2', '2-2', '3-2']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['1-3', '2-3', '3-3']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['1-1', '1-2', '1-3']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['2-1', '2-2', '2-3']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['3-1', '3-2', '3-3']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['1-1', '2-2', '3-3']);
    if ($winner) return $winner;
    $winner = checkWhoHasTheSeries(['3-1', '2-2', '1-3']);
    if ($winner) return $winner;
    return null;
}

function checkWhoHasTheSeries($list)
{
    $values = [
        '1-1' => $_SESSION['grid'][0],
        '2-1' => $_SESSION['grid'][1],
        '3-1' => $_SESSION['grid'][2],
        '1-2' => $_SESSION['grid'][3],
        '2-2' => $_SESSION['grid'][4],
        '3-2' => $_SESSION['grid'][5],
        '1-3' => $_SESSION['grid'][6],
        '2-3' => $_SESSION['grid'][7],
        '3-3' => $_SESSION['grid'][8]
    ];
    
    $XCount = 0;
    $OCount = 0;
    foreach ($list as $value) {
        if ($values[$value] == 'X') {
            $XCount++;
        } elseif ($values[$value] == 'O') {
            $OCount++;
        }
    }

    if ($XCount == 3) return 'X';
    if ($OCount == 3) return 'O';
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic Tac Toe</title>
    <style>
        /* Style guide for buttons and grid */
        button {
            background-color: #3498db;
            height: 100%;
            width: 100%;
            text-align: center;
            font-size: 20px;
            color: white;
            border: 0px;
        }
        button:hover, button:focus {
            background-color: #04469d;
        }
        table td {
            text-align: center;
            vertical-align: middle;
            padding: 0px;
            margin: 0px;
            width: 75px;
            height: 75px;
            font-size: 20px;
            border: 3px solid #040404;
        }
        .green { color: green; }
        .red { color: red; }
    </style>
</head>
<body>

<h1>Tic Tac Toe</h1>
<p>Turn: <?= $_SESSION['turn'] ?></p>

<!-- Game grid -->
<form method="POST">
    <table>
        <?php for ($row = 0; $row < 3; $row++): ?>
        <tr>
            <?php for ($col = 0; $col < 3; $col++):
                $index = $row * 3 + $col;
                $value = $_SESSION['grid'][$index];
            ?>
            <td>
                <?php if ($value == ''): ?>
                    <button type="submit" name="btn-<?= $index ?>"></button>
                <?php elseif ($value == 'X'): ?>
                    <span class="green">X</span>
                <?php elseif ($value == 'O'): ?>
                    <span class="red">O</span>
                <?php endif; ?>
            </td>
            <?php endfor; ?>
        </tr>
        <?php endfor; ?>
    </table>
</form>

<!-- Display result if available -->
<?php if (isset($message)): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<!-- Reset button -->
<form method="POST">
    <button type="submit" name="reset">Reset</button>
</form>

<?php
// Reset the game when requested
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
}
?>

</body>
</html>
