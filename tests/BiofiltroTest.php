<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/BiofiltroController.php';

class BiofiltroTest extends TestCase
{
    private BiofiltroController $bio;

    protected function setUp(): void
    {
        $this->bio = new BiofiltroController();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function calcula_eficiencia_com_valores_conhecidos(): void
    {

        $this->assertEqualsWithDelta(80.0, $this->bio->eficiencia(10.0, 2.0), 0.0001);

        $this->assertEqualsWithDelta(0.0, $this->bio->eficiencia(5.0, 5.0), 0.0001);

        $this->assertEqualsWithDelta(75.0, $this->bio->eficiencia(4.0, 1.0), 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function retorna_null_quando_valor_inicial_e_zero(): void
    {
        $this->assertNull($this->bio->eficiencia(0.0, 0.0));
        $this->assertSame('não calculável (valor inicial zero)', $this->bio->classificarEficiencia(null));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function lanca_erro_para_valores_negativos(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bio->eficiencia(-1.0, 2.0);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function classifica_faixas_de_eficiencia(): void
    {
        $this->assertSame('houve aumento após o biofiltro', $this->bio->classificarEficiencia(-5.0));
        $this->assertSame('baixa remoção', $this->bio->classificarEficiencia(10.0));
        $this->assertSame('remoção moderada', $this->bio->classificarEficiencia(40.0));
        $this->assertSame('boa remoção', $this->bio->classificarEficiencia(80.0));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function compara_todos_os_parametros_antes_depois(): void
    {
        $resultado = $this->bio->comparar(
            ['turbidez' => 10, 'cor' => 20, 'cloro' => 2, 'dureza' => 300, 'ph' => 7.0],
            ['turbidez' => 2, 'cor' => 5, 'cloro' => 1, 'dureza' => 270, 'ph' => 7.0]
        );

        $this->assertTrue($resultado['valido']);
        $this->assertCount(5, $resultado['comparacao']);
        $this->assertEqualsWithDelta(80.0, $resultado['comparacao'][0]['eficiencia'], 0.0001);

        $ph = array_values(array_filter($resultado['comparacao'], fn ($l) => $l['parametro'] === 'ph'))[0];
        $this->assertNotNull($ph['alerta']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_campos_ausentes_e_negativos(): void
    {
        $resultado = $this->bio->comparar(['turbidez' => 1], []);
        $this->assertFalse($resultado['valido']);
        $this->assertNotEmpty($resultado['erros']);

        $resultado = $this->bio->comparar(
            ['turbidez' => -1, 'cor' => 1, 'cloro' => 1, 'dureza' => 1, 'ph' => 7],
            ['turbidez' => 1, 'cor' => 1, 'cloro' => 1, 'dureza' => 1, 'ph' => 7]
        );
        $this->assertFalse($resultado['valido']);
    }
}
