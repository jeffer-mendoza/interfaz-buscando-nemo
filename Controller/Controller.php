<?php

namespace Anunciar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Controller
{

    /**
     * Muestra la página inicial del sitio
     *
     * @param Application $app
     * @return type
     */
    public function indexAction(Application $app)
    {
        $response = $app['twig']->render('index.html.twig', array('class' => 'homepage'));

        return new Response($response, 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

    /**
     * Muestra la página inicial del sitio
     *
     * @param Application $app
     * @return type
     */
    public function busquedaAction(Application $app)
    {
        $response = $app['twig']->render('busqueda.html.twig', array('class' => 'no-sidebar'));

        return new Response($response, 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

    /**
     * Muestra la página nosotros
     *
     * @param Application $app
     * @return type
     */
    public function buscarAction(Application $app)
    {

        $request = $app['request'];
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('file');
            $algoritmo = $request->get('algoritmo');
            $file->move('../lib/', 'input.txt');
            exec("rm ../lib/output.txt");
            exec("java -jar ../lib/search-nemo.jar " . $algoritmo . " ../lib/input.txt >> ../lib/output.txt");

            return $app->redirect($app['url_generator']->generate('resultado'));
        } else {
            $response = $app['twig']->render('index.html.twig', array('class' => 'homepage'));
        }
        return new Response($response, 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

    /**
     * Muestra la página nosotros
     *
     * @param Application $app
     * @return type
     */
    public function resultadoAction(Application $app)
    {
        $fp = fopen("../lib/output.txt", "r");
        $i = 0;
        $array = array();
        while (!feof($fp)) {
            $array[$i] = fgets($fp);
            $i++;
        }
        $tablero = '<div><table class="table table-bordered">';
        $tablero .= '<tr><td>Algoritmo</td><td>'.$array[0].'</td></tr>';
        $tablero .= '<tr><td>Ruta</td><td>'.$array[2].'</td></tr>';
        $tablero .= '<tr><td>Costos</td><td>'.$array[3].'</td></tr>';
        $tablero .= '<tr><td>Nodos Expandidos</td><td>'.$array[4].'</td></tr>';
        $tablero .= '<tr><td>Nodos Creados</td><td>'.$array[5].'</td></tr>';
        $tablero .= '<tr><td>Costo Total</td><td>'.$array[6].'</td></tr>';
        $tablero .= '<tr><td>Factor de Ramificación</td><td>'.$array[7].'</td></tr>';
        $tablero .= '<tr><td>Nivel</td><td>'.$array[8].'</td></tr>';
        $tablero .= '<tr><td>Tiempo de ejecución</td><td>'.$array[1].'</td></tr>';
        $tablero .= '</table></div>';
        fclose($fp);
        $n = count($array) - 8;
        $tamanoMatriz = intval($array[9]) + 1;
        $j = 10;
        for ($i = 1;$i < $n; $i++) {
            if ($i % $tamanoMatriz == 1) {
                $tablero .= '<div class="col-lg-12"><table width="400" class="table-bordered">';
                $tablero .= '<div>'.$array[$j].'</div>';
            } else {
                $tablero .= $this->crearFilas(explode(" ", $array[$j],$tamanoMatriz - 1), $app['request']);
                if ($i % $tamanoMatriz == 0) {
                    $tablero .= '</table></div><div class="col-lg-1"><span class="icon fa-arrow-down"></span></div>';
                }
            }
            $j++;
        }
        $response = $app['twig']->render('resultado.html.twig', array('class' => 'no-sidebar', 'tablero' => $tablero));

        return new Response($response, 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

    function crearFilas($columnas, $request)
    {
        $fila = '<tr>';
        $imagenes = array('0.jpg', '1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg', '7.jpg', '8.png');
        $n = count($columnas);
        for ($i = 0; $i < $n; $i++) {
            $columna = $columnas[$i];
            $src = $request->getBasePath() . "/img/" . $imagenes[intval(substr($columna, 0))];
            $fila .= '<td><img width="80" height="80" src="' . $src . '"></td>';
        }
        $fila .= '</tr>';

        return $fila;
    }


}
