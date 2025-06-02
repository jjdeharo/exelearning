<?php

namespace App\Tests\Controller;

use App\Controller\net\exelearning\Controller\ErrorController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorControllerTest extends KernelTestCase
{
    private $controller;
    private $twig;

    protected function setUp(): void
    {
        self::bootKernel();
        
        // Obtén el contenedor de servicios
        $container = static::getContainer();
        
        // Obtén el servicio Twig
        $this->twig = $container->get('twig');
        
        // Crea una instancia del controlador
        $this->controller = new ErrorController();
        // Establece el contenedor en el controlador
        $controllerReflection = new \ReflectionClass($this->controller);
        $containerProperty = $controllerReflection->getProperty('container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue($this->controller, $container);
    }

    public function testShowRendersErrorTemplate()
    {
        // Crea un mock para FlattenException
        $exception = $this->createMock(FlattenException::class);
        $exception->method('getStatusCode')->willReturn(500);
        $exception->method('getMessage')->willReturn('Test error message');

        // Crea un Request
        $request = new Request();

        // Captura la renderización
        $response = $this->controller->show($request, $exception);

        // Verifica que la respuesta sea una instancia de Response
        $this->assertInstanceOf(Response::class, $response);
        
        // Verifica que el código de estado sea correcto
        $this->assertEquals(500, $response->getStatusCode());
        
        // Verifica que el contenido no esté vacío
        $this->assertNotEmpty($response->getContent());
    }

    public function testShowHandlesDifferentErrorCodes()
    {
        $errorCodes = [400, 403, 404, 500];
        
        foreach ($errorCodes as $code) {
            // Crea un mock para FlattenException con diferentes códigos
            $exception = $this->createMock(FlattenException::class);
            $exception->method('getStatusCode')->willReturn($code);
            $exception->method('getMessage')->willReturn("Error $code message");

            // Crea un Request
            $request = new Request();

            // Obtén la respuesta
            $response = $this->controller->show($request, $exception);

            // Verifica que el código de estado sea correcto
            $this->assertEquals($code, $response->getStatusCode(), "El código de estado debería ser $code");
        }
    }

    public function testErrorTemplateReceivesCorrectParameters()
    {
        // Crea un mock para el entorno Twig
        $twigEnvironment = $this->createMock(\Twig\Environment::class);
        
        // Reemplaza el servicio Twig en el controlador
        $controllerReflection = new \ReflectionClass($this->controller);
        $twigProperty = $controllerReflection->getProperty('twig');
        $twigProperty->setAccessible(true);
        $twigProperty->setValue($this->controller, $twigEnvironment);
        
        // Configura expectativas para la llamada a render
        $twigEnvironment->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('security/error.html.twig'),
                $this->callback(function ($parameters) {
                    // Verifica que los parámetros esperados estén presentes
                    return isset($parameters['status_code']) &&
                           isset($parameters['status_text']) &&
                           isset($parameters['error']) &&
                           $parameters['status_code'] === 404 &&
                           $parameters['error'] === 'Page not found';
                })
            )
            ->willReturn('rendered template');
        
        // Crea un mock para FlattenException
        $exception = $this->createMock(FlattenException::class);
        $exception->method('getStatusCode')->willReturn(404);
        $exception->method('getMessage')->willReturn('Page not found');

        // Crea un Request
        $request = new Request();

        // Ejecuta el método show
        $this->controller->show($request, $exception);
    }
    
    public function testErrorTemplateHandlesEmptyErrorMessage()
    {
        // Crea un mock para FlattenException con mensaje vacío
        $exception = $this->createMock(FlattenException::class);
        $exception->method('getStatusCode')->willReturn(500);
        $exception->method('getMessage')->willReturn('');

        // Crea un Request
        $request = new Request();

        // Obtén la respuesta
        $response = $this->controller->show($request, $exception);

        // Verifica que aún así devuelva una respuesta válida
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }
}