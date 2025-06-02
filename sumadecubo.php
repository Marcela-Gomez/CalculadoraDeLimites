<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Factorización de suma y diferencia de cubos</title>
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
      margin: 0; padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      background-color: rgba(0,0,0,0.6);
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      max-width: 400px;
      width: 100%;
    }
    h1 {
      text-align: center;
      margin-bottom: 24px;
      font-weight: 700;
      letter-spacing: 0.05em;
    }
    label {
      display: block;
      margin: 15px 0 6px;
      font-weight: 600;
    }
    input[type="number"], select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 8px;
      border: none;
      font-size: 16px;
      box-sizing: border-box;
      outline: none;
      transition: background-color 0.3s ease;
    }
    input[type="number"]:focus, select:focus {
      background-color: #eef4ff;
      color: #000;
    }
    button {
      margin-top: 25px;
      width: 100%;
      background-color: #28a745;
      border: none;
      padding: 12px;
      font-size: 18px;
      color: #fff;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 700;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #1e7e34;
    }
    .result {
      margin-top: 30px;
      padding: 18px;
      background-color: #1e1e1e;
      border-radius: 12px;
      font-size: 20px;
      font-weight: 600;
      text-align: center;
      word-wrap: break-word;
    }
    sup {
      font-size: 0.75em;
      vertical-align: super;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Factorización de Cubos</h1>
    <form method="post" action="">
      <label for="a">Valor a (base):</label>
      <input type="number" step="any" id="a" name="a" required value="<?php echo isset($_POST['a']) ? htmlspecialchars($_POST['a']) : ''; ?>" />

      <label for="b">Valor b (base):</label>
      <input type="number" step="any" id="b" name="b" required value="<?php echo isset($_POST['b']) ? htmlspecialchars($_POST['b']) : ''; ?>" />

      <label for="operation">Operación:</label>
      <select id="operation" name="operation" required>
        <option value="" disabled <?php if(!isset($_POST['operation'])) echo 'selected'; ?>>Selecciona una opción</option>
        <option value="difference" <?php if(isset($_POST['operation']) && $_POST['operation'] === 'difference') echo 'selected'; ?>>Diferencia de cubos (a³ - b³)</option>
        <option value="sum" <?php if(isset($_POST['operation']) && $_POST['operation'] === 'sum') echo 'selected'; ?>>Suma de cubos (a³ + b³)</option>
      </select>

      <button type="submit">Factorizar</button>
    </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $a = filter_input(INPUT_POST, 'a', FILTER_VALIDATE_FLOAT);
    $b = filter_input(INPUT_POST, 'b', FILTER_VALIDATE_FLOAT);
    $operation = filter_input(INPUT_POST, 'operation', FILTER_SANITIZE_STRING);

    if ($a === false || $b === false || !in_array($operation, ['difference', 'sum'])) {
        echo '<div class="result" style="color:#ff6b6b;">Por favor ingresa valores válidos para a, b y operación.</div>';
    } else {
      
        // a³ - b³ = (a - b)(a² + ab + b²)
        // a³ + b³ = (a + b)(a² - ab + b²)

        $a2 = round(pow($a, 2), 4);
        $b2 = round(pow($b, 2), 4);
        $ab = round($a * $b, 4);

        function fmt($num) {
            return rtrim(rtrim(number_format($num, 4, '.', ''), '0'), '.');
        }

        $a_fmt = fmt($a);
        $b_fmt = fmt($b);
        $a2_fmt = fmt($a2);
        $b2_fmt = fmt($b2);
        $ab_fmt = fmt($ab);

        if ($operation === 'difference') {
            // (a - b)(a² + ab + b²)
            $factored = "($a_fmt - $b_fmt)($a2_fmt + $a_fmt"."$b_fmt + $b2_fmt)";
            $explanation = "Fórmula usado: a³ - b³ = (a - b)(a² + ab + b²)";
        } else {
            // (a + b)(a² - ab + b²)
            $factored = "($a_fmt + $b_fmt)($a2_fmt - $a_fmt"."$b_fmt + $b2_fmt)";
            $explanation = "Fórmula usado: a³ + b³ = (a + b)(a² - ab + b²)";
        }

        $original = "$a_fmt<sup>3</sup> " . ($operation === 'difference' ? '-' : '+') . " $b_fmt<sup>3</sup>";

        echo "<div class='result'>";
        echo "<p>Expresión original: <strong>$original</strong></p>";
        echo "<p>Resultado factorizado:</p>";
        echo "<p><strong>$factored</strong></p>";
        echo "<p style='font-size:14px; margin-top:12px; color:#bbb;'>$explanation</p>";
        echo "</div>";
    }
}
?>
  </div>
</body>
</html>

