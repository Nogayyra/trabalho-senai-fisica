<?php

declare(strict_types=1);

class BiofiltroController
{

    public function eficiencia(float $antes, float $depois): ?float
    {
        if ($antes < 0 || $depois < 0) {
            throw new InvalidArgumentException('Valores negativos são impossíveis para parâmetros da água.');
        }
        if ($antes == 0.0) {
            return null;
        }
        return (($antes - $depois) / $antes) * 100;
    }

    public function classificarEficiencia(?float $eficiencia): string
    {
        if ($eficiencia === null) {
            return 'não calculável (valor inicial zero)';
        }
        if ($eficiencia < 0) {
            return 'houve aumento após o biofiltro';
        }
        if ($eficiencia < 20) {
            return 'baixa remoção';
        }
        if ($eficiencia < 60) {
            return 'remoção moderada';
        }
        return 'boa remoção';
    }

    public function comparar(array $antes, array $depois): array
    {
        $chaves = ['turbidez', 'cor', 'cloro', 'dureza', 'ph'];
        $erros = [];
        foreach ($chaves as $chave) {
            foreach (['antes' => $antes, 'depois' => $depois] as $etapa => $conjunto) {
                if (!isset($conjunto[$chave]) || $conjunto[$chave] === '') {
                    $erros[] = "O campo {$chave} ({$etapa}) não foi preenchido.";
                } elseif (!is_numeric($conjunto[$chave])) {
                    $erros[] = "O campo {$chave} ({$etapa}) precisa ser um número.";
                } elseif ((float) $conjunto[$chave] < 0) {
                    $erros[] = "O campo {$chave} ({$etapa}) possui valor negativo impossível.";
                }
            }
        }
        if ($erros !== []) {
            return ['valido' => false, 'erros' => $erros];
        }

        $linhas = [];
        foreach ($chaves as $chave) {
            $valorAntes = (float) $antes[$chave];
            $valorDepois = (float) $depois[$chave];
            $ef = $this->eficiencia($valorAntes, $valorDepois);
            $linhas[] = [
                'parametro' => $chave,
                'antes' => $valorAntes,
                'depois' => $valorDepois,
                'eficiencia' => $ef,
                'avaliacao' => $this->classificarEficiencia($ef),
                'alerta' => $chave === 'ph'
                    ? 'Atenção: pH tem faixa ideal (6,0–9,5); redução nem sempre é melhoria.'
                    : null,
            ];
        }

        return ['valido' => true, 'comparacao' => $linhas];
    }
}
