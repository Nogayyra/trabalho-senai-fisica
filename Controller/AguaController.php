<?php

declare(strict_types=1);

class AguaController
{
    public const PH_MIN = 6.0;
    public const PH_MAX = 9.5;
    public const TURBIDEZ_MAX = 5.0;
    public const CLORO_MIN = 0.2;
    public const CLORO_MAX = 5.0;
    public const DUREZA_MAX = 500.0;
    public const COR_MAX = 15.0;
    public const TEMP_IDEAL_MAX = 25.0;

    public function validarNumero(mixed $valor, string $nome): ?string
    {
        if ($valor === null || $valor === '') {
            return "O campo {$nome} não foi preenchido.";
        }
        if (!is_numeric($valor)) {
            return "O campo {$nome} precisa ser um número.";
        }
        $numero = (float) $valor;
        if (is_nan($numero) || is_infinite($numero)) {
            return "O campo {$nome} possui um valor inválido.";
        }
        return null;
    }

    public function classificarPh(float $ph): array
    {
        if ($ph < 0 || $ph > 14) {
            return $this->resultado('ph', $ph, 'inválido', 'Valor impossível: o pH só existe entre 0 e 14.');
        }
        if ($ph < self::PH_MIN) {
            return $this->resultado('ph', $ph, 'fora do padrão', 'pH abaixo de 6,0: água ácida, fora do padrão de potabilidade.');
        }
        if ($ph > self::PH_MAX) {
            return $this->resultado('ph', $ph, 'fora do padrão', 'pH acima de 9,5: água alcalina, fora do padrão de potabilidade.');
        }
        return $this->resultado('ph', $ph, 'dentro do padrão', 'pH entre 6,0 e 9,5, conforme a Portaria GM/MS nº 888/2021.');
    }

    public function classificarTurbidez(float $turbidez): array
    {
        if ($turbidez < 0) {
            return $this->resultado('turbidez', $turbidez, 'inválido', 'Valor impossível: turbidez não pode ser negativa.');
        }
        if ($turbidez > self::TURBIDEZ_MAX) {
            return $this->resultado('turbidez', $turbidez, 'fora do padrão', 'Turbidez acima de 5,0 uT, fora do padrão de potabilidade.');
        }
        return $this->resultado('turbidez', $turbidez, 'dentro do padrão', 'Turbidez dentro do limite de 5,0 uT.');
    }

    public function classificarCloro(float $cloro): array
    {
        if ($cloro < 0) {
            return $this->resultado('cloro', $cloro, 'inválido', 'Valor impossível: cloro residual não pode ser negativo.');
        }
        if ($cloro < self::CLORO_MIN) {
            return $this->resultado('cloro', $cloro, 'fora do padrão', 'Cloro residual abaixo de 0,2 mg/L: desinfecção insuficiente.');
        }
        if ($cloro > self::CLORO_MAX) {
            return $this->resultado('cloro', $cloro, 'fora do padrão', 'Cloro residual acima de 5,0 mg/L: excesso de cloro.');
        }
        return $this->resultado('cloro', $cloro, 'dentro do padrão', 'Cloro residual entre 0,2 e 5,0 mg/L.');
    }

    public function classificarDureza(float $dureza): array
    {
        if ($dureza < 0) {
            return $this->resultado('dureza', $dureza, 'inválido', 'Valor impossível: dureza não pode ser negativa.');
        }
        if ($dureza > self::DUREZA_MAX) {
            return $this->resultado('dureza', $dureza, 'fora do padrão', 'Dureza acima de 500 mg/L em CaCO3, fora do padrão.');
        }
        return $this->resultado('dureza', $dureza, 'dentro do padrão', 'Dureza dentro do limite de 500 mg/L em CaCO3.');
    }

    public function classificarCor(float $cor): array
    {
        if ($cor < 0) {
            return $this->resultado('cor', $cor, 'inválido', 'Valor impossível: cor aparente não pode ser negativa.');
        }
        if ($cor > self::COR_MAX) {
            return $this->resultado('cor', $cor, 'fora do padrão', 'Cor aparente acima de 15 uH, fora do padrão.');
        }
        return $this->resultado('cor', $cor, 'dentro do padrão', 'Cor aparente dentro do limite de 15 uH.');
    }

    public function classificarTemperatura(float $temperatura): array
    {
        if ($temperatura < -5 || $temperatura > 60) {
            return $this->resultado('temperatura', $temperatura, 'inválido', 'Valor impossível para água em condição ambiente de análise.');
        }
        if ($temperatura > self::TEMP_IDEAL_MAX) {
            return $this->resultado('temperatura', $temperatura, 'informativo', 'Acima de 25 °C: apenas informativo, pode favorecer microrganismos, mas não reprova.');
        }
        return $this->resultado('temperatura', $temperatura, 'informativo', 'Temperatura em faixa operacional adequada (informativo).');
    }

    public function analisarAmostra(array $dados): array
    {
        $obrigatorios = ['ph', 'turbidez', 'cloro', 'dureza', 'cor', 'temperatura'];
        $erros = [];
        foreach ($obrigatorios as $campo) {
            $erro = $this->validarNumero($dados[$campo] ?? null, $campo);
            if ($erro !== null) {
                $erros[] = $erro;
            }
        }
        if ($erros !== []) {
            return ['valida' => false, 'erros' => $erros];
        }

        $parametros = [
            $this->classificarPh((float) $dados['ph']),
            $this->classificarTurbidez((float) $dados['turbidez']),
            $this->classificarCloro((float) $dados['cloro']),
            $this->classificarDureza((float) $dados['dureza']),
            $this->classificarCor((float) $dados['cor']),
            $this->classificarTemperatura((float) $dados['temperatura']),
        ];

        return [
            'valida' => true,
            'parametros' => $parametros,
            'parecer' => $this->parecerGeral($parametros),
        ];
    }

    public function parecerGeral(array $parametros): array
    {
        $reprovados = [];
        foreach ($parametros as $p) {
            if ($p['status'] === 'fora do padrão' || $p['status'] === 'inválido') {
                $reprovados[] = $p['parametro'];
            }
        }
        if ($reprovados === []) {
            return [
                'situacao' => 'POTÁVEL',
                'mensagem' => 'Todos os parâmetros estão dentro do padrão da Portaria GM/MS nº 888/2021.',
            ];
        }
        return [
            'situacao' => 'NÃO POTÁVEL',
            'mensagem' => 'Parâmetros fora do padrão: ' . implode(', ', $reprovados) . '. A amostra não deve ser consumida sem tratamento.',
        ];
    }

    private function resultado(string $parametro, float $valor, string $status, string $mensagem): array
    {
        return [
            'parametro' => $parametro,
            'valor' => $valor,
            'status' => $status,
            'mensagem' => $mensagem,
        ];
    }
}
