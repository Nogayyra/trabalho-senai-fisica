<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/AguaController.php';

class AguaTest extends TestCase
{
    private AguaController $agua;

    protected function setUp(): void
    {
        $this->agua = new AguaController();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function classifica_parametros_dentro_do_padrao(): void
    {
        $this->assertSame('dentro do padrão', $this->agua->classificarPh(7.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarTurbidez(2.5)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarCloro(1.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarDureza(120.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarCor(5.0)['status']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function aceita_valores_exatamente_nos_limites(): void
    {

        $this->assertSame('dentro do padrão', $this->agua->classificarPh(6.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarPh(9.5)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarTurbidez(5.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarCloro(0.2)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarCloro(5.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarDureza(500.0)['status']);
        $this->assertSame('dentro do padrão', $this->agua->classificarCor(15.0)['status']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function reprova_valores_fora_do_padrao(): void
    {
        $this->assertSame('fora do padrão', $this->agua->classificarPh(5.9)['status']);
        $this->assertSame('fora do padrão', $this->agua->classificarPh(9.6)['status']);
        $this->assertSame('fora do padrão', $this->agua->classificarTurbidez(5.1)['status']);
        $this->assertSame('fora do padrão', $this->agua->classificarCloro(0.1)['status']);
        $this->assertSame('fora do padrão', $this->agua->classificarCloro(5.1)['status']);
        $this->assertSame('fora do padrão', $this->agua->classificarDureza(501.0)['status']);
        $this->assertSame('fora do padrão', $this->agua->classificarCor(16.0)['status']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_valores_impossiveis(): void
    {
        $this->assertSame('inválido', $this->agua->classificarPh(15.0)['status']);
        $this->assertSame('inválido', $this->agua->classificarPh(-1.0)['status']);
        $this->assertSame('inválido', $this->agua->classificarTurbidez(-0.5)['status']);
        $this->assertSame('inválido', $this->agua->classificarCloro(-1.0)['status']);
        $this->assertSame('inválido', $this->agua->classificarDureza(-10.0)['status']);
        $this->assertSame('inválido', $this->agua->classificarCor(-2.0)['status']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function temperatura_nunca_reprova_a_amostra(): void
    {
        $this->assertSame('informativo', $this->agua->classificarTemperatura(22.0)['status']);
        $this->assertSame('informativo', $this->agua->classificarTemperatura(30.0)['status']);

        $analise = $this->agua->analisarAmostra([
            'ph' => 7.0, 'turbidez' => 1.0, 'cloro' => 1.0,
            'dureza' => 100.0, 'cor' => 5.0, 'temperatura' => 35.0,
        ]);
        $this->assertTrue($analise['valida']);
        $this->assertSame('POTÁVEL', $analise['parecer']['situacao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function parecer_final_potavel_quando_tudo_conforme(): void
    {
        $analise = $this->agua->analisarAmostra([
            'ph' => 7.2, 'turbidez' => 3.0, 'cloro' => 0.5,
            'dureza' => 200.0, 'cor' => 10.0, 'temperatura' => 24.0,
        ]);
        $this->assertTrue($analise['valida']);
        $this->assertCount(6, $analise['parametros']);
        $this->assertSame('POTÁVEL', $analise['parecer']['situacao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function parecer_final_nao_potavel_quando_algum_fora(): void
    {
        $analise = $this->agua->analisarAmostra([
            'ph' => 7.2, 'turbidez' => 8.0, 'cloro' => 0.5,
            'dureza' => 200.0, 'cor' => 10.0, 'temperatura' => 24.0,
        ]);
        $this->assertTrue($analise['valida']);
        $this->assertSame('NÃO POTÁVEL', $analise['parecer']['situacao']);
        $this->assertStringContainsString('turbidez', $analise['parecer']['mensagem']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_campos_ausentes_e_nao_numericos(): void
    {
        $analise = $this->agua->analisarAmostra(['ph' => 7.0]);
        $this->assertFalse($analise['valida']);
        $this->assertNotEmpty($analise['erros']);

        $analise = $this->agua->analisarAmostra([
            'ph' => 'abc', 'turbidez' => 1.0, 'cloro' => 1.0,
            'dureza' => 100.0, 'cor' => 5.0, 'temperatura' => 24.0,
        ]);
        $this->assertFalse($analise['valida']);
    }
}
