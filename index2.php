<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Continuidad y Límites</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            padding-top: 60px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 600px;
        }
        h1 {
            text-align: center;
            background:rgb(167, 188, 251);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 1em;
        }
        button {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .resultado {
            margin-top: 25px;
            padding: 15px;
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Calculadora de Continuidad y Límites</h1>
    <form method="post">
        <label for="formula">Función f(x):</label>
        <input type="text" name="formula" id="formula" placeholder="Ej: (x^2 - 1)/(x - 1)" value="<?= $_POST['formula'] ?? '' ?>" required>

        <label for="tendencia">Punto de interés (x → a):</label>
        <input type="number" name="tendencia" id="tendencia" step="any" value="<?= $_POST['tendencia'] ?? '' ?>" required>

        <button type="submit">Analizar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $formula = $_POST['formula'];
        $tendencia = $_POST['tendencia'];

        // ✅ Normaliza la entrada del usuario
        $formula = str_replace('−', '-', $formula); // reemplaza signos menos raros
        $formula = preg_replace('/\bx\^(\d+)/', 'pow(x,$1)', $formula); // x^3 → pow(x,3)

        // Sustituye la x por el valor de tendencia, solo donde sea una variable (no en funciones tipo 'exp')
        $formulaEvaluada = preg_replace('/\bx\b/', "($tendencia)", $formula);

        echo "<div class='resultado'>";
        echo "Expresión evaluada: <code>$formulaEvaluada</code><br><br>";

        try {
            $formulaANumero = eval("return $formulaEvaluada;");
            echo "Resultado: <strong>$formulaANumero</strong><br>";
            echo "<strong> La función es continua en x = $tendencia.</strong>";
        } catch (DivisionByZeroError $e) {
            echo " <strong>La función no es continua en x = $tendencia.</strong><br>";
            $formulaSeparada = explode("/", $formula);
            $numerador = $formulaSeparada[0] ?? '';
            $denominador = $formulaSeparada[1] ?? '';

            // Intenta detectar y factorizar diferencia de cuadrados
            $regexCuadrados = '/pow\(\s*x\s*,\s*2\s*\)\s*-\s*(\d+)/';
            if (preg_match($regexCuadrados, $denominador, $coincidencias)) {
                $numero = sqrt($coincidencias[1]);
                $denominador = "(x+".$numero.")*(x-".$numero.")";
                $denominadorFact = explode("*", $denominador);

                if (trim($numerador) == trim($denominadorFact[0])) {
                    $formulaRedefinida = "1/(x-".$numero.")";
                } elseif (trim($numerador) == trim($denominadorFact[1])) {
                    $formulaRedefinida = "1/(x+".$numero.")";
                } else {
                    $formulaRedefinida = null;
                }

                if ($formulaRedefinida) {
                    echo "<br>La función tiene una forma indeterminada (0/0), pero puede simplificarse:<br>";
                    echo "Función simplificada: <code>$formulaRedefinida</code><br>";
                    $formulaSimplificada = str_replace('x', "($tendencia)", $formulaRedefinida);
                    $resultado = eval("return $formulaSimplificada;");
                    echo "Límite redefinido: <strong>$resultado</strong><br>";
                } else {
                    echo "No se pudo simplificar automáticamente la expresión.";
                }
            } else {
                echo "No se reconoció un caso de diferencia de cuadrados.";
            }
        } catch (Throwable $e) {
            echo "Error general: " . $e->getMessage();
        }
        echo "</div>";
    }
    ?>
</div>
</body>
</html>
