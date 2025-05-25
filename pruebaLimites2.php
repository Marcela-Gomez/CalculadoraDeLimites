<?php
$formula = "(pow(x,2)-9)/(x-3)";
$tendencia = 3;
$formulaEvaluada = str_replace('x', $tendencia, $formula);
echo "Expresión evaluada: $formulaEvaluada<br>";

try {
    //Evalua si es continua
    $formulaANumero = eval("return $formulaEvaluada;");
    echo "Resultado: $formulaANumero<br>";
    echo "La función es continua en x = $tendencia.<br>";
} catch (DivisionByZeroError $e) {
    echo "La función no es continua en x = $tendencia.<br>";
    $formulaSeparada = explode("/", $formula);
    //separa el denominador y numerador
    $numerador = $formulaSeparada[0];
    $denominador = $formulaSeparada[1];
    //expresion regular para evaluar diferencias de cuadrados
    $regexCuadrados = '/pow\(\s*x\s*,\s*2\s*\)\s*-\s*(\d+)/';
    //evalua si es una diferencia de cuadrados
    if (preg_match($regexCuadrados, $denominador, $coincidencias)) {
        //obtiene el numero final de la diferencia de cuadrados
        $numero = sqrt($coincidencias[1]);
        //crea la nueva expresion
        $denominador = "(x+".$numero.").(x-".$numero.")";
        echo $denominador;
        $denominador = explode(".",$denominador);
        //evalua cual factor es igual al de arriba y lo simplifica
        try{
            if($numerador == $denominador[0]){
                $formula = "1/(x-".$numero.")";
                echo "La formula redifinida es: $formula.<br>";
                //evalua de nuevo la formula
                $denominador = explode("/",$formula);
                $formulaEvaluada = str_replace('x', $tendencia, $denominador[0]);
                $formulaANumero = eval("return $formulaEvaluada;");
                //redefine la formula
                $formulaANumero = "1/$denominador";
                echo "La formula evaluada es: $formulaANumero.<br>";
            }elseif($numerador == $denominador[1]){
                $formula = "1/(x+".$numero.")";
                echo "La formula redifinida es: $formula.<br>";
                $denominador = explode("/",$formula);
                $formulaEvaluada = str_replace('x', $tendencia, $denominador[1]);
                $denominador = eval("return $formulaEvaluada;");    
                $formulaANumero = "1/$denominador";
                echo "La formula evaluada es: $formulaANumero.<br>";
            }else{
                echo "No se puede factorizar por lo tanto la funcion es discontinua inevitable.";
            }
        }catch (DivisionByZeroError $e){
            $formula = "1/(x-".$numero.")";
            echo "La formula redifinida es: $formula.<br>";
            $denominador = explode("/",$formula);
            $formulaEvaluada = str_replace('x', $tendencia, $denominador[1]);
            $denominador = eval("return $formulaEvaluada;");    
            $formulaANumero = "1/$denominador";
            echo "La formula evaluada es: $formulaANumero.<br>";
            echo "No se puede factorizar por lo tanto la funcion es discontinua inevitable.";
        }
    }elseif(preg_match($regexCuadrados, $numerador, $coincidencias)){
        //obtiene el numero final de la diferencia de cuadrados
        $numero = sqrt($coincidencias[1]);
        //crea la nueva expresion
        $numerador = "(x+".$numero.").(x-".$numero.")";
        echo $numerador;
        $numerador = explode(".",$numerador);
        //evalua cual factor es igual al de arriba y lo simplifica
        if($denominador == $numerador[0]){
            $formula = "(x+".$numero.")";
            echo "La formula redifinida es: $formula.<br>";
            //evalua de nuevo la formula
            $formulaEvaluada = str_replace('x', $tendencia, $formula);
            $formulaANumero = eval("return $formulaEvaluada;");
            //redefine la formula
            echo "La formula evaluada es: $formulaANumero.<br>";
        }elseif($denominador == $numerador[1]){
            $formula = "(x-".$numero.")";
            echo "La formula redifinida es: $formula.<br>";
            //evalua de nuevo la formula
            $formulaEvaluada = str_replace('x', $tendencia, $formula);
            $formulaANumero = eval("return $formulaEvaluada;");
            //redefine la formula
            echo "La formula evaluada es: $formulaANumero.<br>";
        }else{
            echo "No se puede factorizar por lo tanto la funcion es discontinua inevitable.";
        }
    }elseif(preg_match($regexCuadrados, $numerador, $coincidencias)&&preg_match($regexCuadrados, $denominador, $coincidencias)){
        
    } else {
        echo "No se pudo factorizar la expresión.";
    }
    
} catch (Throwable $e) {
    echo "Error general: " . $e->getMessage();
}
/* 
Falta evaluar los otros casos de factoreo y en la diferencia de cuadrados falta evaluar el numerador ademas de cuando 
hay que factorizar denominador y numerador
*/
?>
