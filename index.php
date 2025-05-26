<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Funciones Matemáticas f(x)</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        header {
            padding: 2rem 1rem 1rem;
            text-align: center;
            max-width: 600px;
        }
        header h1 {
            font-size: 3rem;
            margin-bottom: 0.2rem;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.4);
        }
        header p {
            font-size: 1.2rem;
            font-weight: 500;
            margin-top: 0;
            color: #ececec;
        }
        form {
            background: rgba(255 255 255 / 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-radius: 10px;
            margin: 1rem auto 2rem;
            max-width: 600px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        input[type="text"] {
            flex-grow: 1;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            font-weight: 500;
            outline: none;
            box-shadow: inset 2px 2px 6px rgba(0,0,0,0.3);
            transition: box-shadow 0.3s ease;
        }
        input[type="text"]:focus {
            box-shadow: inset 2px 2px 6px #a188f0;
        }
        button {
            background: #5a4de8;
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 8px 16px #4a3cc8;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        button:hover {
            background: #816bf8;
            box-shadow: 0 12px 24px #6a59d8;
        }
        main {
            flex-grow: 1;
            width: 90%;
            max-width: 900px;
            background: rgba(255 255 255 / 0.05);
            border-radius: 12px;
            padding: 1rem 1.5rem 2rem;
            box-shadow: 0 8px 12px rgba(0,0,0,0.3);
            margin-bottom: 2rem;
        }
        canvas {
            width: 100% !important;
            height: 400px !important;
            border-radius: 14px;
            background: #2e2d50;
            box-shadow: inset 0 0 20px #7e7ce8;
        }
        footer {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #bbb;
        }
        .error {
            color: #ff6b6b;
            font-weight: 700;
            margin-top: 0.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Funciones Matemáticas f(x)</h1>
        <p>Ingresa una función en términos de <code>x</code> y visualízala</p>
    </header>
    <?php
    // Helper function to safely evaluate math expression with given x
    function evaluate_function($func, $x) {
        // Allowed characters and functions (whitelist)
        $allowed_funcs = [
            'sin', 'cos', 'tan', 'asin', 'acos', 'atan',
            'sqrt', 'log', 'log10', 'exp', 'abs', 'pow'
        ];
        // Replace ^ with pow for PHP
        $func = str_replace('^', '**', $func);
        
        // Sanitize: allow only numbers, operators, x, parentheses, spaces and allowed function names
        // We allow digits, operators (+-*/^), x, period, spaces, parentheses
        $pattern = '/[^0-9+\-.*\/^() xa-z]/i';
        if (preg_match($pattern, $func)) {
            return [false, "La función contiene caracteres no permitidos."];
        }
        
        // Replace x (case insensitive) with actual number
        $func_eval = preg_replace('/\bx\b/i', "($x)", $func);
        
        // Replace function names with PHP equivalents
        foreach ($allowed_funcs as $f) {
            // Replace word boundaries to avoid accidental partial matches
            $func_eval = preg_replace('/\b' . $f . '\b/', ''.$f, $func_eval);
        }
        
        // Replace ** by pow(x,y) if used, since PHP 7 supports **
        // We'll leave as is since PHP 7+ supports **
        
        // To prevent arbitrary code execution, we only allow these functions and operators
        // Use eval with try/catch, but eval errors won't be caught. Instead, suppress errors and check result.
        set_error_handler(function() {}, E_ALL);
        try {
            $result = eval('return ' . $func_eval . ';');
            restore_error_handler();
            if ($result === false || $result === null) {
                return [false, "Error calculando la función. Revisa la sintaxis."];
            }
            if (is_nan($result) || is_infinite($result)) {
                return [false, "El resultado es indefinido o infinito."];
            }
            return [true, $result];
        } catch (ParseError $e) {
            restore_error_handler();
            return [false, "Error de sintaxis en la función."];
        } catch (Throwable $e) {
            restore_error_handler();
            return [false, "Error inesperado al evaluar la función."];
        }
    }
    
    $input_func = "";
    $error = "";
    $points_json = "[]";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['funcion'])) {
        $input_func = trim($_POST['funcion']);
        if ($input_func === '') {
            $error = "Por favor ingresa una función.";
        } else {
            $points = [];
            // Generate values from -10 to 10 (step 0.5)
            for ($x = -10; $x <= 10; $x += 0.5) {
                list($ok, $val) = evaluate_function($input_func, $x);
                if (!$ok) {
                    $error = $val;
                    break;
                }
                $points[] = ['x' => $x, 'y' => round($val, 4)];
            }
            if (!$error) {
                // Prepare JSON data for JS plotting
                $points_json = json_encode($points);
            }
        }
    }
    ?>
    <form method="post" novalidate autocomplete="off" spellcheck="false">
        <input 
            type="text" 
            name="funcion" 
            placeholder="Ejemplo: sin(x) + 0.5*x^2 - 2" 
            value="<?php echo htmlspecialchars($input_func); ?>" 
            autofocus 
            aria-label="Función f(x)"
            required
        />
        <button type="submit" title="Graficar función">Graficar</button>
    </form>
    <?php if ($error): ?>
        <div class="error" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <main>
        <canvas id="grafica"></canvas>
    </main>
    <footer>
        &copy; 2024 Página de funciones matemáticas - Desarrollo moderno y vistoso
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const points = <?php echo $points_json; ?>;
        const ctx = document.getElementById('grafica').getContext('2d');
        let chart = null;
        if(points.length > 0) {
            const labels = points.map(p => p.x);
            const data = points.map(p => p.y);
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'f(x) = <?php echo addslashes($input_func); ?>',
                        data: data,
                        fill: false,
                        borderColor: '#8e7cc3',
                        backgroundColor: '#cdb4db',
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'x',
                                color: 'white',
                                font: { size: 16, weight: 'bold' }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.2)'
                            },
                            ticks: {
                                color: 'white'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'f(x)',
                                color: 'white',
                                font: { size: 16, weight: 'bold' }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.2)'
                            },
                            ticks: {
                                color: 'white'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white',
                                font: { size: 14, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#7e74b8',
                            titleFont: { size: 16, weight: 'bold' },
                            bodyFont: { size: 14 }
                        }
                    }
                }
            });
        } else {
            // Clear canvas if no points to show
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
            ctx.font = '18px Poppins, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Ingrese una función y presione Graficar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        }
    </script>
</body>
</html>